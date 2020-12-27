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

/**
 * Contains helper class to work with PayPal REST API.
 *
 * @package    core_payment
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_paypal;

use curl;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

class paypal_helper {

    /**
     * @var string The payment was authorized or the authorized payment was captured for the order.
     */
    public const CAPTURE_STATUS_COMPLETED = 'COMPLETED';

    /**
     * @var string The merchant intends to capture payment immediately after the customer makes a payment.
     */
    public const ORDER_INTENT_CAPTURE = 'CAPTURE';

    /**
     * @var string The customer approved the payment.
     */
    public const ORDER_STATUS_APPROVED = 'APPROVED';

    /**
     * @var string The base API URL
     */
    private $baseurl;

    /**
     * @var string Client ID
     */
    private $clientid;

    /**
     * @var string PayPal App secret
     */
    private $secret;

    /**
     * @var string The oath bearer token
     */
    private $token;

    /**
     * helper constructor.
     *
     * @param string $clientid The client id.
     * @param string $secret PayPal secret.
     * @param bool $sandbox Whether we are working with the sandbox environment or not.
     */
    public function __construct(string $clientid, string $secret, bool $sandbox) {
        $this->clientid = $clientid;
        $this->secret = $secret;
        $this->baseurl = $sandbox ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

        $this->token = $this->get_token();
    }

    /**
     * Captures an authorized payment, by ID.
     *
     * @param string $authorizationid The PayPal-generated ID for the authorized payment to capture.
     * @param float $amount The amount to capture.
     * @param string $currency The currency code for the amount.
     * @param bool $final Indicates whether this is the final captures against the authorized payment.
     * @return array|null Formatted API response.
     */
    public function capture_authorization(string $authorizationid, float $amount, string $currency, bool $final = true): ?array {
        $location = "{$this->baseurl}/v2/payments/authorizations/{$authorizationid}/capture";

        $options = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 30,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
            'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
            'CURLOPT_HTTPHEADER' => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->token}",
            ],
        ];

        $command = [
            'amount' => [
                'value' => (string) $amount,
                'currency_code' => $currency,
            ],
            'final_capture' => $final,
        ];
        $command = json_encode($command);

        $curl = new curl();
        $result = $curl->post($location, $command, $options);

        return json_decode($result, true);
    }

    /**
     * Captures order details from PayPal.
     *
     * @param string $orderid The order we want to capture.
     * @return array|null Formatted API response.
     */
    public function capture_order(string $orderid): ?array {
        $location = "{$this->baseurl}/v2/checkout/orders/{$orderid}/capture";

        $options = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 30,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
            'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
            'CURLOPT_HTTPHEADER' => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->token}",
            ],
        ];

        $command = '{}';

        $curl = new curl();
        $result = $curl->post($location, $command, $options);

        return json_decode($result, true);
    }

    public function get_order_details(string $orderid): ?array {
        $location = "{$this->baseurl}/v2/checkout/orders/{$orderid}";

        $options = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 30,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
            'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
            'CURLOPT_HTTPHEADER' => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->token}",
            ],
        ];

        $curl = new curl();
        $result = $curl->get($location, [], $options);

        return json_decode($result, true);
    }

    /**
     * Request for PayPal REST oath bearer token.
     *
     * @return string
     */
    private function get_token(): string {
        $location = "{$this->baseurl}/v1/oauth2/token";

        $options = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 30,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
            'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
            'CURLOPT_USERPWD' => "{$this->clientid}:{$this->secret}",
        ];

        $command = 'grant_type=client_credentials';

        $curl = new curl();
        $result = $curl->post($location, $command, $options);

        $result = json_decode($result, true);

        return $result['access_token'];
    }
}