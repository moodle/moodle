<?php

declare(strict_types=1);

namespace SAML2\Assertion;

use Psr\Log\LoggerInterface;

use SAML2\Assertion;
use SAML2\Assertion\Exception\NotDecryptedException;
use SAML2\Certificate\PrivateKeyLoader;
use SAML2\Configuration\IdentityProvider;
use SAML2\Configuration\ServiceProvider;
use SAML2\EncryptedAssertion;

class Decrypter
{
    /**
     * @var \SAML2\Configuration\IdentityProvider
     */
    private $identityProvider;

    /**
     * @var \SAML2\Configuration\ServiceProvider
     */
    private $serviceProvider;

    /**
     * @var \SAML2\Certificate\PrivateKeyLoader
     */
    private $privateKeyLoader;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;


    /**
     * Constructor for Decrypter.
     *
     * @param LoggerInterface $logger
     * @param IdentityProvider $identityProvider
     * @param ServiceProvider $serviceProvider
     * @param PrivateKeyLoader $privateKeyLoader
     */
    public function __construct(
        LoggerInterface $logger,
        IdentityProvider $identityProvider,
        ServiceProvider $serviceProvider,
        PrivateKeyLoader $privateKeyLoader
    ) {
        $this->logger = $logger;
        $this->identityProvider = $identityProvider;
        $this->serviceProvider = $serviceProvider;
        $this->privateKeyLoader = $privateKeyLoader;
    }


    /**
     * Allows for checking whether either the SP or the IdP requires assertion encryption
     *
     * @return bool
     */
    public function isEncryptionRequired() : bool
    {
        return $this->identityProvider->isAssertionEncryptionRequired()
            || $this->serviceProvider->isAssertionEncryptionRequired();
    }


    /**
     * @param \SAML2\EncryptedAssertion $assertion
     *
     * @return \SAML2\Assertion
     */
    public function decrypt(EncryptedAssertion $assertion) : Assertion
    {
        $decryptionKeys = $this->privateKeyLoader->loadDecryptionKeys($this->identityProvider, $this->serviceProvider);
        $blacklistedKeys = $this->identityProvider->getBlacklistedAlgorithms();
        if (is_null($blacklistedKeys)) {
            $blacklistedKeys = $this->serviceProvider->getBlacklistedAlgorithms();
        }

        // reflects the simplesamlphp behaviour for BC, see
        // https://github.com/simplesamlphp/simplesamlphp/blob/3d735912342767d391297cc5e13272a76730aca0/modules/saml/lib/Message.php#L369
        foreach ($decryptionKeys as $index => $key) {
            try {
                $decryptedAssertion = $assertion->getAssertion($key, $blacklistedKeys);
                $this->logger->debug(sprintf('Decrypted Assertion with key "#%d"', $index));

                return $decryptedAssertion;
            } catch (\Exception $e) {
                $this->logger->debug(sprintf(
                    'Could not decrypt assertion with key "#%d", "%s" thrown: "%s"',
                    $index,
                    get_class($e),
                    $e->getMessage()
                ));
            }
        }

        throw new NotDecryptedException(sprintf(
            'Could not decrypt the assertion, tried with "%d" keys. See the debug log for more information',
            count($decryptionKeys)
        ));
    }
}
