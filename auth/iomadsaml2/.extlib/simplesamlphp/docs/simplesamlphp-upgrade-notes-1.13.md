Upgrade notes for SimpleSAMLphp 1.13
====================================

  * The RSA_1.5 (RSA with PKCS#1 v1.5 padding) algorithm is now longer allowed by default. This means messages received
  that use this algorithm will fail to decrypt.
  * Several functions, classes and interfaces are now deprecated. Please check your code if you are using the API.
  * A workaround related to performance issues when processing large metadata sets was included in **1.13.2**. **This workaround is experimental and could have unexpected side effects**.