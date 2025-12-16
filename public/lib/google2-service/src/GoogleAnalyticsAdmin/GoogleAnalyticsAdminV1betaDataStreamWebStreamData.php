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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaDataStreamWebStreamData extends \Google\Model
{
  /**
   * Domain name of the web app being measured, or empty. Example:
   * "http://www.google.com", "https://www.google.com"
   *
   * @var string
   */
  public $defaultUri;
  /**
   * Output only. ID of the corresponding web app in Firebase, if any. This ID
   * can change if the web app is deleted and recreated.
   *
   * @var string
   */
  public $firebaseAppId;
  /**
   * Output only. Analytics Measurement ID. Example: "G-1A2BCD345E"
   *
   * @var string
   */
  public $measurementId;

  /**
   * Domain name of the web app being measured, or empty. Example:
   * "http://www.google.com", "https://www.google.com"
   *
   * @param string $defaultUri
   */
  public function setDefaultUri($defaultUri)
  {
    $this->defaultUri = $defaultUri;
  }
  /**
   * @return string
   */
  public function getDefaultUri()
  {
    return $this->defaultUri;
  }
  /**
   * Output only. ID of the corresponding web app in Firebase, if any. This ID
   * can change if the web app is deleted and recreated.
   *
   * @param string $firebaseAppId
   */
  public function setFirebaseAppId($firebaseAppId)
  {
    $this->firebaseAppId = $firebaseAppId;
  }
  /**
   * @return string
   */
  public function getFirebaseAppId()
  {
    return $this->firebaseAppId;
  }
  /**
   * Output only. Analytics Measurement ID. Example: "G-1A2BCD345E"
   *
   * @param string $measurementId
   */
  public function setMeasurementId($measurementId)
  {
    $this->measurementId = $measurementId;
  }
  /**
   * @return string
   */
  public function getMeasurementId()
  {
    return $this->measurementId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaDataStreamWebStreamData::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaDataStreamWebStreamData');
