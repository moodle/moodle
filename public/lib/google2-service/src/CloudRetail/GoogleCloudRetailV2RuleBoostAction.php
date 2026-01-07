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

class GoogleCloudRetailV2RuleBoostAction extends \Google\Model
{
  /**
   * Strength of the condition boost, which must be in [-1, 1]. Negative boost
   * means demotion. Default is 0.0. Setting to 1.0 gives the item a big
   * promotion. However, it does not necessarily mean that the boosted item will
   * be the top result at all times, nor that other items will be excluded.
   * Results could still be shown even when none of them matches the condition.
   * And results that are significantly more relevant to the search query can
   * still trump your heavily favored but irrelevant items. Setting to -1.0
   * gives the item a big demotion. However, results that are deeply relevant
   * might still be shown. The item will have an upstream battle to get a fairly
   * high ranking, but it is not blocked out completely. Setting to 0.0 means no
   * boost applied. The boosting condition is ignored.
   *
   * @var float
   */
  public $boost;
  /**
   * The filter can have a max size of 5000 characters. An expression which
   * specifies which products to apply an action to. The syntax and supported
   * fields are the same as a filter expression. See SearchRequest.filter for
   * detail syntax and limitations. Examples: * To boost products with product
   * ID "product_1" or "product_2", and color "Red" or "Blue": *(id:
   * ANY("product_1", "product_2")) * *AND * *(colorFamilies: ANY("Red",
   * "Blue")) *
   *
   * @var string
   */
  public $productsFilter;

  /**
   * Strength of the condition boost, which must be in [-1, 1]. Negative boost
   * means demotion. Default is 0.0. Setting to 1.0 gives the item a big
   * promotion. However, it does not necessarily mean that the boosted item will
   * be the top result at all times, nor that other items will be excluded.
   * Results could still be shown even when none of them matches the condition.
   * And results that are significantly more relevant to the search query can
   * still trump your heavily favored but irrelevant items. Setting to -1.0
   * gives the item a big demotion. However, results that are deeply relevant
   * might still be shown. The item will have an upstream battle to get a fairly
   * high ranking, but it is not blocked out completely. Setting to 0.0 means no
   * boost applied. The boosting condition is ignored.
   *
   * @param float $boost
   */
  public function setBoost($boost)
  {
    $this->boost = $boost;
  }
  /**
   * @return float
   */
  public function getBoost()
  {
    return $this->boost;
  }
  /**
   * The filter can have a max size of 5000 characters. An expression which
   * specifies which products to apply an action to. The syntax and supported
   * fields are the same as a filter expression. See SearchRequest.filter for
   * detail syntax and limitations. Examples: * To boost products with product
   * ID "product_1" or "product_2", and color "Red" or "Blue": *(id:
   * ANY("product_1", "product_2")) * *AND * *(colorFamilies: ANY("Red",
   * "Blue")) *
   *
   * @param string $productsFilter
   */
  public function setProductsFilter($productsFilter)
  {
    $this->productsFilter = $productsFilter;
  }
  /**
   * @return string
   */
  public function getProductsFilter()
  {
    return $this->productsFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RuleBoostAction::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RuleBoostAction');
