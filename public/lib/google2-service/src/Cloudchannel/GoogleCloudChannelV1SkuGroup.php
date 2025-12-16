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

class GoogleCloudChannelV1SkuGroup extends \Google\Model
{
  /**
   * Unique human readable identifier for the SKU group.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource name of SKU group. Format:
   * accounts/{account}/skuGroups/{sku_group}. Example:
   * "accounts/C01234/skuGroups/3d50fd57-3157-4577-a5a9-a219b8490041".
   *
   * @var string
   */
  public $name;

  /**
   * Unique human readable identifier for the SKU group.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Resource name of SKU group. Format:
   * accounts/{account}/skuGroups/{sku_group}. Example:
   * "accounts/C01234/skuGroups/3d50fd57-3157-4577-a5a9-a219b8490041".
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1SkuGroup::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1SkuGroup');
