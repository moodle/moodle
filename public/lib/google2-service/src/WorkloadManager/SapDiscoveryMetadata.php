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

namespace Google\Service\WorkloadManager;

class SapDiscoveryMetadata extends \Google\Model
{
  /**
   * Optional. Customer region string for customer's use. Does not represent GCP
   * region.
   *
   * @var string
   */
  public $customerRegion;
  /**
   * Optional. Customer defined, something like "E-commerce pre prod"
   *
   * @var string
   */
  public $definedSystem;
  /**
   * Optional. Should be "prod", "QA", "dev", "staging", etc.
   *
   * @var string
   */
  public $environmentType;
  /**
   * Optional. This SAP product name
   *
   * @var string
   */
  public $sapProduct;

  /**
   * Optional. Customer region string for customer's use. Does not represent GCP
   * region.
   *
   * @param string $customerRegion
   */
  public function setCustomerRegion($customerRegion)
  {
    $this->customerRegion = $customerRegion;
  }
  /**
   * @return string
   */
  public function getCustomerRegion()
  {
    return $this->customerRegion;
  }
  /**
   * Optional. Customer defined, something like "E-commerce pre prod"
   *
   * @param string $definedSystem
   */
  public function setDefinedSystem($definedSystem)
  {
    $this->definedSystem = $definedSystem;
  }
  /**
   * @return string
   */
  public function getDefinedSystem()
  {
    return $this->definedSystem;
  }
  /**
   * Optional. Should be "prod", "QA", "dev", "staging", etc.
   *
   * @param string $environmentType
   */
  public function setEnvironmentType($environmentType)
  {
    $this->environmentType = $environmentType;
  }
  /**
   * @return string
   */
  public function getEnvironmentType()
  {
    return $this->environmentType;
  }
  /**
   * Optional. This SAP product name
   *
   * @param string $sapProduct
   */
  public function setSapProduct($sapProduct)
  {
    $this->sapProduct = $sapProduct;
  }
  /**
   * @return string
   */
  public function getSapProduct()
  {
    return $this->sapProduct;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryMetadata::class, 'Google_Service_WorkloadManager_SapDiscoveryMetadata');
