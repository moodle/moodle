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

namespace auth_lti\local\ltiadvantage\entity;

/**
 * A simplified representation of a 'https://purl.imsglobal.org/spec/lti/claim/lti1p1' migration claim.
 *
 * This serves the purpose of migrating a legacy user account only. Claim properties that do not relate to user migration are not
 * included or handled by this representation.
 *
 * See https://www.imsglobal.org/spec/lti/v1p3/migr#lti-1-1-migration-claim
 *
 * @package auth_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_migration_claim {

    /** @var string the LTI 1.1 consumer key */
    private $consumerkey;

    /** @var string the LTI 1.1 user identifier.
     * This is only included in the claim if it differs to the value included in the LTI 1.3 'sub' claim.
     * If not included, the value will be taken from 'sub'.
     */
    private $userid;

    /**
     * The migration_claim constructor.
     *
     * The signature of a migration claim must be verifiable. To achieve this, the constructor takes a list of secrets
     * corresponding to the 'oauth_consumer_key' provided in the 'https://purl.imsglobal.org/spec/lti/claim/lti1p1'
     * claim. How these secrets are determined is not the responsibility of this class. The constructor assumes these
     * correspond.
     *
     * @param array $jwt the array of claim data, as received in a resource link launch JWT.
     * @param array $consumersecrets a list of consumer secrets for the consumerkey included in the migration claim.
     * @throws \coding_exception if the claim data is invalid.
     */
    public function __construct(array $jwt, array $consumersecrets) {
        // Can't get a claim instance without the claim data.
        if (empty($jwt['https://purl.imsglobal.org/spec/lti/claim/lti1p1'])) {
            throw new \coding_exception("Missing the 'https://purl.imsglobal.org/spec/lti/claim/lti1p1' JWT claim");
        }
        $claim = $jwt['https://purl.imsglobal.org/spec/lti/claim/lti1p1'];

        // The oauth_consumer_key property MUST be sent.
        // See: https://www.imsglobal.org/spec/lti/v1p3/migr#oauth_consumer_key.
        if (empty($claim['oauth_consumer_key'])) {
            throw new \coding_exception("Missing 'oauth_consumer_key' property in lti1p1 migration claim.");
        }

        // The oauth_consumer_key_sign property MAY be sent.
        // For user migration to take place, however, this is deemed a required property since Moodle identified its
        // legacy users through a combination of consumerkey and userid.
        // See: https://www.imsglobal.org/spec/lti/v1p3/migr#oauth_consumer_key_sign.
        if (empty($claim['oauth_consumer_key_sign'])) {
            throw new \coding_exception("Missing 'oauth_consumer_key_sign' property in lti1p1 migration claim.");
        }

        if (!$this->verify_signature(
            $claim['oauth_consumer_key'],
            $claim['oauth_consumer_key_sign'],
            $jwt['https://purl.imsglobal.org/spec/lti/claim/deployment_id'],
            $jwt['iss'],
            $jwt['aud'],
            $jwt['exp'],
            $jwt['nonce'],
            $consumersecrets
        )) {
            throw new \coding_exception("Invalid 'oauth_consumer_key_sign' signature in lti1p1 claim.");
        }

        $this->consumerkey = $claim['oauth_consumer_key'];
        $this->userid = $claim['user_id'] ?? $jwt['sub'];
    }

    /**
     * Verify the claim signature by recalculating it using the launch data and cross-checking consumer secrets.
     *
     * @param string $consumerkey the LTI 1.1 consumer key.
     * @param string $signature a signature of the LTI 1.1 consumer key and associated launch data.
     * @param string $deploymentid the deployment id included in the launch.
     * @param string $platform the platform included in the launch.
     * @param string $clientid the client id included in the launch.
     * @param string $exp the exp included in the launch.
     * @param string $nonce the nonce included in the launch.
     * @param array $consumersecrets the list of consumer secrets used with the given $consumerkey param
     * @return bool true if the signature was verified, false otherwise.
     */
    private function verify_signature(string $consumerkey, string $signature, string $deploymentid, string $platform,
        string $clientid, string $exp, string $nonce, array $consumersecrets): bool {

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
     * @return string the user id, or null if not provided in the claim.
     */
    public function get_user_id(): string {
        return $this->userid;
    }
}
