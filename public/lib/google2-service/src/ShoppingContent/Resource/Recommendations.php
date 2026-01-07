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

use Google\Service\ShoppingContent\GenerateRecommendationsResponse;
use Google\Service\ShoppingContent\ReportInteractionRequest;

/**
 * The "recommendations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Google\Service\ShoppingContent(...);
 *   $recommendations = $contentService->recommendations;
 *  </code>
 */
class Recommendations extends \Google\Service\Resource
{
  /**
   * Generates recommendations for a merchant. (recommendations.generate)
   *
   * @param string $merchantId Required. The ID of the account to fetch
   * recommendations for.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string allowedTag Optional. List of allowed tags. Tags are a set
   * of predefined strings that describe the category that individual
   * recommendation types belong to. User can specify zero or more tags in this
   * field to indicate what categories of recommendations they want to receive.
   * Current list of supported tags: - TREND
   * @opt_param string languageCode Optional. Language code of the client. If not
   * set, the result will be in default language (English). This language code
   * affects all fields prefixed with "localized". This should be set to ISO 639-1
   * country code. List of currently verified supported language code: en, fr, cs,
   * da, de, es, it, nl, no, pl, pt, pt, fi, sv, vi, tr, th, ko, zh-CN, zh-TW, ja,
   * id, hi
   * @return GenerateRecommendationsResponse
   * @throws \Google\Service\Exception
   */
  public function generate($merchantId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId];
    $params = array_merge($params, $optParams);
    return $this->call('generate', [$params], GenerateRecommendationsResponse::class);
  }
  /**
   * Reports an interaction on a recommendation for a merchant.
   * (recommendations.reportInteraction)
   *
   * @param string $merchantId Required. The ID of the account that wants to
   * report an interaction.
   * @param ReportInteractionRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function reportInteraction($merchantId, ReportInteractionRequest $postBody, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reportInteraction', [$params]);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Recommendations::class, 'Google_Service_ShoppingContent_Resource_Recommendations');
