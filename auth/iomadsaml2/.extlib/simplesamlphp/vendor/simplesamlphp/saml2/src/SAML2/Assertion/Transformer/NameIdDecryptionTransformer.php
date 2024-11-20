<?php

declare(strict_types=1);

namespace SAML2\Assertion\Transformer;

use Psr\Log\LoggerInterface;

use SAML2\Assertion;
use SAML2\Assertion\Exception\NotDecryptedException;
use SAML2\Certificate\PrivateKeyLoader;
use SAML2\Configuration\IdentityProvider;
use SAML2\Configuration\IdentityProviderAware;
use SAML2\Configuration\ServiceProvider;
use SAML2\Configuration\ServiceProviderAware;

final class NameIdDecryptionTransformer implements
    Transformer,
    IdentityProviderAware,
    ServiceProviderAware
{
    /**
     * @var \SAML2\Certificate\PrivateKeyLoader
     */
    private $privateKeyLoader;

    /**
     * @var \SAML2\Configuration\IdentityProvider
     */
    private $identityProvider;

    /**
     * @var \SAML2\Configuration\ServiceProvider
     */
    private $serviceProvider;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;


    /**
     * Constructor for NameIdDecryptionTransformer
     *
     * @param LoggerInterface $logger
     * @param PrivateKeyLoader $privateKeyLoader
     */
    public function __construct(
        LoggerInterface $logger,
        PrivateKeyLoader $privateKeyLoader
    ) {
        $this->logger = $logger;
        $this->privateKeyLoader = $privateKeyLoader;
    }


    /**
     * @param Assertion $assertion
     * @throws \Exception
     * @return Assertion
     */
    public function transform(Assertion $assertion) : Assertion
    {
        if (!$assertion->isNameIdEncrypted()) {
            return $assertion;
        }

        $decryptionKeys  = $this->privateKeyLoader->loadDecryptionKeys($this->identityProvider, $this->serviceProvider);
        $blacklistedKeys = $this->identityProvider->getBlacklistedAlgorithms();
        if (is_null($blacklistedKeys)) {
            $blacklistedKeys = $this->serviceProvider->getBlacklistedAlgorithms();
        }

        foreach ($decryptionKeys as $index => $key) {
            try {
                $assertion->decryptNameId($key, $blacklistedKeys);
                $this->logger->debug(sprintf('Decrypted assertion NameId with key "#%d"', $index));
            } catch (\Exception $e) {
                $this->logger->debug(sprintf(
                    'Decrypting assertion NameId with key "#%d" failed, "%s" thrown: "%s"',
                    $index,
                    get_class($e),
                    $e->getMessage()
                ));
            }
        }

        if ($assertion->isNameIdEncrypted()) {
            throw new NotDecryptedException(
                'Could not decrypt the assertion NameId with the configured keys, see the debug log for information'
            );
        }

        return $assertion;
    }


    /**
     * @param IdentityProvider $identityProvider
     * @return void
     */
    public function setIdentityProvider(IdentityProvider $identityProvider) : void
    {
        $this->identityProvider = $identityProvider;
    }


    /**
     * @param ServiceProvider $serviceProvider
     * @return void
     */
    public function setServiceProvider(ServiceProvider $serviceProvider) : void
    {
        $this->serviceProvider = $serviceProvider;
    }
}
