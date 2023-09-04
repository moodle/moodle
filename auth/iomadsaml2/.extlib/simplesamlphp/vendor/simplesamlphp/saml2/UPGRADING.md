# UPGRADE NOTES

## 4.0 to 4.1

**Assertion processor BC breaking change**

The assertion processor in 4.0 assumed all assertions are encrypted and would try and decrypt twice, causing a fatal error.
This problem was introduced in [#120](https://github.com/simplesamlphp/saml2/pull/120).

If you are using the assertion processor as a stand-alone component, then you will have to update your code to reflect this
change, see: [97a7bd0](https://github.com/simplesamlphp/saml2/commit/97a7bd0be4865617048e1ea92aa0b55df488fe4a).

## 3.4 to 4.0 

**Assertion processor BC breaking change**

The response processor in pre 4.0 releases assumed all assertions are decrypted, and caused a fatal error when the response was unsigned.
This problem was fixed in [#120](https://github.com/simplesamlphp/saml2/pull/120). 

If you are using the assertion processor as a stand-alone component, then you will have to update your code to reflect this
change, see: [e6c01fa](https://github.com/simplesamlphp/saml2/commit/e6c01fa9b0e815682e24916f03a84d245480c4a0).

**NameID's and Issuers**

In pre 4.0 releases we allowed both objects and arrays to be used for Issuers and nameID's. We know only support objects.
If in your code you use something like this:

``
$assertion = new \SAML2\Assertion();
$assertion->setIssuer('someissuer');
``

You would now replace that with:

``
$issuer = new \SAML2\XML\saml\Issuer();
$issuer->setValue('someissuer');

$assertion = new \SAML2\Assertion();
$assertion->setIssuer($issuer);
``

**Class properties**
All public properies have been replaced by either protected or private properties.
Public getter/setter methods are available to set/get values.

**Autoloading classes**
The PSR-0 autoloader has been removed. If your code isn't prepared to use PSR-4 namespaces yet, then you would have to change that before using this version.
