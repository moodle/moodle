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

namespace Google\Service\OSConfig;

class OSPolicyResourceGroup extends \Google\Collection
{
  protected $collection_key = 'resources';
  protected $inventoryFiltersType = OSPolicyInventoryFilter::class;
  protected $inventoryFiltersDataType = 'array';
  protected $resourcesType = OSPolicyResource::class;
  protected $resourcesDataType = 'array';

  /**
   * List of inventory filters for the resource group. The resources in this
   * resource group are applied to the target VM if it satisfies at least one of
   * the following inventory filters. For example, to apply this resource group
   * to VMs running either `RHEL` or `CentOS` operating systems, specify 2 items
   * for the list with following values:
   * inventory_filters[0].os_short_name='rhel' and
   * inventory_filters[1].os_short_name='centos' If the list is empty, this
   * resource group will be applied to the target VM unconditionally.
   *
   * @param OSPolicyInventoryFilter[] $inventoryFilters
   */
  public function setInventoryFilters($inventoryFilters)
  {
    $this->inventoryFilters = $inventoryFilters;
  }
  /**
   * @return OSPolicyInventoryFilter[]
   */
  public function getInventoryFilters()
  {
    return $this->inventoryFilters;
  }
  /**
   * Required. List of resources configured for this resource group. The
   * resources are executed in the exact order specified here.
   *
   * @param OSPolicyResource[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return OSPolicyResource[]
   */
  public function getResources()
  {
    return $this->resources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyResourceGroup::class, 'Google_Service_OSConfig_OSPolicyResourceGroup');
