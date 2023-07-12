<?php

declare(strict_types=1);

namespace SAML2\Signature;

use Psr\Log\LoggerInterface;

use SAML2\Configuration\CertificateProvider;
use SAML2\SignedElement;

/**
 * Allows for validation of a signature trying different validators till a validator is found
 * that can validate the signature.
 *
 * If no validation is possible an exception is thrown.
 */
class ValidatorChain implements ValidatorInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var  \SAML2\Signature\ChainedValidator[]
     */
    private $validators = [];


    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \SAML2\Signature\ChainedValidator[] $validators
     */
    public function __construct(LoggerInterface $logger, array $validators)
    {
        $this->logger = $logger;

        // should be done through "adder" injection in the container.
        foreach ($validators as $validator) {
            $this->appendValidator($validator);
        }
    }


    /**
     * @param \SAML2\Signature\ChainedValidator $validator
     * @return void
     */
    public function appendValidator(ChainedValidator $validator) : void
    {
        $this->validators[] = $validator;
    }


    /**
     * @param \SAML2\SignedElement $signedElement
     * @param \SAML2\Configuration\CertificateProvider $configuration
     *
     * @return bool
     */
    public function hasValidSignature(
        SignedElement $signedElement,
        CertificateProvider $configuration
    ) : bool {
        foreach ($this->validators as $validator) {
            if ($validator->canValidate($signedElement, $configuration)) {
                $this->logger->debug(sprintf(
                    'Validating the signed element with validator of type "%s"',
                    get_class($validator)
                ));

                return $validator->hasValidSignature($signedElement, $configuration);
            }

            $this->logger->debug(sprintf(
                'Could not validate the signed element with validator of type "%s"',
                get_class($validator)
            ));
        }

        throw new MissingConfigurationException(sprintf(
            'No certificates have been configured%s',
            $configuration->has('entityid') ? ' for "'.$configuration->get('entityid').'"' : ''
        ));
    }
}
