Upgrade notes for SimpleSAMLphp 1.8
===================================

  * The IdP now sends the NotOnOrAfter attribute in LogoutRequest messages.
  * We now have full support for selecting the correct AssertionConsumerService endpoint based on parameters in the authentication request.
    As a side effect of this, an IdP may start sending responses to a new AssertionConsumerService endpoint after upgrade.
    (This should only happen in the case where it sent the response to the wrong endpoint before.)
  * The SP no longer incorrectly returns PartialLogout as a status code in a LogoutResponse after the local session has expired.

