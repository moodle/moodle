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

namespace Google\Service\SA360\Resource;

use Google\Service\SA360\GoogleAdsSearchads360V0ResourcesCustomColumn;
use Google\Service\SA360\GoogleAdsSearchads360V0ServicesListCustomColumnsResponse;

/**
 * The "customColumns" collection of methods.
 * Typical usage is:
 *  <code>
 *   $searchads360Service = new Google\Service\SA360(...);
 *   $customColumns = $searchads360Service->customers_customColumns;
 *  </code>
 */
class CustomersCustomColumns extends \Google\Service\Resource
{
  /**
   * Returns the requested custom column in full detail. (customColumns.get)
   *
   * @param string $resourceName Required. The resource name of the custom column
   * to fetch.
   * @param array $optParams Optional parameters.
   * @return GoogleAdsSearchads360V0ResourcesCustomColumn
   * @throws \Google\Service\Exception
   */
  public function get($resourceName, $optParams = [])
  {
    $params = ['resourceName' => $resourceName];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleAdsSearchads360V0ResourcesCustomColumn::class);
  }
  /**
   * Returns all the custom columns associated with the customer in full detail.
   * (customColumns.listCustomersCustomColumns)
   *
   * @param string $customerId Required. The ID of the customer to apply the
   * CustomColumn list operation to.
   * @param array $optParams Optional parameters.
   * @return GoogleAdsSearchads360V0ServicesListCustomColumnsResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomersCustomColumns($customerId, $optParams = [])
  {
    $params = ['customerId' => $customerId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleAdsSearchads360V0ServicesListCustomColumnsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersCustomColumns::class, 'Google_Service_SA360_Resource_CustomersCustomColumns');
