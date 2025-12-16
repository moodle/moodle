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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfig extends \Google\Collection
{
  protected $collection_key = 'contactDetails';
  protected $alertEnrollmentsType = GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfigAlertEnrollment::class;
  protected $alertEnrollmentsDataType = 'array';
  /**
   * Immutable. The fully qualified resource name of the AlertPolicy.
   *
   * @var string
   */
  public $alertPolicy;
  protected $contactDetailsType = GoogleCloudDiscoveryengineV1alphaContactDetails::class;
  protected $contactDetailsDataType = 'array';
  /**
   * Optional. The language code used for notifications
   *
   * @var string
   */
  public $languageCode;

  /**
   * Optional. The enrollment state of each alert.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfigAlertEnrollment[] $alertEnrollments
   */
  public function setAlertEnrollments($alertEnrollments)
  {
    $this->alertEnrollments = $alertEnrollments;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfigAlertEnrollment[]
   */
  public function getAlertEnrollments()
  {
    return $this->alertEnrollments;
  }
  /**
   * Immutable. The fully qualified resource name of the AlertPolicy.
   *
   * @param string $alertPolicy
   */
  public function setAlertPolicy($alertPolicy)
  {
    $this->alertPolicy = $alertPolicy;
  }
  /**
   * @return string
   */
  public function getAlertPolicy()
  {
    return $this->alertPolicy;
  }
  /**
   * Optional. The contact details for each alert policy.
   *
   * @param GoogleCloudDiscoveryengineV1alphaContactDetails[] $contactDetails
   */
  public function setContactDetails($contactDetails)
  {
    $this->contactDetails = $contactDetails;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaContactDetails[]
   */
  public function getContactDetails()
  {
    return $this->contactDetails;
  }
  /**
   * Optional. The language code used for notifications
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfig');
