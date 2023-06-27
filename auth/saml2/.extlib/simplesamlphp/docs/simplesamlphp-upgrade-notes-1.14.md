Upgrade notes for SimpleSAMLphp 1.14
====================================

The `mcrypt` extension is no longer required by SimpleSAMLphp, so if no signatures or encryption are being used, it
can be skipped. It is still a requirement for `xmlseclibs` though, so for those verifying or creating signed
documents, or using encryption, it is still needed.

The `mbstring` extension is now required starting on SimpleSAMLphp 1.14.12.

PHP session cookies are now set to HTTP-only by default. This relates to the `session.phpsession.httponly`
configuration option.

The default value for the 'trusted.url.domains' option in the config file has been changed from null to an empty array,
making SimpleSAMLphp secure to open redirection attacks by default. Setting it explicitly to null will re-allow
insecure redirections.

The jQuery version in use has been bumped to the latest 1.8.X version.

Service Providers using the eduPersonTargetedID attribute, will get a DOMNodeList object instead of the NameID value. In
order to process the NameID, a SAML2_XML_saml_NameID object can be used:

```
$attributes = $as->getAttributes();
$eptid = $attributes['eduPersonTargetedID'][0]->item(0);
$nameID = new SAML2_XML_saml_NameID($eptid);
```

The following deprecated files, directories and endpoints have been removed:

* `bin/pack.php`
* `docs/pack.txt`
* `docs/simplesamlphp-features.txt`
* `docs/simplesamlphp-reference-sp-hosted.txt`
* `docs/simplesamlphp-subversion.txt`
* `lib/SimpleSAML/Auth/BWC.php` (`SimpleSAML_Auth_BWC`)
* `lib/SimpleSAML/MemcacheStore.php` (`SimpleSAML_MemcacheStore`)
* `lib/SimpleSAML/Metadata/MetaDataStorageHandlerDynamicXML.php` (`SimpleSAML_Metadata_MetaDataStorageHandlerDynamicXML`)
* `modules/aselect/www/linkback.php`
* `modules/core/lib/ModuleDefinition.php` (`sspmod_core_ModuleDefinition`)
* `modules/core/lib/ModuleInstaller.php` (`sspmod_core_ModuleInstaller`)
* `modules/core/www/bwc_resumeauth.php`
* `modules/core/www/idp/resumeauth.php`
* `modules/oauth/lib/OauthSignatureMethodRSASHA1.php` (`sspmod_oauth_OauthSignatureMethodRSASHA1`)
* `modules/oauth/www/accessToken.php`
* `modules/oauth/www/authorize.php`
* `modules/oauth/www/requestToken.php`
* `modules/smartnameattribute/`
* `www/resources/jquery.js`
* `www/resources/jquery-ui.js`
* `www/resources/uitheme/`
* `www/shib13/sp/`
* `www/saml2/idp/idpInitSingleLogoutServiceiFrame.php`
* `www/saml2/idp/SingleLogoutServiceiFrame.php`
* `www/saml2/idp/SingleLogoutServiceiFrameResponse.php`
* `www/saml2/sp/`
* `www/wsfed/`
* `www/example-simple/`
* `www/auth/`

The following deprecated methods and constants have been removed:

* `SimpleSAML_AuthMemCookie::getLoginMethod()`
* `SimpleSAML_Session::DATA_TIMEOUT_LOGOUT`
* `SimpleSAML_Session::expireDataLogout()`
* `SimpleSAML_Session::get_sp_list()`
* `SimpleSAML_Session::getAttribute()`
* `SimpleSAML_Session::getAttributes()`
* `SimpleSAML_Session::getAuthnInstant()`
* `SimpleSAML_Session::getAuthnRequest()`
* `SimpleSAML_Session::getAuthority()`
* `SimpleSAML_Session::getIdP()`
* `SimpleSAML_Session::getInstance()`
* `SimpleSAML_Session::getLogoutState()`
* `SimpleSAML_Session::getNameID()`
* `SimpleSAML_Session::getSessionIndex()`
* `SimpleSAML_Session::getSize()`
* `SimpleSAML_Session::isAuthenticated()`
* `SimpleSAML_Session::remainingTime()`
* `SimpleSAML_Session::setAttribute()`
* `SimpleSAML_Session::setAttributes()`
* `SimpleSAML_Session::setAuthnRequest()`
* `SimpleSAML_Session::setIdP()`
* `SimpleSAML_Session::setLogoutState()`
* `SimpleSAML_Session::setNameID()`
* `SimpleSAML_Session::setSessionDuration()`
* `SimpleSAML_Session::setSessionIndex()`
* `SimpleSAML_Utilities::generateRandomBytesMTrand()`

The following methods have changed their signature. Refer to the code for the updated signatures:

* `SimpleSAML_Auth_Default::initLogin()`
* `SimpleSAML_Metadata_MetaDataStorageHandler::getGenerated()`
* `SimpleSAML_Metadata_MetaDataStorageHandler::getMetaData()`
* `SimpleSAML_Metadata_MetaDataStorageHandler::getMetaDataCurrent()`
* `SimpleSAML_Metadata_MetaDataStorageHandler::getMetaDataCurrentEntityID()`
* `SimpleSAML_Session::doLogout()`
* `SimpleSAML_Session::getAuthState()`
* `SimpleSAML_Session::registerLogoutHandler()`
* `SimpleSAML_Utilities::generateRandomBytes()`
* `SimpleSAML_XML_Shib13_AuthnRequest::createRedirect()`

