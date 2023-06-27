<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;
use RobRichards\XMLSecLibs\XMLSecurityKey;

use SAML2\Utilities\Temporal;
use SAML2\XML\saml\Issuer;
use SAML2\XML\samlp\Extensions;

/**
 * Base class for all SAML 2 messages.
 *
 * Implements what is common between the samlp:RequestAbstractType and
 * samlp:StatusResponseType element types.
 */
abstract class Message extends SignedElement
{
    /**
     * Request extensions.
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * The name of the root element of the DOM tree for the message.
     *
     * Used when creating a DOM tree from the message.
     *
     * @var string
     */
    private $tagName;

    /**
     * The identifier of this message.
     *
     * @var string
     */
    private $id;

    /**
     * The issue timestamp of this message, as an UNIX timestamp.
     *
     * @var int
     */
    private $issueInstant;

    /**
     * The destination URL of this message if it is known.
     *
     * @var string|null
     */
    private $destination = null;

    /**
     * The destination URL of this message if it is known.
     *
     * @var string
     */
    private $consent = Constants::CONSENT_UNSPECIFIED;

    /**
     * The entity id of the issuer of this message, or null if unknown.
     *
     * @var \SAML2\XML\saml\Issuer|null
     */
    private $issuer = null;

    /**
     * The RelayState associated with this message.
     *
     * @var string|null
     */
    private $relayState = null;

    /**
     * The \DOMDocument we are currently building.
     *
     * This variable is used while generating XML from this message. It holds the
     * \DOMDocument of the XML we are generating.
     *
     * @var \DOMDocument
     */
    protected $document;

    /**
     * The private key we should use to sign the message.
     *
     * The private key can be null, in which case the message is sent unsigned.
     *
     * @var XMLSecurityKey|null
     */
    protected $signatureKey;

    /**
     * @var bool
     */
    protected $messageContainedSignatureUponConstruction = false;

    /**
     * List of certificates that should be included in the message.
     *
     * @var array
     */
    protected $certificates;

    /**
     * Available methods for validating this message.
     *
     * @var array
     */
    private $validators;

    /**
     * @var null|string
     */
    private $signatureMethod = null;


    /**
     * Initialize a message.
     *
     * This constructor takes an optional parameter with a \DOMElement. If this
     * parameter is given, the message will be initialized with data from that
     * XML element.
     *
     * If no XML element is given, the message is initialized with suitable
     * default values.
     *
     * @param string $tagName The tag name of the root element
     * @param \DOMElement|null $xml The input message
     *
     * @throws \Exception
     */
    protected function __construct(string $tagName, DOMElement $xml = null)
    {
        $this->tagName = $tagName;

        $this->id = Utils::getContainer()->generateId();
        $this->issueInstant = Temporal::getTime();
        $this->certificates = [];
        $this->validators = [];

        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('ID')) {
            throw new \Exception('Missing ID attribute on SAML message.');
        }
        $this->id = $xml->getAttribute('ID');

        if ($xml->getAttribute('Version') !== '2.0') {
            /* Currently a very strict check. */
            throw new \Exception('Unsupported version: '.$xml->getAttribute('Version'));
        }

        $this->issueInstant = Utils::xsDateTimeToTimestamp($xml->getAttribute('IssueInstant'));

        if ($xml->hasAttribute('Destination')) {
            $this->destination = $xml->getAttribute('Destination');
        }

        if ($xml->hasAttribute('Consent')) {
            $this->consent = $xml->getAttribute('Consent');
        }

        /** @var \DOMElement[] $issuer */
        $issuer = Utils::xpQuery($xml, './saml_assertion:Issuer');
        if (!empty($issuer)) {
            $this->issuer = new Issuer($issuer[0]);
        }

        $this->validateSignature($xml);

