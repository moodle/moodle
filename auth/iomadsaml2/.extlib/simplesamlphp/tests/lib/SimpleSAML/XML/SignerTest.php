<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XML;

use DOMDocument;
use DOMElement;
use Exception;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SimpleSAML\Configuration;
use SimpleSAML\Test\SigningTestCase;
use SimpleSAML\XML\Signer;

/**
 * Tests for SimpleSAML\XML\Signer.
 */
class SignerTest extends SigningTestCase
{
    /** @var string */
    private $other_certificate_file;

    // openssl req -new -x509 -key good.key.pem -out public2.pem -days 3650
    /** @var string */
    private $other_certificate = <<<'NOWDOC'
-----BEGIN CERTIFICATE-----
MIIDazCCAlOgAwIBAgIUGPKUWW1GN07xxAsGENQ+rZPyABAwDQYJKoZIhvcNAQEL
BQAwRTELMAkGA1UEBhMCQVUxEzARBgNVBAgMClNvbWUtU3RhdGUxITAfBgNVBAoM
GEludGVybmV0IFdpZGdpdHMgUHR5IEx0ZDAeFw0xOTAyMTgxNTU5MDRaFw0yOTAy
MTUxNTU5MDRaMEUxCzAJBgNVBAYTAkFVMRMwEQYDVQQIDApTb21lLVN0YXRlMSEw
HwYDVQQKDBhJbnRlcm5ldCBXaWRnaXRzIFB0eSBMdGQwggEiMA0GCSqGSIb3DQEB
AQUAA4IBDwAwggEKAoIBAQCqY2fhu3+OsweCha3BctzaiTXsEeHU7kRNuf2srcoW
OTcFenndoa96KWX4ptbtKCuIZlJrUoETa5pDLaZmmIFCoWstyAqG3NoI0vIG8o6j
NCGf9c3qbaQ3wQNloYgbG0z/rKlQCfK2xwajZsrPIe06Ng4/AR4AuKrv5itHUFzZ
fTk7JW51XxaO7xBQKbs2jzn9M6PEPpKtZRe4Q8mbRGLJUx5mG4qa3GbSQHZm1DSU
pWI7FouIFGJb1OV1j3g1od+BplKkKauKSiJLhAniFOjicRWmPH7UEMkhqvdt/Ef6
/C4uU6hmDcoj0Fhxabmf5crBPGeFoFYzwwfc+2Ys1utHAgMBAAGjUzBRMB0GA1Ud
DgQWBBSJC3ioF0fu+hgZTQCy0dg1LwNE0TAfBgNVHSMEGDAWgBSJC3ioF0fu+hgZ
TQCy0dg1LwNE0TAPBgNVHRMBAf8EBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQB/
otEPUNWLWIEJXOnF7pPv1orXdeCGpCgNK+k8pJDYkl7jTNs3sq8U/aCy9qIrrFOA
MH0D5dh9xVf+DeDeUKT6/Td8EvPrXnzfkfU2xDgbCKk+WIk1luMvCwOzxYFlPCOo
pBCt2aQAHuqKeR6uXOjyRv5Kw0jdr94df/FquqHFkSQxVSPBSLW8jzwxXKSh291j
d2udRIYG0WcjQTk86+EraXNGtuwUaknQ7WPKlJwLzypuZM8lk3F1FXxXWomHN3SH
29N8MpL1tceQuMX8F6cdQuhjLThs4b+Dy6ITF05Pgm7xr1tByO+C5e7dNpEDwA3I
31r+Yt4vwxjeCqQDSZik
-----END CERTIFICATE-----
NOWDOC;

    private const OTHER_CERTIFICATE = 'other_certificate.pem';


    /**
     * @return array
     */
    public function getCertDirContent(): array
    {
        return [
            self::GOOD_PRIVATE_KEY => $this->good_private_key,
            self::GOOD_CERTIFICATE => $this->good_certificate,
            self::OTHER_CERTIFICATE => $this->other_certificate,
        ];
    }


