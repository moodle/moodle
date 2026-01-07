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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2RuleFilterAction extends \Google\Model
{
  /**
   * A filter to apply on the matching condition results. Supported features: *
   * filter must be set. * Filter syntax is identical to SearchRequest.filter.
   * For more information, see [Filter](/retail/docs/filter-and-order#filter). *
   * To filter products with product ID "product_1" or "product_2", and color
   * "Red" or "Blue": *(id: ANY("product_1", "product_2")) * *AND *
   * *(colorFamilies: ANY("Red", "Blue")) *
   *
   * @var string
   */
  public $filter;

  /**
   * A filter to apply on the matching condition results. Supported features: *
   * filter must be set. * Filter syntax is identical to SearchRequest.filter.
   * For more information, see [Filter](/retail/docs/filter-and-order#filter). *
   * To filter products with product ID "product_1" or "product_2", and color
   * "Red" or "Blue": *(id: ANY("product_1", "product_2")) * *AND *
   * *(colorFamilies: ANY("Red", "Blue")) *
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RuleFilterAction::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RuleFilterAction');
