== CHANGELOG ==

1. Rename class name 'Repository' to 'Alfresco_Repository' in
    - Service/Repository.php
    - Service/Webservice/AlfrescoWebService.php

2. Update path for require_once() in
    - Service/Logger/Logger.php
    - Service/WebService/WebServiceFactory.php
    - Service/ContentData.php
    - Service/Functions.php
    - Service/Node.php
    - Service/Repository.php
    - Service/Session.php
    - Service/SpacesStore.php
    - Service/Store.php

3. In Alfresco_Repository::__construct(), set _port to 80 when not specified

    @@ -46,7 +46,11 @@ class Alfresco_Repository extends BaseObject
            $this->_connectionUrl = $connectionUrl;
            $parts = parse_url($connectionUrl);
            $this->_host = $parts["host"];
    -       $this->_port = $parts["port"];
    +       if (empty($parts["port"])) {
    +           $this->_port = 80;
    +       } else {
    +           $this->_port = $parts["port"];
    +       }

4. Reapply changes from MDL-20876 Fix depreacted split() calls (bed733c)

5. Fix strict standards in Service/WebService/AlfrescoWebService.php
    - AlfrescoWebService::__soapCall() arguments do not match SoapClient::__soapCall()
    - AlfrescoWebService::__doRequest() arguments do not match SoapClient::__soapCall()

6. Apply the changes from MDL-41975 in regard with the timestamp

== Alfresco PHP Library ==

Installation and developer documentation for the Alfresco PHP Library can be found on the Alfresco Wiki.
Get the source from http://code.google.com/p/alfresco-php-sdk/

== Documentation Links ==

Installation Instructions - http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoPHPLibraryInstallationInstructions
MediaWiki Integration Installation Instructions - http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoMediaWikiInstallationInstructions
PHP API Documentation - http://code.google.com/p/alfresco-php-sdk/wiki/AlfrescoPHPAPI