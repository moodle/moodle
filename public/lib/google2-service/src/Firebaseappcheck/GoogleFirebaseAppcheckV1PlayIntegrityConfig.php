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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1PlayIntegrityConfig extends \Google\Model
{
  protected $accountDetailsType = GoogleFirebaseAppcheckV1PlayIntegrityConfigAccountDetails::class;
  protected $accountDetailsDataType = '';
  protected $appIntegrityType = GoogleFirebaseAppcheckV1PlayIntegrityConfigAppIntegrity::class;
  protected $appIntegrityDataType = '';
  protected $deviceIntegrityType = GoogleFirebaseAppcheckV1PlayIntegrityConfigDeviceIntegrity::class;
  protected $deviceIntegrityDataType = '';
  /**
   * Required. The relative resource name of the Play Integrity configuration
   * object, in the format: ```
   * projects/{project_number}/apps/{app_id}/playIntegrityConfig ```
   *
   * @var string
   */
  public $name;
  /**
   * Specifies the duration for which App Check tokens exchanged from Play
   * Integrity tokens will be valid. If unset, a default value of 1 hour is
   * assumed. Must be between 30 minutes and 7 days, inclusive.
   *
   * @var string
   */
  public $tokenTtl;

  /**
   * Specifies account requirements for Android devices running your app. These
   * settings correspond to requirements on the [**account details** field](http
   * s://developer.android.com/google/play/integrity/verdicts#account-details-
   * field) obtained from the Play Integrity API. See the [default responses
   * table](https://developer.android.com/google/play/integrity/setup#default)
   * for a quick summary. The default values for these settings work for most
   * apps, and are recommended.
   *
   * @param GoogleFirebaseAppcheckV1PlayIntegrityConfigAccountDetails $accountDetails
   */
  public function setAccountDetails(GoogleFirebaseAppcheckV1PlayIntegrityConfigAccountDetails $accountDetails)
  {
    $this->accountDetails = $accountDetails;
  }
  /**
   * @return GoogleFirebaseAppcheckV1PlayIntegrityConfigAccountDetails
   */
  public function getAccountDetails()
  {
    return $this->accountDetails;
  }
  /**
   * Specifies application integrity requirements for Android devices running
   * your app. These settings correspond to requirements on the [**application
   * integrity** field](https://developer.android.com/google/play/integrity/verd
   * icts#application-integrity-field) obtained from the Play Integrity API. See
   * the [default responses
   * table](https://developer.android.com/google/play/integrity/setup#default)
   * for a quick summary. The default values for these settings work for most
   * apps, and are recommended.
   *
   * @param GoogleFirebaseAppcheckV1PlayIntegrityConfigAppIntegrity $appIntegrity
   */
  public function setAppIntegrity(GoogleFirebaseAppcheckV1PlayIntegrityConfigAppIntegrity $appIntegrity)
  {
    $this->appIntegrity = $appIntegrity;
  }
  /**
   * @return GoogleFirebaseAppcheckV1PlayIntegrityConfigAppIntegrity
   */
  public function getAppIntegrity()
  {
    return $this->appIntegrity;
  }
  /**
   * Specifies device integrity requirements for Android devices running your
   * app. These settings correspond to requirements on the [**device integrity**
   * field](https://developer.android.com/google/play/integrity/verdicts#device-
   * integrity-field) obtained from the Play Integrity API. See the [default
   * responses
   * table](https://developer.android.com/google/play/integrity/setup#default)
   * for a quick summary. Warning: There are also [conditional](https://develope
   * r.android.com/google/play/integrity/setup#conditional) as well as [optional
   * ](https://developer.android.com/google/play/integrity/setup#optional_device
   * _information) responses that you can receive, but requires additional
   * explicit opt-in from you. The App Check API is **not** responsible for any
   * such opt-ins. The default values for these settings work for most apps, and
   * are recommended.
   *
   * @param GoogleFirebaseAppcheckV1PlayIntegrityConfigDeviceIntegrity $deviceIntegrity
   */
  public function setDeviceIntegrity(GoogleFirebaseAppcheckV1PlayIntegrityConfigDeviceIntegrity $deviceIntegrity)
  {
    $this->deviceIntegrity = $deviceIntegrity;
  }
  /**
   * @return GoogleFirebaseAppcheckV1PlayIntegrityConfigDeviceIntegrity
   */
  public function getDeviceIntegrity()
  {
    return $this->deviceIntegrity;
  }
  /**
   * Required. The relative resource name of the Play Integrity configuration
   * object, in the format: ```
   * projects/{project_number}/apps/{app_id}/playIntegrityConfig ```
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
   * Specifies the duration for which App Check tokens exchanged from Play
   * Integrity tokens will be valid. If unset, a default value of 1 hour is
   * assumed. Must be between 30 minutes and 7 days, inclusive.
   *
   * @param string $tokenTtl
   */
  public function setTokenTtl($tokenTtl)
  {
    $this->tokenTtl = $tokenTtl;
  }
  /**
   * @return string
   */
  public function getTokenTtl()
  {
    return $this->tokenTtl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1PlayIntegrityConfig::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1PlayIntegrityConfig');