    /**
     * @return void
     */
    public function testSignerBasic(): void
    {
        $res = new Signer([]);

        $this->assertNotNull($res);
    }


    /**
     * @return void
     */
    public function testSignBasic(): void
    {
        $node = new DOMDocument();
        $node->loadXML('<?xml version="1.0"?><node>value</node>');

        /** @psalm-var DOMElement $element */
        $element = $node->getElementsByTagName("node")->item(0);

        $doc = new DOMDocument();
        $insertInto = $doc->appendChild(new DOMElement('insert'));

        $signer = new Signer([]);
        $signer->loadPrivateKey($this->good_private_key_file, null, true);
        $signer->sign($element, $insertInto);

        $res = $doc->saveXML();

        $this->assertContains('DigestValue', $res);
        $this->assertContains('SignatureValue', $res);
    }


    /**
     * @param string $certificate
     * @return string
     */
    private static function getCertificateValue(string $certificate): string
    {
        $replacements = [
            "-----BEGIN CERTIFICATE-----",
            "-----END CERTIFICATE-----",
            "\n",
        ];

        return str_replace($replacements, "", $certificate);
    }


    /**
     * @return void
     */
    public function testSignWithCertificate(): void
    {
        $node = new DOMDocument();
        $node->loadXML('<?xml version="1.0"?><node>value</node>');

        /** @psalm-var DOMElement $element */
        $element = $node->getElementsByTagName("node")->item(0);

        $doc = new DOMDocument();
        $insertInto = $doc->appendChild(new DOMElement('insert'));

        $signer = new Signer([]);
        $signer->loadPrivateKey($this->good_private_key_file, null, true);
        $signer->loadCertificate($this->good_certificate_file, true);
        $signer->sign($element, $insertInto);

        $res = $doc->saveXML();

        $expected = self::getCertificateValue($this->good_certificate);

        $this->assertContains('X509Certificate', $res);
        $this->assertContains($expected, $res);
    }


    /**
     * @return void
     */
    public function testSignWithMultiCertificate(): void
    {
        $this->other_certificate_file = $this->certdir . DIRECTORY_SEPARATOR . self::OTHER_CERTIFICATE;

        $node = new DOMDocument();
        $node->loadXML('<?xml version="1.0"?><node>value</node>');

        /** @psalm-var DOMElement $element */
        $element = $node->getElementsByTagName("node")->item(0);

        $doc = new DOMDocument();
        $insertInto = $doc->appendChild(new DOMElement('insert'));

        $signer = new Signer([]);
        $signer->loadPrivateKey($this->good_private_key_file, null, true);
        $signer->loadCertificate($this->good_certificate_file, true);
        $signer->addCertificate($this->other_certificate_file, true);
        $signer->sign($element, $insertInto);

        $res = $doc->saveXML();

        $expected1 = self::getCertificateValue($this->good_certificate);
        $expected2 = self::getCertificateValue($this->other_certificate);

        $this->assertContains('X509Certificate', $res);
        $this->assertContains($expected1, $res);
        $this->assertContains($expected2, $res);
    }


    /**
     * @return void
     */
    public function testSignMissingPrivateKey(): void
    {
        $node = new DOMDocument();
        $node->loadXML('<?xml version="1.0"?><node>value</node>');

        /** @psalm-var DOMElement $element */
        $element = $node->getElementsByTagName("node")->item(0);

        $doc = new DOMDocument();
        $insertInto = $doc->appendChild(new DOMElement('insert'));

        $signer = new Signer([]);

        $this->expectException(Exception::class);
        $signer->sign($element, $insertInto);
    }


    /**
     * @param \SimpleSAML\Configuration $service
     * @param class-string $className
     * @param mixed|null $value
     * @return void
     */
    protected function clearInstance(Configuration $service, string $className, $value = null): void
    {
        $reflectedClass = new ReflectionClass($className);
        $reflectedInstance = $reflectedClass->getProperty('instance');
        $reflectedInstance->setAccessible(true);
        $reflectedInstance->setValue($service, $value);
        $reflectedInstance->setAccessible(false);
    }
}
