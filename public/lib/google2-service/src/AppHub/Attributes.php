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

namespace Google\Service\AppHub;

class Attributes extends \Google\Collection
{
  protected $collection_key = 'operatorOwners';
  protected $businessOwnersType = ContactInfo::class;
  protected $businessOwnersDataType = 'array';
  protected $criticalityType = Criticality::class;
  protected $criticalityDataType = '';
  protected $developerOwnersType = ContactInfo::class;
  protected $developerOwnersDataType = 'array';
  protected $environmentType = Environment::class;
  protected $environmentDataType = '';
  protected $operatorOwnersType = ContactInfo::class;
  protected $operatorOwnersDataType = 'array';

  /**
   * Optional. Business team that ensures user needs are met and value is
   * delivered
   *
   * @param ContactInfo[] $businessOwners
   */
  public function setBusinessOwners($businessOwners)
  {
    $this->businessOwners = $businessOwners;
  }
  /**
   * @return ContactInfo[]
   */
  public function getBusinessOwners()
  {
    return $this->businessOwners;
  }
  /**
   * Optional. User-defined criticality information.
   *
   * @param Criticality $criticality
   */
  public function setCriticality(Criticality $criticality)
  {
    $this->criticality = $criticality;
  }
  /**
   * @return Criticality
   */
  public function getCriticality()
  {
    return $this->criticality;
  }
  /**
   * Optional. Developer team that owns development and coding.
   *
   * @param ContactInfo[] $developerOwners
   */
  public function setDeveloperOwners($developerOwners)
  {
    $this->developerOwners = $developerOwners;
  }
  /**
   * @return ContactInfo[]
   */
  public function getDeveloperOwners()
  {
    return $this->developerOwners;
  }
  /**
   * Optional. User-defined environment information.
   *
   * @param Environment $environment
   */
  public function setEnvironment(Environment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return Environment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Optional. Operator team that ensures runtime and operations.
   *
   * @param ContactInfo[] $operatorOwners
   */
  public function setOperatorOwners($operatorOwners)
  {
    $this->operatorOwners = $operatorOwners;
  }
  /**
   * @return ContactInfo[]
   */
  public function getOperatorOwners()
  {
    return $this->operatorOwners;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attributes::class, 'Google_Service_AppHub_Attributes');
