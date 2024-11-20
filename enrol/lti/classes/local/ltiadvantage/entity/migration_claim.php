<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace enrol_lti\local\ltiadvantage\entity;

use enrol_lti\local\ltiadvantage\repository\legacy_consumer_repository;

/**
 * The migration_claim class, instances of which represent information passed in an 'lti1p1' migration claim.
 *
 * Provides validation and data retrieval for the claim.
 *
 * See https://www.imsglobal.org/spec/lti/v1p3/migr#lti-1-1-migration-claim
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration_claim {

    /** @var string the LTI 1.1 consumer key */
    private $consumerkey;

    /** @var string the LTI 1.1 user identifier.
     * This is only included in the claim if it differs to the value included in the LTI 1.3 'sub' claim.
     * I.e. https://www.imsglobal.org/spec/security/v1p0#id-token
     */
    private $userid = null;

    /** @var string the LTI 1.1 context identifier.
     * This is only included in the claim if it differs to the 'id' property of the LTI 1.3 'context' claim.
     * I.e. https://purl.imsglobal.org/spec/lti/claim/context#id.
     */
    private $contextid = null;

    /** @var string the LTI 1.1 consumer instance GUID.
     * This is only included in the claim if it differs to the 'guid' property of the LTI 1.3 'tool_platform' claim.
     * I.e. https://purl.imsglobal.org/spec/lti/claim/tool_platform#guid.
     */
    private $toolconsumerinstanceguid = null;

    /** @var string the LTI 1.1 resource link identifier.
     * This is only included in the claim if it differs to the 'id' property of the LTI 1.3 'resource_link' claim.
     * I.e. https://purl.imsglobal.org/spec/lti/claim/resource_link#id.
     */
    private $resourcelinkid = null;

    /** @var legacy_consumer_repository repository instance for querying consumer secrets when verifying signature. */
    private $legacyconsumerrepo;

    /**
     * The migration_claim constructor.
     *
     * @param array $claim the array of claim data, as received in a resource link launch.
     * @param string $deploymentid the deployment id included in the launch.
     * @param string $platform the platform included in the launch.
     * @param string $clientid the client id included in the launch.
     * @param string $exp the exp included in the launch.
     * @param string $nonce the nonce included in the launch.
     * @param legacy_consumer_repository $legacyconsumerrepo  a legacy consumer repository instance.
     * @throws \coding_exception if the claim data is invalid.
     */
    public function __construct(array $claim, string $deploymentid, string $platform, string $clientid, string $exp,
            string $nonce, legacy_consumer_repository $legacyconsumerrepo) {

        // The oauth_consumer_key property MUST be sent.
        // See: https://www.imsglobal.org/spec/lti/v1p3/migr#oauth_consumer_key.
        if (empty($claim['oauth_consumer_key'])) {
            throw new \coding_exception("Missing 'oauth_consumer_key' property in lti1p1 migration claim.");
        }

        // The oauth_consumer_key_sign property MAY be sent.
        // For user migration to take place, however, this is deemed a required property.
        // See: https://www.imsglobal.org/spec/lti/v1p3/migr#oauth_consumer_key_sign.
        if (empty($claim['oauth_consumer_key_sign'])) {
            throw new \coding_exception("Missing 'oauth_consumer_key_sign' property in lti1p1 migration claim.");
        }
        $this->legacyconsumerrepo = $legacyconsumerrepo;

        if (!$this->verify_signature($claim['oauth_consumer_key'], $claim['oauth_consumer_key_sign'], $deploymentid,
                $platform, $clientid, $exp, $nonce, $legacyconsumerrepo)) {
            throw new \coding_exception("Invalid 'oauth_consumer_key_sign' signature in lti1p1 claim.");
        }

        $this->consumerkey = $claim['oauth_consumer_key'];
        $this->userid = $claim['user_id'] ?? null;
        $this->contextid = $claim['context_id'] ?? null;
        $this->toolconsumerinstanceguid = $claim['tool_consumer_instance_guid'] ?? null;
        $this->resourcelinkid = $claim['resource_link_id'] ?? null;
    }

    /**
     * Verify the claim signature by recalculating it using the launch data and locally stored consumer secret.
     *
     * @param string $consumerkey the LTI 1.1 consumer key.
     * @param string $signature a signature of the LTI 1.1 consumer key and associated launch data.
     * @param string $deploymentid the deployment id included in the launch.
     * @param string $platform the platform included in the launch.
     * @param string $clientid the client id included in the launch.
     * @param string $exp the exp included in the launch.
     * @param string $nonce the nonce included in the launch.
     * @return bool true if the signature was verified, false otherwise.
     */
    private function verify_signature(string $consumerkey, string $signature, string $deploymentid, string $platform,
        string $clientid, string $exp, string $nonce): bool {

        $base = [
            $consumerkey,
            $deploymentid,
            $platform,
            $clientid,
            $exp,
            $nonce
        ];
        $basestring = implode('&', $base);

        // Legacy enrol_lti code permits tools to share a consumer key but use different secrets. This results in
        // potentially many secrets per mapped tool consumer. As such, when generating the migration claim it's
        // impossible to know which secret the platform will use to sign the consumer key. The consumer key in the
        // migration claim is thus verified by trying all known secrets for the consumer, until either a match is found
        // or no signatures match.
        $consumersecrets = $this->legacyconsumerrepo->get_consumer_secrets($consumerkey);
        foreach ($consumersecrets as $consumersecret) {
            $calculatedsignature = base64_encode(hash_hmac('sha256', $basestring, $consumersecret));

            if ($signature === $calculatedsignature) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return the consumer key stored in the claim.
     *
     * @return string the consumer key included in the claim.
     */
    public function get_consumer_key(): string {
        return $this->consumerkey;
    }

    /**
     * Return the LTI 1.1 user id stored in the claim.
     *
     * @return string|null the user id, or null if not provided in the claim.
     */
    public function get_user_id(): ?string {
        return $this->userid;
    }


    /**
     * Return the LTI 1.1 context id stored in the claim.
     *
     * @return string|null the context id, or null if not provided in the claim.
     */
    public function get_context_id(): ?string {
        return $this->contextid;
    }

    /**
     * Return the LTI 1.1 tool consumer instance GUID stored in the claim.
     *
     * @return string|null the tool consumer instance GUID, or null if not provided in the claim.
     */
    public function get_tool_consumer_instance_guid(): ?string {
        return $this->toolconsumerinstanceguid;
    }

    /**
     * Return the LTI 1.1 resource link id stored in the claim.
     *
     * @return string|null the resource link id, or null if not provided in the claim.
     */
    public function get_resource_link_id(): ?string {
        return $this->resourcelinkid;
    }
}
