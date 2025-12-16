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

namespace Google\Service\ShoppingContent\Resource;

use Google\Service\ShoppingContent\Customer;

/**
 * The "customers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Google\Service\ShoppingContent(...);
 *   $customers = $contentService->customers;
 *  </code>
 */
class Customers extends \Google\Service\Resource
{
  /**
   * Allows uploading one customer information entry. Adding a customer with
   * loyalty data enables the customer to see personalized loyalty annotations on
   * search. Uploading a previously existing customer will overwrite the old
   * entry. (customers.create)
   *
   * @param string $merchantId Required. The ID of the account that owns the
   * customer information.
   * @param Customer $postBody
   * @param array $optParams Optional parameters.
   * @return Customer
   */
  public function create($merchantId, Customer $postBody, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Customer::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Customers::class, 'Google_Service_ShoppingContent_Resource_Customers');
