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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2IssueResourceApplicationAttributes extends \Google\Collection
{
  protected $collection_key = 'operatorOwners';
  protected $businessOwnersType = GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo::class;
  protected $businessOwnersDataType = 'array';
  protected $criticalityType = GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesCriticality::class;
  protected $criticalityDataType = '';
  protected $developerOwnersType = GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo::class;
  protected $developerOwnersDataType = 'array';
  protected $environmentType = GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesEnvironment::class;
  protected $environmentDataType = '';
  protected $operatorOwnersType = GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo::class;
  protected $operatorOwnersDataType = 'array';

  /**
   * Business team that ensures user needs are met and value is delivered
   *
   * @param GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo[] $businessOwners
   */
  public function setBusinessOwners($businessOwners)
  {
    $this->businessOwners = $businessOwners;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo[]
   */
  public function getBusinessOwners()
  {
    return $this->businessOwners;
  }
  /**
   * User-defined criticality information.
   *
   * @param GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesCriticality $criticality
   */
  public function setCriticality(GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesCriticality $criticality)
  {
    $this->criticality = $criticality;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesCriticality
   */
  public function getCriticality()
  {
    return $this->criticality;
  }
  /**
   * Developer team that owns development and coding.
   *
   * @param GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo[] $developerOwners
   */
  public function setDeveloperOwners($developerOwners)
  {
    $this->developerOwners = $developerOwners;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo[]
   */
  public function getDeveloperOwners()
  {
    return $this->developerOwners;
  }
  /**
   * User-defined environment information.
   *
   * @param GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesEnvironment $environment
   */
  public function setEnvironment(GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesEnvironment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesEnvironment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Operator team that ensures runtime and operations.
   *
   * @param GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo[] $operatorOwners
   */
  public function setOperatorOwners($operatorOwners)
  {
    $this->operatorOwners = $operatorOwners;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IssueResourceApplicationAttributesContactInfo[]
   */
  public function getOperatorOwners()
  {
    return $this->operatorOwners;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2IssueResourceApplicationAttributes::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2IssueResourceApplicationAttributes');