        $this->extensions = Extensions::getList($xml);
    }


    /**
     * Validate the signature element of a SAML message, and configure this object appropriately to perform the
     * signature verification afterwards.
     *
     * Please note this method does NOT verify the signature, it just validates the signature construction and prepares
     * this object to do the verification.
     *
     * @param \DOMElement $xml The SAML message whose signature we want to validate.
     * @return void
     */
    private function validateSignature(DOMElement $xml) : void
    {
        try {
            /** @var \DOMAttr[] $signatureMethod */
            $signatureMethod = Utils::xpQuery($xml, './ds:Signature/ds:SignedInfo/ds:SignatureMethod/@Algorithm');
            if (empty($signatureMethod)) {
                throw new \Exception('No Algorithm specified in signature.');
            }

            $sig = Utils::validateElement($xml);

            if ($sig !== false) {
                $this->messageContainedSignatureUponConstruction = true;
                $this->certificates = $sig['Certificates'];
                $this->validators[] = [
                    'Function' => ['\SAML2\Utils', 'validateSignature'],
                    'Data' => $sig,
                ];
                $this->signatureMethod = $signatureMethod[0]->value;
            }
        } catch (\Exception $e) {
            // ignore signature validation errors
        }
    }


    /**
     * Add a method for validating this message.
     *
     * This function is used by the HTTP-Redirect binding, to make it possible to
     * check the signature against the one included in the query string.
     *
     * @param callable $function The function which should be called
     * @param mixed $data The data that should be included as the first parameter to the function
     * @return void
     */
    public function addValidator(callable $function, $data) : void
    {
        $this->validators[] = [
            'Function' => $function,
            'Data' => $data,
        ];
    }


    /**
     * Validate this message against a public key.
     *
     * true is returned on success, false is returned if we don't have any
     * signature we can validate. An exception is thrown if the signature
     * validation fails.
     *
     * @param XMLSecurityKey $key The key we should check against
     * @throws \Exception
     * @return bool true on success, false when we don't have a signature
     */
    public function validate(XMLSecurityKey $key) : bool
    {
        if (count($this->validators) === 0) {
            return false;
        }

        $exceptions = [];

        foreach ($this->validators as $validator) {
            $function = $validator['Function'];
            $data = $validator['Data'];

            try {
                call_user_func($function, $data, $key);
                /* We were able to validate the message with this validator. */

                return true;
            } catch (\Exception $e) {
                $exceptions[] = $e;
            }
        }

        /* No validators were able to validate the message. */
        throw $exceptions[0];
    }


    /**
     * Retrieve the identifier of this message.
     *
     * @return string The identifier of this message
     */
    public function getId() : string
    {
        return $this->id;
    }


    /**
     * Set the identifier of this message.
     *
     * @param string $id The new identifier of this message
     * @return void
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }


    /**
     * Retrieve the issue timestamp of this message.
     *
     * @return int The issue timestamp of this message, as an UNIX timestamp
     */
    public function getIssueInstant() : int
    {
        return $this->issueInstant;
    }


    /**
     * Set the issue timestamp of this message.
     *
     * @param int $issueInstant The new issue timestamp of this message, as an UNIX timestamp
     * @return void
     */
    public function setIssueInstant(int $issueInstant) : void
    {
        $this->issueInstant = $issueInstant;
    }


    /**
     * Retrieve the destination of this message.
     *
     * @return string|null The destination of this message, or NULL if no destination is given
     */
    public function getDestination() : ?string
    {
        return $this->destination;
    }


    /**
     * Set the destination of this message.
     *
     * @param string|null $destination The new destination of this message
     * @return void
     */
    public function setDestination(string $destination = null) : void
    {
        $this->destination = $destination;
    }


    /**
     * Set the given consent for this message.
     * Most likely (though not required) a value of urn:oasis:names:tc:SAML:2.0:consent.
     *
     * @see \SAML2\Constants
     * @param string $consent
     * @return void
     */
    public function setConsent(string $consent) : void
    {
        $this->consent = $consent;
    }


    /**
     * Get the given consent for this message.
     * Most likely (though not required) a value of urn:oasis:names:tc:SAML:2.0:consent.
     *
     * @see \SAML2\Constants
     * @return string|null Consent
     */
    public function getConsent() : ?string
    {
        return $this->consent;
    }


    /**
     * Retrieve the issuer if this message.
     *
     * @return \SAML2\XML\saml\Issuer|null The issuer of this message, or NULL if no issuer is given
     */
    public function getIssuer() : ?Issuer
    {
        return $this->issuer;
    }


    /**
     * Set the issuer of this message.
     *
     * @param \SAML2\XML\saml\Issuer|null $issuer The new issuer of this message
     * @return void
     */
    public function setIssuer(Issuer $issuer = null) : void
    {
        $this->issuer = $issuer;
    }


    /**
     * Query whether or not the message contained a signature at the root level when the object was constructed.
     *
     * @return bool
     */
    public function isMessageConstructedWithSignature() : bool
    {
        return $this->messageContainedSignatureUponConstruction;
    }


    /**
     * Retrieve the RelayState associated with this message.
     *
     * @return string|null The RelayState, or NULL if no RelayState is given
     */
    public function getRelayState() : ?string
    {
        return $this->relayState;
    }


    /**
     * Set the RelayState associated with this message.
     *
     * @param string|null $relayState The new RelayState
     * @return void
     */
    public function setRelayState(string $relayState = null) : void
    {
        $this->relayState = $relayState;
    }


    /**
     * Convert this message to an unsigned XML document.
     * This method does not sign the resulting XML document.
     *
     * @return \DOMElement The root element of the DOM tree
     */
    public function toUnsignedXML() : DOMElement
    {
        $this->document = DOMDocumentFactory::create();

        $root = $this->document->createElementNS(Constants::NS_SAMLP, 'samlp:'.$this->tagName);
        $this->document->appendChild($root);

        /* Ugly hack to add another namespace declaration to the root element. */
        $root->setAttributeNS(Constants::NS_SAML, 'saml:tmp', 'tmp');
        $root->removeAttributeNS(Constants::NS_SAML, 'tmp');

        $root->setAttribute('ID', $this->id);
        $root->setAttribute('Version', '2.0');
        $root->setAttribute('IssueInstant', gmdate('Y-m-d\TH:i:s\Z', $this->issueInstant));

        if ($this->destination !== null) {
            $root->setAttribute('Destination', $this->destination);
        }
        if ($this->consent !== Constants::CONSENT_UNSPECIFIED) {
            $root->setAttribute('Consent', $this->consent);
        }

        if ($this->issuer !== null) {
            $this->issuer->toXML($root);
        }

        Extensions::addList($root, $this->extensions);

        return $root;
    }


    /**
     * Convert this message to a signed XML document.
     * This method sign the resulting XML document if the private key for
     * the signature is set.
     *
     * @return \DOMElement The root element of the DOM tree
     */
    public function toSignedXML() : DOMElement
    {
        $root = $this->toUnsignedXML();

        if ($this->signatureKey === null) {
            /* We don't have a key to sign it with. */

            return $root;
        }

        /* Find the position we should insert the signature node at. */
        if ($this->issuer !== null) {
            /*
             * We have an issuer node. The signature node should come
             * after the issuer node.
             */
            $issuerNode = $root->firstChild;
            /** @psalm-suppress PossiblyNullPropertyFetch */
            $insertBefore = $issuerNode->nextSibling;
        } else {
            /* No issuer node - the signature element should be the first element. */
            $insertBefore = $root->firstChild;
        }

        Utils::insertSignature($this->signatureKey, $this->certificates, $root, $insertBefore);

        return $root;
    }


    /**
     * Retrieve the private key we should use to sign the message.
     *
     * @return XMLSecurityKey|null The key, or NULL if no key is specified
     */
    public function getSignatureKey() : ?XMLSecurityKey
    {
        return $this->signatureKey;
    }


    /**
     * Set the private key we should use to sign the message.
     * If the key is null, the message will be sent unsigned.
     *
     * @param XMLSecurityKey|null $signatureKey
     * @return void
     */
    public function setSignatureKey(XMLSecurityKey $signatureKey = null) : void
    {
        $this->signatureKey = $signatureKey;
    }


    /**
     * Set the certificates that should be included in the message.
     * The certificates should be strings with the PEM encoded data.
     *
     * @param array $certificates An array of certificates
     * @return void
     */
    public function setCertificates(array $certificates) : void
    {
        $this->certificates = $certificates;
    }


    /**
     * Retrieve the certificates that are included in the message.
     *
     * @return array An array of certificates
     */
    public function getCertificates() : array
    {
        return $this->certificates;
    }


    /**
     * Convert an XML element into a message.
     *
     * @param \DOMElement $xml The root XML element
     * @throws \Exception
     * @return \SAML2\Message The message
     */
    public static function fromXML(\DOMElement $xml) : Message
    {
        if ($xml->namespaceURI !== Constants::NS_SAMLP) {
            throw new \Exception('Unknown namespace of SAML message: '.var_export($xml->namespaceURI, true));
        }

        switch ($xml->localName) {
            case 'AttributeQuery':
                return new AttributeQuery($xml);
            case 'AuthnRequest':
                return new AuthnRequest($xml);
            case 'LogoutResponse':
                return new LogoutResponse($xml);
            case 'LogoutRequest':
                return new LogoutRequest($xml);
            case 'Response':
                return new Response($xml);
            case 'ArtifactResponse':
                return new ArtifactResponse($xml);
            case 'ArtifactResolve':
                return new ArtifactResolve($xml);
            default:
                throw new \Exception('Unknown SAML message: '.var_export($xml->localName, true));
        }
    }


    /**
     * Retrieve the Extensions.
     *
     * @return \SAML2\XML\samlp\Extensions[]
     */
    public function getExtensions() : array
    {
        return $this->extensions;
    }


    /**
     * Set the Extensions.
     *
     * @param array $extensions The Extensions
     * @return void
     */
    public function setExtensions(array $extensions) : void
    {
        $this->extensions = $extensions;
    }


    /**
     * Add an Extension.
     *
     * @param \SAML2\XML\samlp\Extensions $extensions The Extensions
     * @return void
     */
    public function addExtension(Extensions $extension) : void
    {
        $this->extensions[] = $extension;
    }


    /**
     * @return null|string
     */
    public function getSignatureMethod() : ?string
    {
        return $this->signatureMethod;
    }
}
