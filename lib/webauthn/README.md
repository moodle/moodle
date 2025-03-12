[![Licensed under the MIT License](https://img.shields.io/badge/License-MIT-blue.svg)](https://github.com/lbuchs/WebAuthn/blob/master/LICENSE)
[![Requires PHP 7.1.0](https://img.shields.io/badge/PHP-7.1.0-green.svg)](https://php.net)
[![Last Commit](https://img.shields.io/github/last-commit/lbuchs/WebAuthn.svg)](https://github.com/lbuchs/WebAuthn/commits/master)

# WebAuthn
*A simple PHP WebAuthn (FIDO2) server library*

Goal of this project is to provide a small, lightweight, understandable library to protect logins with passkeys, security keys like Yubico or Solo, fingerprint on Android or Windows Hello.

## Manual
See /_test for a simple usage of this library. Check [webauthn.lubu.ch](https://webauthn.lubu.ch) for a working example.

### Supported attestation statement formats
* android-key &#x2705;
* android-safetynet &#x2705;
* apple &#x2705;
* fido-u2f &#x2705;
* none &#x2705;
* packed &#x2705;
* tpm &#x2705;

> [!NOTE]
> This library supports authenticators which are signed with a X.509 certificate or which are self attested. ECDAA is not supported.

## Workflow

             JAVASCRIPT            |          SERVER
    ------------------------------------------------------------
                             REGISTRATION


       window.fetch  ----------------->     getCreateArgs
                                                 |
    navigator.credentials.create   <-------------'
            |
            '------------------------->     processCreate
                                                 |
          alert ok or fail      <----------------'


    ------------------------------------------------------------
                          VALIDATION


       window.fetch ------------------>      getGetArgs
                                                 |
    navigator.credentials.get   <----------------'
            |
            '------------------------->      processGet
                                                 |
          alert ok or fail      <----------------'

## Attestation
Typically, when someone logs in, you only need to confirm that they are using the same device they used during
registration. In this scenario, you do not require any form of attestation.
However, if you need additional security, such as when your company mandates the use of a Solokey for login,
you can verify its authenticity through direct attestation. Companies may also purchase authenticators that
are signed with their own root certificate, enabling them to validate that an authenticator is affiliated with
their organization.

### no attestation
just verify that the device is the same device used on registration.
You can use 'none' attestation with this library if you only check 'none' as format.

> [!TIP]
> this is propably what you want to use if you want secure login for a public website.

### indirect attestation
the browser may replace the AAGUID and attestation statement with a more privacy-friendly and/or more easily
verifiable version of the same data (for example, by employing an anonymization CA).
You can not validate against any root ca, if the browser uses a anonymization certificate.
this library sets attestation to indirect, if you select multiple formats but don't provide any root ca.

> [!TIP]
> hybrid soultion, clients may be discouraged by browser warnings but then you know what device they're using (statistics rulez!)

### direct attestation
the browser proviedes data about the identificator device, the device can be identified uniquely. User could be tracked over multiple sites, because of that the browser may show a warning message about providing this data when register.
this library sets attestation to direct, if you select multiple formats and provide root ca's.

> [!TIP]
> this is probably what you want if you know what devices your clients are using and make sure that only this devices are used.

## Passkeys / Client-side discoverable Credentials
A Client-side discoverable Credential Source is a public key credential source whose credential private key is stored in the authenticator,
client or client device. Such client-side storage requires a resident credential capable authenticator.
This is only supported by FIDO2 hardware, not by older U2F hardware.

>[!NOTE]
>Passkeys is a technique that allows sharing credentials stored on the device with other devices. So from a technical standpoint of the server, there is no difference to client-side discoverable credentials. The difference is only that the phone or computer system is automatically syncing the credentials between the user’s devices via a cloud service. The cross-device sync of passkeys is managed transparently by the OS.

### How does it work?
In a typical server-side key management process, a user initiates a request by entering their username and, in some cases, their password. 
The server validates the user's credentials and, upon successful authentication, retrieves a list of all public key identifiers associated with that user account. 
This list is then returned to the authenticator, which selects the first credential identifier it issued and responds with a signature that can be verified using the public key registered during the registration process.

In a client-side key process, the user does not need to provide a username or password.
Instead, the authenticator searches its own memory to see if it has saved a key for the relying party (domain).
If a key is found, the authentication process proceeds in the same way as it would if the server had sent a list
of identifiers. There is no difference in the verification process.

### How can I use it with this library?
#### on registration
When calling `WebAuthn\WebAuthn->getCreateArgs`, set `$requireResidentKey` to true,
to notify the authenticator that he should save the registration in its memory.

#### on login
When calling `WebAuthn\WebAuthn->getGetArgs`, don't provide any `$credentialIds` (the authenticator will look up the ids in its own memory and returns the user ID as userHandle).
Set the type of authenticator to `hybrid` (Passkey scanned via QR Code) and `internal` (Passkey stored on the device itself).

#### disadvantage
The RP ID (= domain) is saved on the authenticator. So If an authenticator is lost, its theoretically possible to find the services, which the authenticator is used and login there.

### device support
Availability of built-in passkeys that automatically synchronize to all of a user’s devices: (see also [passkeys.dev/device-support](https://passkeys.dev/device-support/))
* Apple iOS 16+ / iPadOS 16+ / macOS Ventura+
* Android 9+
* Microsoft Windows 11 23H2+

## Requirements
* PHP >= 8.0 with [OpenSSL](http://php.net/manual/en/book.openssl.php) and [Multibyte String](https://www.php.net/manual/en/book.mbstring.php)
* Browser with [WebAuthn support](https://caniuse.com/webauthn) (Firefox 60+, Chrome 67+, Edge 18+, Safari 13+)
* PHP [Sodium](https://www.php.net/manual/en/book.sodium.php) (or [Sodium Compat](https://github.com/paragonie/sodium_compat) ) for [Ed25519](https://en.wikipedia.org/wiki/EdDSA#Ed25519) support

## Infos about WebAuthn
* [Wikipedia](https://en.wikipedia.org/wiki/WebAuthn)
* [W3C](https://www.w3.org/TR/webauthn/)
* [MDN](https://developer.mozilla.org/en-US/docs/Web/API/Web_Authentication_API)
* [dev.yubico](https://developers.yubico.com/FIDO2/)
* [FIDO Alliance](https://fidoalliance.org)
* [passkeys](https://passkeys.dev/)

## FIDO2 Hardware
* [Yubico](https://www.yubico.com)
* [Solo](https://solokeys.com) Open Source!
* [Nitrokey](https://www.nitrokey.com/)
* [Feitan](https://fido.ftsafe.com/)
* [TrustKey](https://www.trustkeysolutions.com)
* [Google Titan](https://cloud.google.com/titan-security-key)
* [Egis](https://www.egistec.com/u2f-solution/)
* [OneSpan](https://www.vasco.com/products/two-factor-authenticators/hardware/one-button/digipass-secureclick.html)
* [Hypersecu](https://hypersecu.com/tmp/products/hyperfido)
* [Kensington VeriMark™](https://www.kensington.com/)
* [Token2](https://www.token2.com/shop/category/fido2-keys)
