This is a Typo3 extension from miniOrange Inc. for Single-Sign-On(SSO) using OAuth/OIDC protocol.
Feel free to point out any bug or issue. 
For any query or enabling premium features contact us through support form in extension itself. 
Also you can email at info@xecurify.com or visit https://www.miniorange.com/contact.

Installation Instructions:

1. Composer Installation:
    Run the below command to install the extension:
        - composer require miniorange/miniorange-oidc

            OR

2. Manual Installation:
        - Unzip the plugin zip into the typo3conf/ext folder, rename the plugin folder to 'oauth' and activate the extension from the Extensions section in Typo3.

3. After installing the extension, apply the database changes, if not applied automatically with command:
```bash
vendor/bin/typo3 extension:setup
```

5. Create the two standard pages as feoidc and response and add the feoidc and response pages, respectively, to them.

6. Once the extension is installed successfully, navigate to the OpenID Connect Client tab of the plugin and fill in all the required fields as below:
    - OAuth/ OpenID Provider Name: {Name of your OAuth/OIDC provider}
    - Application type: OAuth/OpenID Connect
    - Frontend Redirect/Callback Url : {Response Plugin Page URL which you created in earlier steps} (You will need to provide this URL to your
      OAuth/OIDC provider)
    - feoidc page URL: {feoidc Plugin Page URL which you created in earlier steps}
    - Client ID : {You will get it from your OAuth/OIDC provider}
    - Client Secret : {You will get it from your OAuth/OIDC provider}
    - Scope : openid profile email
    - Authorization Endpoint : {You will get this endpoint from your OAuth/OIDC provider}
    - Token Endpoint : {You will get this endpoint from your OAuth/OIDC provider}
    - User Info Endpoint : {You will get this endpoint from your OAuth/OIDC provider}
    - Set client credentials in : Header/Body

7. Provide the redirect/callback URL in your OAuth/OIDC provider application by copying it from Frontend Redirect/Callback Url field in OpenID Connect Client tab.

8. Once you are done with the configurations on both ends (i.e., Typo3 and your OAuth/OIDC provider), click on the Test Configuration button in the OpenID Connect Client tab of the plugin and check if you are able to test it successfully.

9. Navigate to the Attribute Mapping tab and map the Username attribute to the OAuth/OIDC provider attribute using which you want to identify the users in Typo3 (you can find all the attributes received from your OAuth/OIDC provider in the test configuration).

10. Navigate to the Group Mapping tab of the plugin and save the Group Mapping for Frontend Users by selecting the Default Usergroup.

11. Once you have done all the above steps, you are ready to test the SSO. You can use your Feoidc Page URL in order to initiate the SSO.

You can choose the setup guide according to your OAuth/OIDC provider from below link:
    - https://plugins.miniorange.com/typo3-sso-single-sign-on-with-oauth-openid-connect-setup-guides
