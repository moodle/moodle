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

namespace Google\Service\CloudAsset;

class ResourceSelector extends \Google\Model
{
  /**
   * Required. The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of a resource of [supported resource
   * types](https://cloud.google.com/asset-inventory/docs/supported-asset-
   * types#analyzable_asset_types).
   *
   * @var string
   */
  public $fullResourceName;

  /**
   * Required. The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of a resource of [supported resource
   * types](https://cloud.google.com/asset-inventory/docs/supported-asset-
   * types#analyzable_asset_types).
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceSelector::class, 'Google_Service_CloudAsset_ResourceSelector');