The following methods and classes have been deprecated. Refer to the code for alternatives:

* `SimpleSAML_Auth_Default`, together with all the `SimpleSAML_Auth_Default.*` keys in the state array.
* `SimpleSAML_Auth_Default::extractPersistentAuthState()`
* `SimpleSAML_Auth_Default::handleUnsolicitedAuth()`
* `SimpleSAML_Auth_Default::initLogin()`
* `SimpleSAML_Auth_Default::initLogout()`
* `SimpleSAML_Auth_Default::initLogoutReturn()`
* `SimpleSAML_Auth_Default::loginCompleted()`
* `SimpleSAML_Auth_Default::logoutCallback()`
* `SimpleSAML_Auth_Default::logoutCompleted()`
* `SimpleSAML_Utilities`
* `SimpleSAML_Utilities::addURLParameter()`
* `SimpleSAML_Utilities::aesDecrypt()`
* `SimpleSAML_Utilities::aesEncrypt()`
* `SimpleSAML_Utilities::arrayize()`
* `SimpleSAML_Utilities::checkCookie()`
* `SimpleSAML_Utilities::checkDateConditions()`
* `SimpleSAML_Utilities::checkURLAllowed()`
* `SimpleSAML_Utilities::createHttpPostRedirectLink()`
* `SimpleSAML_Utilities::createPostRedirectLink()`
* `SimpleSAML_Utilities::debugMessage()`
* `SimpleSAML_Utilities::doRedirect()`
* `SimpleSAML_Utilities::fatalError()`
* `SimpleSAML_Utilities::fetch()`
* `SimpleSAML_Utilities::formatDOMElement()`
* `SimpleSAML_Utilities::formatXMLString()`
* `SimpleSAML_Utilities::generateID()`
* `SimpleSAML_Utilities::generateRandomBytes()`
* `SimpleSAML_Utilities::generateTimestamp()`
* `SimpleSAML_Utilities::getAcceptLanguage()`
* `SimpleSAML_Utilities::getAdminLogoutURL()`
* `SimpleSAML_Utilities::getBaseURL()`
* `SimpleSAML_Utilities::getDefaultEndpoint()`
* `SimpleSAML_Utilities::getDOMChildren()`
* `SimpleSAML_Utilities::getDOMText()`
* `SimpleSAML_Utilities::getFirstPathElement()`
* `SimpleSAML_Utilities::getLastError()`
* `SimpleSAML_Utilities::getSecretSalt()`
* `SimpleSAML_Utilities::getSelfHost()`
* `SimpleSAML_Utilities::getSelfHostWithPath()`
* `SimpleSAML_Utilities::getTempDir()`
* `SimpleSAML_Utilities::initTimezone()`
* `SimpleSAML_Utilities::ipCIDRcheck()`
* `SimpleSAML_Utilities::isAdmin()`
* `SimpleSAML_Utilities::isDOMElementOfType()`
* `SimpleSAML_Utilities::isHTTPS()`
* `SimpleSAML_Utilities::isWindowsOS()`
* `SimpleSAML_Utilities::loadPrivateKey()`
* `SimpleSAML_Utilities::loadPublicKey()`
* `SimpleSAML_Utilities::maskErrors()`
* `SimpleSAML_Utilities::normalizeURL()`
* `SimpleSAML_Utilities::parseAttributes()`
* `SimpleSAML_Utilities::parseDuration()`
* `SimpleSAML_Utilities::parseQueryString()`
* `SimpleSAML_Utilities::parseStateID()`
* `SimpleSAML_Utilities::popErrorMask()`
* `SimpleSAML_Utilities::postRedirect()`
* `SimpleSAML_Utilities::redirect()`
* `SimpleSAML_Utilities::redirectTrustedURL()`
* `SimpleSAML_Utilities::redirectUntrustedURL()`
* `SimpleSAML_Utilities::requireAdmin()`
* `SimpleSAML_Utilities::resolveCert()`
* `SimpleSAML_Utilities::resolvePath()`
* `SimpleSAML_Utilities::resolveURL()`
* `SimpleSAML_Utilities::selfURL()`
* `SimpleSAML_Utilities::selfURLHost()`
* `SimpleSAML_Utilities::selfURLNoQuery()`
* `SimpleSAML_Utilities::setCookie()`
* `SimpleSAML_Utilities::stringToHex()`
* `SimpleSAML_Utilities::transposeArray()`
* `SimpleSAML_Utilities::validateCA()`
* `SimpleSAML_Utilities::validateXML()`
* `SimpleSAML_Utilities::validateXMLDocument()`
* `SimpleSAML_Utilities::writeFile()`

The following modules will no longer be shipped with the next version of SimpleSAMLphp:

* `aggregator`
* `aggregator2`
* `aselect`
* `autotest`
* `casserver`
* `consentSimpleAdmin`
* `discojuice`
* `InfoCard`
* `logpeek`
* `metaedit`
* `modinfo`
* `papi`
* `oauth`
* `openid`
* `openidProvider`
* `saml2debug`
* `themefeidernd`
