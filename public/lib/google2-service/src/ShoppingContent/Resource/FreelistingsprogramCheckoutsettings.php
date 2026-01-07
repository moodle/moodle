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

use Google\Service\ShoppingContent\CheckoutSettings;
use Google\Service\ShoppingContent\InsertCheckoutSettingsRequest;

/**
 * The "checkoutsettings" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Google\Service\ShoppingContent(...);
 *   $checkoutsettings = $contentService->freelistingsprogram_checkoutsettings;
 *  </code>
 */
class FreelistingsprogramCheckoutsettings extends \Google\Service\Resource
{
  /**
   * Deletes `Checkout` settings and unenrolls merchant from `Checkout` program.
   * (checkoutsettings.delete)
   *
   * @param string $merchantId Required. The ID of the account.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($merchantId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Gets Checkout settings for the given merchant. This includes information
   * about review state, enrollment state and URL settings. (checkoutsettings.get)
   *
   * @param string $merchantId Required. The ID of the account.
   * @param array $optParams Optional parameters.
   * @return CheckoutSettings
   * @throws \Google\Service\Exception
   */
  public function get($merchantId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CheckoutSettings::class);
  }
  /**
   * Enrolls merchant in `Checkout` program. (checkoutsettings.insert)
   *
   * @param string $merchantId Required. The ID of the account.
   * @param InsertCheckoutSettingsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CheckoutSettings
   * @throws \Google\Service\Exception
   */
  public function insert($merchantId, InsertCheckoutSettingsRequest $postBody, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], CheckoutSettings::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FreelistingsprogramCheckoutsettings::class, 'Google_Service_ShoppingContent_Resource_FreelistingsprogramCheckoutsettings');
