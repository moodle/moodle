<?php

declare(strict_types=1);

namespace SAML2\Response;

use Psr\Log\LoggerInterface;

use SAML2\Assertion\ProcessorBuilder;
use SAML2\Configuration\Destination;
use SAML2\Configuration\IdentityProvider;
use SAML2\Configuration\ServiceProvider;
use SAML2\Response;
use SAML2\Response\Exception\InvalidResponseException;
use SAML2\Response\Exception\NoAssertionsFoundException;
use SAML2\Response\Exception\PreconditionNotMetException;
use SAML2\Response\Exception\UnsignedResponseException;
use SAML2\Response\Validation\PreconditionValidator;
use SAML2\Signature\Validator;
use SAML2\Utilities\ArrayCollection;

class Processor
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \SAML2\Response\Validation\PreconditionValidator
     */
    private $preconditionValidator;

    /**
     * @var \SAML2\Signature\Validator
     */
    private $signatureValidator;

    /**
     * @var \SAML2\Assertion\Processor
     */
    private $assertionProcessor;

    /**
     * Indicates whether or not the response was signed. This is required in order to be able to check whether either
     * the reponse or one of its assertions was signed
     *
     * @var bool
     */
    private $responseIsSigned = false;


    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->signatureValidator = new Validator($logger);
    }


    /**
     * @param \SAML2\Configuration\ServiceProvider  $serviceProviderConfiguration
     * @param \SAML2\Configuration\IdentityProvider $identityProviderConfiguration
     * @param \SAML2\Configuration\Destination $currentDestination
     * @param \SAML2\Response $response
     *
     * @return \SAML2\Utilities\ArrayCollection Collection of \SAML2\Assertion objects
     */
    public function process(
        ServiceProvider $serviceProviderConfiguration,
        IdentityProvider $identityProviderConfiguration,
        Destination $currentDestination,
        Response $response
    ) : ArrayCollection {
        $this->preconditionValidator = new PreconditionValidator($currentDestination);
        $this->assertionProcessor = ProcessorBuilder::build(
            $this->logger,
            $this->signatureValidator,
            $currentDestination,
            $identityProviderConfiguration,
            $serviceProviderConfiguration,
            $response
        );

        $this->enforcePreconditions($response);
        $this->verifySignature($response, $identityProviderConfiguration);
        return $this->processAssertions($response);
    }


    /**
     * Checks the preconditions that must be valid in order for the response to be processed.
     *
     * @param \SAML2\Response $response
     * @throws PreconditionNotMetException
     * @return void
     */
    private function enforcePreconditions(Response $response) : void
    {
        $result = $this->preconditionValidator->validate($response);

        if (!$result->isValid()) {
            throw PreconditionNotMetException::createFromValidationResult($result);
        }
    }


    /**
     * @param \SAML2\Response $response
     * @param \SAML2\Configuration\IdentityProvider $identityProviderConfiguration
     * @throws InvalidResponseException
     * @return void
     */
    private function verifySignature(Response $response, IdentityProvider $identityProviderConfiguration) : void
    {
        if (!$response->isMessageConstructedWithSignature()) {
            $this->logger->info(sprintf(
                'SAMLResponse with id "%s" was not signed at root level, not attempting to verify the signature of the'
                . ' reponse itself',
                $response->getId()
            ));

            return;
        }

        $this->logger->info(sprintf(
            'Attempting to verify the signature of SAMLResponse with id "%s"',
            $response->getId()
        ));

        $this->responseIsSigned = true;

        if (!$this->signatureValidator->hasValidSignature($response, $identityProviderConfiguration)) {
            throw new InvalidResponseException(
                sprintf('The SAMLResponse with id "%s", does not have a valid signature', $response->getId())
            );
        }
    }


    /**
     * @param \SAML2\Response $response
     * @throws UnsignedResponseException
     * @throws NoAssertionsFoundException
     * @return \SAML2\Utilities\ArrayCollection
     */
    private function processAssertions(Response $response) : ArrayCollection
    {
        $assertions = $response->getAssertions();
        if (empty($assertions)) {
            throw new NoAssertionsFoundException('No assertions found in response from IdP.');
        }

        $decryptedAssertions = $this->assertionProcessor->decryptAssertions(
            new ArrayCollection($assertions)
        );

        if (!$this->responseIsSigned) {
            foreach ($assertions as $assertion) {
                if (!$assertion->wasSignedAtConstruction()) {
                    throw new UnsignedResponseException(
                        'Both the response and the assertion it contains are not signed.'
                    );
                }
            }
        }

        return $this->assertionProcessor->processAssertions($decryptedAssertions);
    }
}
