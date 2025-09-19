<?php

namespace Miniorange\Oauth\Helper;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;

class EncryptionHelper
{
    /**
     * Get encryption key with fallback
     * 
     * @return string
     */
    private static function getEncryptionKey()
    {
        // Primary: TYPO3's encryption key
        if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']) && 
            !empty($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'])) {
            return $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
        }
        
        // Fallback: Default key
        return 'keycloak_sso_default_encryption_key_2024';
    }

    /**
     * Encrypt user count value using XOR encryption with key rotation
     * 
     * @param int $count
     * @return string
     */
    public static function encryptUserCount($count)
    {
        if (!is_numeric($count)) {
            return $count; // Return as-is if not numeric
        }
        
        $data = (string)$count;
        $encryptionKey = self::getEncryptionKey();
        
        // XOR encryption with key rotation
        $encrypted = '';
        $keyLength = strlen($encryptionKey);
        
        for ($i = 0; $i < strlen($data); $i++) {
            $encrypted .= chr(ord($data[$i]) ^ ord($encryptionKey[$i % $keyLength]));
        }
        
        // Base64 encoding for database safety
        return base64_encode($encrypted);
    }
    
    /**
     * Decrypt user count value using XOR decryption with key rotation
     * 
     * @param string $encryptedCount
     * @return int
     */
    public static function decryptUserCount($encryptedCount)
    {
        if (empty($encryptedCount) || !is_string($encryptedCount)) {
            return 0;
        }
        
        $encryptionKey = self::getEncryptionKey();
        
        try {
            // Base64 decode first
            $encrypted = base64_decode($encryptedCount);
            
            if ($encrypted === false) {
                return 0;
            }
            
            // XOR decryption with key rotation
            $decrypted = '';
            $keyLength = strlen($encryptionKey);
            
            for ($i = 0; $i < strlen($encrypted); $i++) {
                $decrypted .= chr(ord($encrypted[$i]) ^ ord($encryptionKey[$i % $keyLength]));
            }
            
            if (is_numeric($decrypted)) {
                return (int)$decrypted;
            }
        } catch (\Exception $e) {
            error_log('Failed to decrypt user count: ' . $e->getMessage());
        }
        
        return 0;
    }
    
    /**
     * Check if a value is encrypted
     * 
     * @param mixed $value
     * @return bool
     */
    public static function isEncrypted($value)
    {
        if (!is_string($value)) {
            return false;
        }
        
        // Check if it's base64 encoded and contains encrypted data
        $decoded = base64_decode($value, true);
        return $decoded !== false && strlen($decoded) > 0;
    }
    
    /**
     * Get user count with automatic decryption
     * 
     * @param mixed $value
     * @return int
     */
    public static function getUserCount($value)
    {
        if (is_numeric($value)) {
            // Legacy unencrypted value
            return (int)$value;
        }
        
        if (self::isEncrypted($value)) {
            return self::decryptUserCount($value);
        }
        
        return 0;
    }

    /**
     * Update encrypted user count in database
     * 
     * @param int $count
     * @return bool
     */
    public static function updateEncryptedUserCount($count)
    {
        $encryptedCount = self::encryptUserCount($count);
        $typo3Version = \Miniorange\Oauth\Helper\MoUtilities::getTypo3Version();
        $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable(Constants::TABLE_OIDC);
        
        try {
            if ($typo3Version > 12) {
                $queryBuilder->update(Constants::TABLE_OIDC)
                    ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter(1, \TYPO3\CMS\Core\Database\Connection::PARAM_INT)))
                    ->set(Constants::COUNTUSER, $encryptedCount)
                    ->executeStatement();
            } else {
                $queryBuilder->update(Constants::TABLE_OIDC)
                    ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter(1, \TYPO3\CMS\Core\Database\Connection::PARAM_INT)))
                    ->set(Constants::COUNTUSER, $encryptedCount)
                    ->execute();
            }
            return true;
        } catch (\Exception $e) {
            error_log('Failed to update encrypted user count: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch encrypted user count from database
     * 
     * @return int
     */
    public static function fetchEncryptedUserCount()
    {
        $encryptedCount = \Miniorange\Oauth\Helper\Utilities::fetchFromTable(Constants::COUNTUSER, Constants::TABLE_OIDC);
        
        // Check if encrypted
        if (self::isEncrypted($encryptedCount)) {
            return self::decryptUserCount($encryptedCount);
        }
        
        // Backward compatibility for unencrypted values
        return is_numeric($encryptedCount) ? (int)$encryptedCount : 10;
    }

    /**
     * Decrement encrypted user count
     * 
     * @return int
     */
    public static function decrementEncryptedUserCount()
    {
        $currentCount = self::fetchEncryptedUserCount();
        $newCount = max(0, $currentCount - 1);
        self::updateEncryptedUserCount($newCount);
        return $newCount;
    }

    /**
     * Migrate existing unencrypted user count to encrypted format
     * 
     * @return bool
     */
    public static function migrateUserCountToEncrypted()
    {
        $currentCount = \Miniorange\Oauth\Helper\Utilities::fetchFromTable(Constants::COUNTUSER, Constants::TABLE_OIDC);
        
        // If it's already encrypted, no need to migrate
        if (self::isEncrypted($currentCount)) {
            return true;
        }
        
        // If it's numeric (unencrypted), encrypt it
        if (is_numeric($currentCount)) {
            return self::updateEncryptedUserCount((int)$currentCount);
        }
        
        // If it's empty or invalid, set default value
        return self::updateEncryptedUserCount(10);
    }
}
