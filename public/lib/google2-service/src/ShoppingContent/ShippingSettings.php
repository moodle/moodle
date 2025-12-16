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

namespace Google\Service\ShoppingContent;

class ShippingSettings extends \Google\Collection
{
  protected $collection_key = 'warehouses';
  /**
   * The ID of the account to which these account shipping settings belong.
   * Ignored upon update, always present in get request responses.
   *
   * @var string
   */
  public $accountId;
  protected $postalCodeGroupsType = PostalCodeGroup::class;
  protected $postalCodeGroupsDataType = 'array';
  protected $servicesType = Service::class;
  protected $servicesDataType = 'array';
  protected $warehousesType = Warehouse::class;
  protected $warehousesDataType = 'array';

  /**
   * The ID of the account to which these account shipping settings belong.
   * Ignored upon update, always present in get request responses.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * A list of postal code groups that can be referred to in `services`.
   * Optional.
   *
   * @param PostalCodeGroup[] $postalCodeGroups
   */
  public function setPostalCodeGroups($postalCodeGroups)
  {
    $this->postalCodeGroups = $postalCodeGroups;
  }
  /**
   * @return PostalCodeGroup[]
   */
  public function getPostalCodeGroups()
  {
    return $this->postalCodeGroups;
  }
  /**
   * The target account's list of services. Optional.
   *
   * @param Service[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return Service[]
   */
  public function getServices()
  {
    return $this->services;
  }
  /**
   * Optional. A list of warehouses which can be referred to in `services`.
   *
   * @param Warehouse[] $warehouses
   */
  public function setWarehouses($warehouses)
  {
    $this->warehouses = $warehouses;
  }
  /**
   * @return Warehouse[]
   */
  public function getWarehouses()
  {
    return $this->warehouses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShippingSettings::class, 'Google_Service_ShoppingContent_ShippingSettings');
