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

class GoogleCloudChannelV1SkuGroupCondition extends \Google\Model
{
  /**
   * Specifies a SKU group (https://cloud.google.com/skus/sku-groups). Resource
   * name of SKU group. Format: accounts/{account}/skuGroups/{sku_group}.
   * Example: "accounts/C01234/skuGroups/3d50fd57-3157-4577-a5a9-a219b8490041".
   *
   * @var string
   */
  public $skuGroup;

  /**
   * Specifies a SKU group (https://cloud.google.com/skus/sku-groups). Resource
   * name of SKU group. Format: accounts/{account}/skuGroups/{sku_group}.
   * Example: "accounts/C01234/skuGroups/3d50fd57-3157-4577-a5a9-a219b8490041".
   *
   * @param string $skuGroup
   */
  public function setSkuGroup($skuGroup)
  {
    $this->skuGroup = $skuGroup;
  }
  /**
   * @return string
   */
  public function getSkuGroup()
  {
    return $this->skuGroup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1SkuGroupCondition::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1SkuGroupCondition');
