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

namespace Google\Service\PaymentsResellerSubscription;

class FindEligiblePromotionsRequest extends \Google\Model
{
  /**
   * Optional. Specifies the filters for the promotion results. The syntax is
   * defined in https://google.aip.dev/160 with the following caveats: 1. Only
   * the following features are supported: - Logical operator `AND` - Comparison
   * operator `=` (no wildcards `*`) - Traversal operator `.` - Has operator `:`
   * (no wildcards `*`) 2. Only the following fields are supported: -
   * `applicableProducts` - `regionCodes` -
   * `youtubePayload.partnerEligibilityId` - `youtubePayload.postalCode` 3.
   * Unless explicitly mentioned above, other features are not supported.
   * Example: `applicableProducts:partners/partner1/products/product1 AND
   * regionCodes:US AND youtubePayload.postalCode=94043 AND
   * youtubePayload.partnerEligibilityId=eligibility-id`
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. The maximum number of promotions to return. The service may
   * return fewer than this value. If unspecified, at most 50 products will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token, received from a previous `ListPromotions` call.
   * Provide this to retrieve the subsequent page. When paginating, all other
   * parameters provided to `ListPromotions` must match the call that provided
   * the page token.
   *
   * @var string
   */
  public $pageToken;

  /**
   * Optional. Specifies the filters for the promotion results. The syntax is
   * defined in https://google.aip.dev/160 with the following caveats: 1. Only
   * the following features are supported: - Logical operator `AND` - Comparison
   * operator `=` (no wildcards `*`) - Traversal operator `.` - Has operator `:`
   * (no wildcards `*`) 2. Only the following fields are supported: -
   * `applicableProducts` - `regionCodes` -
   * `youtubePayload.partnerEligibilityId` - `youtubePayload.postalCode` 3.
   * Unless explicitly mentioned above, other features are not supported.
   * Example: `applicableProducts:partners/partner1/products/product1 AND
   * regionCodes:US AND youtubePayload.postalCode=94043 AND
   * youtubePayload.partnerEligibilityId=eligibility-id`
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. The maximum number of promotions to return. The service may
   * return fewer than this value. If unspecified, at most 50 products will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Optional. A page token, received from a previous `ListPromotions` call.
   * Provide this to retrieve the subsequent page. When paginating, all other
   * parameters provided to `ListPromotions` must match the call that provided
   * the page token.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FindEligiblePromotionsRequest::class, 'Google_Service_PaymentsResellerSubscription_FindEligiblePromotionsRequest');
