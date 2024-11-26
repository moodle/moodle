<?php
/*
    Copyright 2014 Rustici Software

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

namespace TinCan;

use InvalidArgumentException;
use Namshi\JOSE\JWS;

class Statement extends StatementBase
{
    const SIGNATURE_USAGE_TYPE = 'http://adlnet.gov/expapi/attachments/signature';
    const SIGNATURE_CONTENT_TYPE = 'application/octet-stream';

    protected $id;

    //
    // stored *must* store a string because DateTime doesn't
    // support sub-second precision, the setter will take a DateTime and convert
    // it to the proper ISO8601 representation, but if a user needs sub-second
    // precision as afforded by the spec they will have to create their own,
    // they can see TinCan\Util::getTimestamp for an example of how to do so
    //
    protected $stored;

    protected $authority;
    protected $version;
    protected $attachments;

    public function __construct() {
        call_user_func_array('parent::__construct', func_get_args());

        if (func_num_args() == 1) {
            $arg = func_get_arg(0);

            //
            // 'object' isn't in the list of properties so ._fromArray doesn't
            // pick it up correctly, but 'target' and 'object' shouldn't be in
            // the args at the same time, so handle 'object' here
            //
            if (isset($arg['object'])) {
                $this->setObject($arg['object']);
            }
        }
        if (! isset($this->attachments)) {
            $this->setAttachments(array());
        }
    }

    public function stamp() {
        $this->setId(Util::getUUID());
        $this->setTimestamp(Util::getTimestamp());

        return $this;
    }

    public function compareWithSignature($fromSig) {
        foreach (array('id', 'attachments') as $property) {
            if (! isset($this->$property) && ! isset($fromSig->$property)) {
                continue;
            }
            if (isset($this->$property) && ! isset($fromSig->$property)) {
                return array('success' => false, 'reason' => "Comparison of $property failed: value not in signature");
            }
            if (isset($fromSig->$property) && ! isset($this->$property)) {
                return array('success' => false, 'reason' => "Comparison of $property failed: value not in this");
            }
        }
        if (isset($this->id)) {
            if ($this->id !== $fromSig->id) {
                return array('success' => false, 'reason' => 'Comparison of id failed: value is not the same');
            }
        }
        if (isset($this->attachments)) {
            if (count($this->attachments) !== count($fromSig->attachments)) {
                return array('success' => false, 'reason' => 'Comparison of attachments list failed: array lengths differ');
            }

            for ($i = 0; $i < count($this->attachments); $i++) {
                $comparison = $this->attachments[$i]->compareWithSignature($fromSig->attachments[$i]);
                if (! $comparison['success']) {
                    return array('success' => false, 'reason' => "Comparison of attachment $i failed: " . $comparison['reason']);
                }
            }
        }

        return parent::compareWithSignature($fromSig);
    }

    private function serializeForSignature($version) {
        if (! isset($this->actor)) {
            throw new \InvalidArgumentException('actor must be present in signed statement');
        }
        if (! isset($this->verb)) {
            throw new \InvalidArgumentException('verb must be present in signed statement');
        }
        if (! isset($this->target)) {
            throw new \InvalidArgumentException('object must be present in signed statement');
        }

        $result = $this->asVersion($version);
        $result['version'] = $version;

        foreach (['authority', 'stored'] as $prop) {
            unset($result[$prop]);
        }

        return $result;
    }

    public function sign($privateKeyFile, $privateKeyPass, $options = array()) {
        if (! isset($options['version'])) {
            $options['version'] = Version::latest();
        }
        if (! isset($options['algorithm'])) {
            $options['algorithm'] = 'RS256';
        }
        if (! isset($options['display'])) {
            $options['display'] = array(
                'en-US' => 'Statement Signature'
            );
        }
        if (! isset($options['signatureHeader'])) {
            $options['signatureHeader'] = array();
        }

        if (! in_array($options['algorithm'], array('RS256', 'RS384', 'RS512'), true)) {
            throw new \InvalidArgumentException("Invalid signing algorithm: '" . $options['algorithm'] . "'");
        }

        // serialize the statement
        $serialization = $this->serializeForSignature($options['version']);

        //
        // commands to generate required files:
        //  openssl genrsa -aes256 -out private.key 2048
        //  openssl req -new -x509 -key private.key -out cacert.pem -days 1095
        //
        $privateKey = openssl_pkey_get_private($privateKeyFile, $privateKeyPass);
        if (! $privateKey) {
            throw new \Exception('Unable to get private key: ' . openssl_error_string());
        }

        $jwsHeader = array(
            'alg' => $options['algorithm'],
            'TinCanPHP' => true
        );
        if (isset($options['signatureHeader'])) {
            array_replace($jwsHeader, $options['signatureHeader']);
        }

        if (isset($options['x5c'])) {
            $jwsHeader['x5c'] = array();

            if (! is_array($options['x5c'])) {
                $options['x5c'] = array($options['x5c']);
            }

            foreach ($options['x5c'] as $cert) {
                $cert = openssl_x509_read($cert);
                if (! $cert) {
                    throw new \Exception('Unable to read certificate for x5c inclusion: ' . openssl_error_string());
                }

                if (! openssl_x509_export($cert, $x5c, true)) {
                    throw new \Exception('Unable to export certificate for x5c inclusion: ' . openssl_error_string());
                }

                $x5c = preg_replace(
                    array(
                        "/^-----BEGIN CERTIFICATE-----\r?\n/",
                        "/-----END CERTIFICATE-----\r?\n$/",
                        "/\r?\n/"
                    ),
                    '',
                    $x5c
                );

                array_push($jwsHeader['x5c'], $x5c);
            }
        }
        $jws = new JWS($jwsHeader);

        $jws->setPayload($serialization, false);
        $jws->sign($privateKey);

        $attachment = array(
            'contentType' => self::SIGNATURE_CONTENT_TYPE,
            'usageType'   => self::SIGNATURE_USAGE_TYPE,
            'content'     => $jws->getTokenString(),
            'display'     => $options['display'],
        );
        if (isset($options['description'])) {
            $attachment['description'] = $options['description'];
        }
        $this->addAttachment($attachment);
    }

    public function verify($options = array()) {
        if (! isset($options['version'])) {
            $options['version'] = Version::latest();
        }

        $signatureAttachment = null;
        $signatureIndex = 0;

        foreach ($this->getAttachments() as $attachment) {
            if ($attachment->getUsageType() === self::SIGNATURE_USAGE_TYPE) {
                $signatureAttachment = $attachment;
                break;
            }
            $signatureIndex++;
        }
        if ($signatureAttachment === null) {
            return array('success' => false, 'reason' => "Unable to locate signature attachment (usage type)");
        }

        try {
            $jws = JWS::load($signatureAttachment->getContent());
        }
        catch (\InvalidArgumentException $e) {
            return array('success' => false, 'reason' => 'Failed to load JWS: ' . $e);
        }

        $header = $jws->getHeader();

        //
        // there is a JWS spec security issue with allowing non-RS algorithms
        // to be specified and it is against the Tin Can spec anyways so we
        // want to fail hard on non-RS algorithms
        //
        if (! in_array($header['alg'], array('RS256', 'RS384', 'RS512'), true)) {
            throw new \InvalidArgumentException("Refusing to verify signature: Invalid signing algorithm ('" . $options['algorithm'] . "')");
        }

        if (isset($options['publicKey'])) {
            $publicKeyFile = $options['publicKey'];
        }
        elseif (isset($header['x5c'])) {
            $cert = "-----BEGIN CERTIFICATE-----\r\n" . chunk_split($header['x5c'][0], 64, "\r\n") . "-----END CERTIFICATE-----\r\n";
            $cert = openssl_x509_read($cert);
            if (! $cert) {
                return array('success' => false, 'reason' => 'failed to read cert in x5c: ' . openssl_error_string());
            }
            $publicKeyFile = openssl_pkey_get_public($cert);
            if (! $publicKeyFile) {
                return array('success' => false, 'reason' => 'x5c failed to provide public key: ' . openssl_error_string());
            }
        }
        else {
            return array('success' => false, 'reason' => 'No public key found or provided for verification');
        }

        if (! $jws->verify($publicKeyFile)) {
            return array('success' => false, 'reason' => 'Failed to verify signature');
        }

        $payload = $jws->getPayload();

        //
        // serializing this statement as if it was going to be
        // made into a signature should provide us with what we
        // can expect in the payload, if the two don't match then
        // the signature isn't valid, it also gives us a clone
        // that we can then manipulate without affecting the
        // original instance
        //
        // use the version from the payload as it indicates the
        // version in use when the statement was serialized to
        // begin with
        //
        $version = $payload['version'] ? $payload['version'] : Version::latest();
        $serialization = $this->serializeForSignature($version);

        //
        // remove the signature attachment before comparing the
        // serializations, if it was the only attachment and the
        // signature doesn't include the 'attachments' property
        // then unset it as well
        //
        unset($serialization['attachments'][$signatureIndex]);
        if (count($serialization['attachments']) === 0 && ! isset($payload['attachments'])) {
            unset($serialization['attachments']);
        }

        //
        // authority and stored are most often populated by the LRS,
        // and presumably for signature purposes are *never* included
        // in the signature so we are safe to remove them here
        //
        unset($serialization['stored']);
        unset($serialization['authority']);

        //
        // the payload 'version' is instructive of how to serialize the
        // statement for comparison, that 'version' is not required and
        // when not set we need to remove the 'version' in the serialization
        // which will be the current latest supported by the library
        // which shouldn't be compared against what is in the signature
        //
        if (! isset($payload['version'])) {
            unset($serialization['version']);
        }

        //
        // a statement can be signed without having first provided an
        // id, in that case the id is set by the receiving LRS, so if
        // the serialization has one, presumably from retrieval from
        // an LRS, remove it so that it is not compared
        //
        // if the statement did provide an id before signing then the
        // LRS should have maintained that id, so they can be compared
        //
        if (! isset($payload['id'])) {
            unset($serialization['id']);
        }

        //
        // the same applies to timestamp
        //
        if (! isset($payload['timestamp'])) {
            unset($serialization['timestamp']);
        }

        //
        // now we can construct an object from both the payload and the
        // serialization of this instance and compare the two for a match
        // in meaning
        //
        $fromSerialization = new self($serialization);
        $comparison = $fromSerialization->compareWithSignature(new self($payload));
        if (! $comparison['success']) {
            return array('success' => false, 'reason' => 'Statement to signature comparison failed: ' . $comparison['reason']);
        }

        return array('success' => true, 'jws' => $jws);
    }

    public function setId($value) {
        if (isset($value) && ! preg_match(Util::UUID_REGEX, $value)) {
            throw new \InvalidArgumentException('arg1 must be a UUID "' . $value . '"');
        }
        $this->id = $value;
        return $this;
    }
    public function getId() { return $this->id; }
    public function hasId() { return isset($this->id); }

    public function setStored($value) {
        if (isset($value)) {
            if ($value instanceof \DateTime) {
                // Use format('c') instead of format(\DateTime::ISO8601) due to bug in format(\DateTime::ISO8601) that generates an invalid timestamp.
                $value = $value->format('c');
            }
            elseif (is_string($value)) {
                $value = $value;
            }
            else {
                throw new \InvalidArgumentException('type of arg1 must be string or DateTime');
            }
        }

        $this->stored = $value;

        return $this;
    }
    public function getStored() { return $this->stored; }

    public function setAuthority($value) {
        if (! $value instanceof Agent && is_array($value)) {
            $value = new Agent($value);
        }

        $this->authority = $value;

        return $this;
    }
    public function getAuthority() { return $this->authority; }

    public function setVersion($value) { $this->version = $value; return $this; }
    public function getVersion() { return $this->version; }

    public function setAttachments($value) {
        foreach ($value as $k => $v) {
            if (! $value[$k] instanceof Attachment) {
                $value[$k] = new Attachment($value[$k]);
            }
        }

        $this->attachments = $value;

        return $this;
    }
    public function getAttachments() { return $this->attachments; }
    public function hasAttachments() { return count($this->attachments) > 0; }
    public function hasAttachmentsWithContent() {
        if (! $this->hasAttachments()) {
            return false;
        }

        foreach ($this->attachments as $attachment) {
            if ($attachment->hasContent()) {
                return true;
            }
        }

        return false;
    }
    public function addAttachment($value) {
        if (! $value instanceof Attachment) {
            $value = new Attachment($value);
        }

        array_push($this->attachments, $value);

        return $this;
    }
}
