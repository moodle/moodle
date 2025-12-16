<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Firebaseappcheck\Resource;

use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1AppCheckToken;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1ExchangeAppAttestAssertionRequest;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1ExchangeAppAttestAttestationRequest;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1ExchangeAppAttestAttestationResponse;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1ExchangeDebugTokenRequest;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1GenerateAppAttestChallengeRequest;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1GenerateAppAttestChallengeResponse;

/**
 * The "oauthClients" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseappcheckService = new Google\Service\Firebaseappcheck(...);
 *   $oauthClients = $firebaseappcheckService->oauthClients;
 *  </code>
 */
class OauthClients extends \Google\Service\Resource
{
  /**
   * Accepts an App Attest assertion and an artifact previously obtained from
   * ExchangeAppAttestAttestation and verifies those with Apple. If valid, returns
   * an AppCheckToken. (oauthClients.exchangeAppAttestAssertion)
   *
   * @param string $app Required. The relative resource name of the iOS app, in
   * the format: ``` projects/{project_number}/apps/{app_id} ``` If necessary, the
   * `project_number` element can be replaced with the project ID of the Firebase
   * project. Learn more about using project identifiers in Google's [AIP
   * 2510](https://google.aip.dev/cloud/2510) standard.
   * @param GoogleFirebaseAppcheckV1ExchangeAppAttestAssertionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppcheckV1AppCheckToken
   * @throws \Google\Service\Exception
   */
  public function exchangeAppAttestAssertion($app, GoogleFirebaseAppcheckV1ExchangeAppAttestAssertionRequest $postBody, $optParams = [])
  {
    $params = ['app' => $app, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exchangeAppAttestAssertion', [$params], GoogleFirebaseAppcheckV1AppCheckToken::class);
  }
  /**
   * Accepts an App Attest CBOR attestation and verifies it with Apple using your
   * preconfigured team and bundle IDs. If valid, returns an attestation artifact
   * that can later be exchanged for an AppCheckToken using
   * ExchangeAppAttestAssertion. For convenience and performance, this method's
   * response object will also contain an AppCheckToken (if the verification is
   * successful). (oauthClients.exchangeAppAttestAttestation)
   *
   * @param string $app Required. The relative resource name of the iOS app, in
   * the format: ``` projects/{project_number}/apps/{app_id} ``` If necessary, the
   * `project_number` element can be replaced with the project ID of the Firebase
   * project. Learn more about using project identifiers in Google's [AIP
   * 2510](https://google.aip.dev/cloud/2510) standard.
   * @param GoogleFirebaseAppcheckV1ExchangeAppAttestAttestationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppcheckV1ExchangeAppAttestAttestationResponse
   * @throws \Google\Service\Exception
   */
  public function exchangeAppAttestAttestation($app, GoogleFirebaseAppcheckV1ExchangeAppAttestAttestationRequest $postBody, $optParams = [])
  {
    $params = ['app' => $app, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exchangeAppAttestAttestation', [$params], GoogleFirebaseAppcheckV1ExchangeAppAttestAttestationResponse::class);
  }
  /**
   * Validates a debug token secret that you have previously created using
   * CreateDebugToken. If valid, returns an AppCheckToken. Note that a restrictive
   * quota is enforced on this method to prevent accidental exposure of the app to
   * abuse. (oauthClients.exchangeDebugToken)
   *
   * @param string $app Required. The relative resource name of the app, in the
   * format: ``` projects/{project_number}/apps/{app_id} ``` If necessary, the
   * `project_number` element can be replaced with the project ID of the Firebase
   * project. Learn more about using project identifiers in Google's [AIP
   * 2510](https://google.aip.dev/cloud/2510) standard.
   * @param GoogleFirebaseAppcheckV1ExchangeDebugTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppcheckV1AppCheckToken
   * @throws \Google\Service\Exception
   */
  public function exchangeDebugToken($app, GoogleFirebaseAppcheckV1ExchangeDebugTokenRequest $postBody, $optParams = [])
  {
    $params = ['app' => $app, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exchangeDebugToken', [$params], GoogleFirebaseAppcheckV1AppCheckToken::class);
  }
  /**
   * Generates a challenge that protects the integrity of an immediately following
   * call to ExchangeAppAttestAttestation or ExchangeAppAttestAssertion. A
   * challenge should not be reused for multiple calls.
   * (oauthClients.generateAppAttestChallenge)
   *
   * @param string $app Required. The relative resource name of the iOS app, in
   * the format: ``` projects/{project_number}/apps/{app_id} ``` If necessary, the
   * `project_number` element can be replaced with the project ID of the Firebase
   * project. Learn more about using project identifiers in Google's [AIP
   * 2510](https://google.aip.dev/cloud/2510) standard.
   * @param GoogleFirebaseAppcheckV1GenerateAppAttestChallengeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppcheckV1GenerateAppAttestChallengeResponse
   * @throws \Google\Service\Exception
   */
  public function generateAppAttestChallenge($app, GoogleFirebaseAppcheckV1GenerateAppAttestChallengeRequest $postBody, $optParams = [])
  {
    $params = ['app' => $app, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateAppAttestChallenge', [$params], GoogleFirebaseAppcheckV1GenerateAppAttestChallengeResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OauthClients::class, 'Google_Service_Firebaseappcheck_Resource_OauthClients');
