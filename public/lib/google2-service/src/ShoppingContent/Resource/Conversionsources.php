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

use Google\Service\ShoppingContent\ConversionSource;
use Google\Service\ShoppingContent\ListConversionSourcesResponse;
use Google\Service\ShoppingContent\UndeleteConversionSourceRequest;

/**
 * The "conversionsources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Google\Service\ShoppingContent(...);
 *   $conversionsources = $contentService->conversionsources;
 *  </code>
 */
class Conversionsources extends \Google\Service\Resource
{
  /**
   * Creates a new conversion source. (conversionsources.create)
   *
   * @param string $merchantId Required. The ID of the account that owns the new
   * conversion source.
   * @param ConversionSource $postBody
   * @param array $optParams Optional parameters.
   * @return ConversionSource
   * @throws \Google\Service\Exception
   */
  public function create($merchantId, ConversionSource $postBody, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], ConversionSource::class);
  }
  /**
   * Archives an existing conversion source. It will be recoverable for 30 days.
   * This archiving behavior is not typical in the Content API and unique to this
   * service. (conversionsources.delete)
   *
   * @param string $merchantId Required. The ID of the account that owns the new
   * conversion source.
   * @param string $conversionSourceId Required. The ID of the conversion source
   * to be deleted.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($merchantId, $conversionSourceId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'conversionSourceId' => $conversionSourceId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Fetches a conversion source. (conversionsources.get)
   *
   * @param string $merchantId Required. The ID of the account that owns the new
   * conversion source.
   * @param string $conversionSourceId Required. The REST ID of the collection.
   * @param array $optParams Optional parameters.
   * @return ConversionSource
   * @throws \Google\Service\Exception
   */
  public function get($merchantId, $conversionSourceId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'conversionSourceId' => $conversionSourceId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ConversionSource::class);
  }
  /**
   * Retrieves the list of conversion sources the caller has access to.
   * (conversionsources.listConversionsources)
   *
   * @param string $merchantId Required. The ID of the account that owns the new
   * conversion source.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of conversion sources to return in
   * a page. If no `page_size` is specified, `100` is used as the default value.
   * The maximum value is `200`. Values above `200` will be coerced to `200`.
   * Regardless of pagination, at most `200` conversion sources are returned in
   * total.
   * @opt_param string pageToken Page token.
   * @opt_param bool showDeleted If true, also returns archived conversion
   * sources.
   * @return ListConversionSourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listConversionsources($merchantId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConversionSourcesResponse::class);
  }
  /**
   * Updates information of an existing conversion source.
   * (conversionsources.patch)
   *
   * @param string $merchantId Required. The ID of the account that owns the new
   * conversion source.
   * @param string $conversionSourceId Required. The ID of the conversion source
   * to be updated.
   * @param ConversionSource $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. List of fields being updated. The
   * following fields can be updated: `attribution_settings`, `display_name`,
   * `currency_code`.
   * @return ConversionSource
   * @throws \Google\Service\Exception
   */
  public function patch($merchantId, $conversionSourceId, ConversionSource $postBody, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'conversionSourceId' => $conversionSourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], ConversionSource::class);
  }
  /**
   * Re-enables an archived conversion source. (conversionsources.undelete)
   *
   * @param string $merchantId Required. The ID of the account that owns the new
   * conversion source.
   * @param string $conversionSourceId Required. The ID of the conversion source
   * to be undeleted.
   * @param UndeleteConversionSourceRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function undelete($merchantId, $conversionSourceId, UndeleteConversionSourceRequest $postBody, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'conversionSourceId' => $conversionSourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('undelete', [$params]);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Conversionsources::class, 'Google_Service_ShoppingContent_Resource_Conversionsources');
