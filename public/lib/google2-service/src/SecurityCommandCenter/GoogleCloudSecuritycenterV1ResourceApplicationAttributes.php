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

class GoogleCloudSecuritycenterV1ResourceApplicationAttributes extends \Google\Collection
{
  protected $collection_key = 'operatorOwners';
  protected $businessOwnersType = GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo::class;
  protected $businessOwnersDataType = 'array';
  protected $criticalityType = GoogleCloudSecuritycenterV1ResourceApplicationAttributesCriticality::class;
  protected $criticalityDataType = '';
  protected $developerOwnersType = GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo::class;
  protected $developerOwnersDataType = 'array';
  protected $environmentType = GoogleCloudSecuritycenterV1ResourceApplicationAttributesEnvironment::class;
  protected $environmentDataType = '';
  protected $operatorOwnersType = GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo::class;
  protected $operatorOwnersDataType = 'array';

  /**
   * Business team that ensures user needs are met and value is delivered
   *
   * @param GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo[] $businessOwners
   */
  public function setBusinessOwners($businessOwners)
  {
    $this->businessOwners = $businessOwners;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo[]
   */
  public function getBusinessOwners()
  {
    return $this->businessOwners;
  }
  /**
   * User-defined criticality information.
   *
   * @param GoogleCloudSecuritycenterV1ResourceApplicationAttributesCriticality $criticality
   */
  public function setCriticality(GoogleCloudSecuritycenterV1ResourceApplicationAttributesCriticality $criticality)
  {
    $this->criticality = $criticality;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ResourceApplicationAttributesCriticality
   */
  public function getCriticality()
  {
    return $this->criticality;
  }
  /**
   * Developer team that owns development and coding.
   *
   * @param GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo[] $developerOwners
   */
  public function setDeveloperOwners($developerOwners)
  {
    $this->developerOwners = $developerOwners;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo[]
   */
  public function getDeveloperOwners()
  {
    return $this->developerOwners;
  }
  /**
   * User-defined environment information.
   *
   * @param GoogleCloudSecuritycenterV1ResourceApplicationAttributesEnvironment $environment
   */
  public function setEnvironment(GoogleCloudSecuritycenterV1ResourceApplicationAttributesEnvironment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ResourceApplicationAttributesEnvironment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Operator team that ensures runtime and operations.
   *
   * @param GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo[] $operatorOwners
   */
  public function setOperatorOwners($operatorOwners)
  {
    $this->operatorOwners = $operatorOwners;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ResourceApplicationAttributesContactInfo[]
   */
  public function getOperatorOwners()
  {
    return $this->operatorOwners;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV1ResourceApplicationAttributes::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1ResourceApplicationAttributes');
