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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1BillableSku extends \Google\Model
{
  /**
   * Resource name of Service which contains Repricing SKU. Format:
   * services/{service}. Example: "services/B7D9-FDCB-15D8".
   *
   * @var string
   */
  public $service;
  /**
   * Unique human readable name for the Service.
   *
   * @var string
   */
  public $serviceDisplayName;
  /**
   * Resource name of Billable SKU. Format: billableSkus/{sku}. Example:
   * billableSkus/6E1B-6634-470F".
   *
   * @var string
   */
  public $sku;
  /**
   * Unique human readable name for the SKU.
   *
   * @var string
   */
  public $skuDisplayName;

  /**
   * Resource name of Service which contains Repricing SKU. Format:
   * services/{service}. Example: "services/B7D9-FDCB-15D8".
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Unique human readable name for the Service.
   *
   * @param string $serviceDisplayName
   */
  public function setServiceDisplayName($serviceDisplayName)
  {
    $this->serviceDisplayName = $serviceDisplayName;
  }
  /**
   * @return string
   */
  public function getServiceDisplayName()
  {
    return $this->serviceDisplayName;
  }
  /**
   * Resource name of Billable SKU. Format: billableSkus/{sku}. Example:
   * billableSkus/6E1B-6634-470F".
   *
   * @param string $sku
   */
  public function setSku($sku)
  {
    $this->sku = $sku;
  }
  /**
   * @return string
   */
  public function getSku()
  {
    return $this->sku;
  }
  /**
   * Unique human readable name for the SKU.
   *
   * @param string $skuDisplayName
   */
  public function setSkuDisplayName($skuDisplayName)
  {
    $this->skuDisplayName = $skuDisplayName;
  }
  /**
   * @return string
   */
  public function getSkuDisplayName()
  {
    return $this->skuDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1BillableSku::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1BillableSku');
