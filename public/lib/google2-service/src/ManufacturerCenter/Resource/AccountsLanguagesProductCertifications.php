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

namespace Google\Service\ManufacturerCenter\Resource;

use Google\Service\ManufacturerCenter\ListProductCertificationsResponse;
use Google\Service\ManufacturerCenter\ManufacturersEmpty;
use Google\Service\ManufacturerCenter\ProductCertification;

/**
 * The "productCertifications" collection of methods.
 * Typical usage is:
 *  <code>
 *   $manufacturersService = new Google\Service\ManufacturerCenter(...);
 *   $productCertifications = $manufacturersService->accounts_languages_productCertifications;
 *  </code>
 */
class AccountsLanguagesProductCertifications extends \Google\Service\Resource
{
  /**
   * Deletes a product certification by its name. This method can only be called
   * by certification bodies. (productCertifications.delete)
   *
   * @param string $name Required. The name of the product certification to
   * delete. Format:
   * accounts/{account}/languages/{language_code}/productCertifications/{id}
   * @param array $optParams Optional parameters.
   * @return ManufacturersEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ManufacturersEmpty::class);
  }
  /**
   * Gets a product certification by its name. This method can only be called by
   * certification bodies. (productCertifications.get)
   *
   * @param string $name Required. The name of the product certification to get.
   * Format:
   * accounts/{account}/languages/{language_code}/productCertifications/{id}
   * @param array $optParams Optional parameters.
   * @return ProductCertification
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ProductCertification::class);
  }
  /**
   * Lists product certifications from a specified certification body. This method
   * can only be called by certification bodies.
   * (productCertifications.listAccountsLanguagesProductCertifications)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * product certifications. Format: accounts/{account}/languages/{language_code}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of product
   * certifications to return. The service may return fewer than this value. If
   * unspecified, at most 50 product certifications will be returned. The maximum
   * value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListProductCertifications` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListProductCertifications` must match the call that provided the page token.
   * Required if requesting the second or higher page.
   * @return ListProductCertificationsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsLanguagesProductCertifications($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListProductCertificationsResponse::class);
  }
  /**
   * Updates (or creates if allow_missing = true) a product certification which
   * links certifications with products. This method can only be called by
   * certification bodies. (productCertifications.patch)
   *
   * @param string $name Required. The unique name identifier of a product
   * certification Format:
   * accounts/{account}/languages/{language_code}/productCertifications/{id} Where
   * `id` is a some unique identifier and `language_code` is a 2-letter ISO 639-1
   * code of a Shopping supported language according to
   * https://support.google.com/merchants/answer/160637.
   * @param ProductCertification $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update according
   * to aip.dev/134. However, only full update is supported as of right now.
   * Therefore, it can be either ignored or set to "*". Setting any other values
   * will returns UNIMPLEMENTED error.
   * @return ProductCertification
   * @throws \Google\Service\Exception
   */
  public function patch($name, ProductCertification $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], ProductCertification::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsLanguagesProductCertifications::class, 'Google_Service_ManufacturerCenter_Resource_AccountsLanguagesProductCertifications');
