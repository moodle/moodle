Upgrade notes for SimpleSAMLphp 1.10
====================================

  * The default encryption key padding scheme has been changed to `http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p`. This may cause problems if the recipient of messages do not support this padding scheme.
