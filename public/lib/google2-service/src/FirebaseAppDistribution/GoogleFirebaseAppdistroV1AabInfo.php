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

namespace Google\Service\FirebaseAppDistribution;

class GoogleFirebaseAppdistroV1AabInfo extends \Google\Model
{
  /**
   * Aab integration state unspecified
   */
  public const INTEGRATION_STATE_AAB_INTEGRATION_STATE_UNSPECIFIED = 'AAB_INTEGRATION_STATE_UNSPECIFIED';
  /**
   * App can receive app bundle uploads
   */
  public const INTEGRATION_STATE_INTEGRATED = 'INTEGRATED';
  /**
   * Firebase project is not linked to a Play developer account
   */
  public const INTEGRATION_STATE_PLAY_ACCOUNT_NOT_LINKED = 'PLAY_ACCOUNT_NOT_LINKED';
  /**
   * There is no app in linked Play developer account with the same bundle id
   */
  public const INTEGRATION_STATE_NO_APP_WITH_GIVEN_BUNDLE_ID_IN_PLAY_ACCOUNT = 'NO_APP_WITH_GIVEN_BUNDLE_ID_IN_PLAY_ACCOUNT';
  /**
   * The app in Play developer account is not in a published state
   */
  public const INTEGRATION_STATE_APP_NOT_PUBLISHED = 'APP_NOT_PUBLISHED';
  /**
   * Play App status is unavailable
   */
  public const INTEGRATION_STATE_AAB_STATE_UNAVAILABLE = 'AAB_STATE_UNAVAILABLE';
  /**
   * Play IAS terms not accepted
   */
  public const INTEGRATION_STATE_PLAY_IAS_TERMS_NOT_ACCEPTED = 'PLAY_IAS_TERMS_NOT_ACCEPTED';
  /**
   * App bundle integration state. Only valid for android apps.
   *
   * @var string
   */
  public $integrationState;
  /**
   * The name of the `AabInfo` resource. Format:
   * `projects/{project_number}/apps/{app}/aabInfo`
   *
   * @var string
   */
  public $name;
  protected $testCertificateType = GoogleFirebaseAppdistroV1TestCertificate::class;
  protected $testCertificateDataType = '';

  /**
   * App bundle integration state. Only valid for android apps.
   *
   * Accepted values: AAB_INTEGRATION_STATE_UNSPECIFIED, INTEGRATED,
   * PLAY_ACCOUNT_NOT_LINKED, NO_APP_WITH_GIVEN_BUNDLE_ID_IN_PLAY_ACCOUNT,
   * APP_NOT_PUBLISHED, AAB_STATE_UNAVAILABLE, PLAY_IAS_TERMS_NOT_ACCEPTED
   *
   * @param self::INTEGRATION_STATE_* $integrationState
   */
  public function setIntegrationState($integrationState)
  {
    $this->integrationState = $integrationState;
  }
  /**
   * @return self::INTEGRATION_STATE_*
   */
  public function getIntegrationState()
  {
    return $this->integrationState;
  }
  /**
   * The name of the `AabInfo` resource. Format:
   * `projects/{project_number}/apps/{app}/aabInfo`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * App bundle test certificate generated for the app. Set after the first app
   * bundle is uploaded for this app.
   *
   * @param GoogleFirebaseAppdistroV1TestCertificate $testCertificate
   */
  public function setTestCertificate(GoogleFirebaseAppdistroV1TestCertificate $testCertificate)
  {
    $this->testCertificate = $testCertificate;
  }
  /**
   * @return GoogleFirebaseAppdistroV1TestCertificate
   */
  public function getTestCertificate()
  {
    return $this->testCertificate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppdistroV1AabInfo::class, 'Google_Service_FirebaseAppDistribution_GoogleFirebaseAppdistroV1AabInfo');
