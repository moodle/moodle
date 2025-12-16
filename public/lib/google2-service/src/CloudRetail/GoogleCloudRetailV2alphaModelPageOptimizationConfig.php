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

class GoogleCloudRetailV2alphaModelPageOptimizationConfig extends \Google\Collection
{
  /**
   * Unspecified value for restriction.
   */
  public const RESTRICTION_RESTRICTION_UNSPECIFIED = 'RESTRICTION_UNSPECIFIED';
  /**
   * Allow any ServingConfig to be show on any number of panels. Example:
   * `Panel1 candidates`: pdp_ctr, pdp_cvr, home_page_ctr_no_diversity `Panel2
   * candidates`: home_page_ctr_no_diversity, home_page_ctr_diversity,
   * pdp_cvr_no_diversity `Restriction` = NO_RESTRICTION `Valid combinations`: *
   * * (pdp_ctr, home_page_ctr_no_diversity) * (pdp_ctr,
   * home_page_ctr_diversity) * (pdp_ctr, pdp_cvr_no_diversity) * (pdp_cvr,
   * home_page_ctr_no_diversity) * (pdp_cvr, home_page_ctr_diversity) *
   * (pdp_cvr, pdp_cvr_no_diversity) * (home_page_ctr_no_diversity,
   * home_page_ctr_no_diversity) * (home_page_ctr_no_diversity,
   * home_page_ctr_diversity) * (home_page_ctr_no_diversity,
   * pdp_cvr_no_diversity) * `Invalid combinations`: []
   */
  public const RESTRICTION_NO_RESTRICTION = 'NO_RESTRICTION';
  /**
   * Do not allow the same ServingConfig.name to be shown on multiple panels.
   * Example: `Panel1 candidates`: * pdp_ctr, pdp_cvr,
   * home_page_ctr_no_diversity * `Panel2 candidates`: *
   * home_page_ctr_no_diversity, home_page_ctr_diversity_low,
   * pdp_cvr_no_diversity * `Restriction` = `UNIQUE_SERVING_CONFIG_RESTRICTION`
   * `Valid combinations`: * * (pdp_ctr, home_page_ctr_no_diversity) * (pdp_ctr,
   * home_page_ctr_diversity_low) * (pdp_ctr, pdp_cvr_no_diversity) * (pdp_ctr,
   * pdp_cvr_no_diversity) * (pdp_cvr, home_page_ctr_no_diversity) * (pdp_cvr,
   * home_page_ctr_diversity_low) * (pdp_cvr, pdp_cvr_no_diversity) *
   * (home_page_ctr_no_diversity, home_page_ctr_diversity_low) *
   * (home_page_ctr_no_diversity, pdp_cvr_no_diversity) * `Invalid
   * combinations`: * * (home_page_ctr_no_diversity, home_page_ctr_no_diversity)
   * *
   */
  public const RESTRICTION_UNIQUE_SERVING_CONFIG_RESTRICTION = 'UNIQUE_SERVING_CONFIG_RESTRICTION';
  /**
   * Do not allow multiple ServingConfigs with same Model.name to be show on on
   * different panels. Example: `Panel1 candidates`: * pdp_ctr, pdp_cvr,
   * home_page_ctr_no_diversity * `Panel2 candidates`: *
   * home_page_ctr_no_diversity, home_page_ctr_diversity_low,
   * pdp_cvr_no_diversity * `Restriction` = `UNIQUE_MODEL_RESTRICTION` `Valid
   * combinations`: * * (pdp_ctr, home_page_ctr_no_diversity) * (pdp_ctr,
   * home_page_ctr_diversity) * (pdp_ctr, pdp_cvr_no_diversity) * (pdp_ctr,
   * pdp_cvr_no_diversity) * (pdp_cvr, home_page_ctr_no_diversity) * (pdp_cvr,
   * home_page_ctr_diversity_low) * (home_page_ctr_no_diversity,
   * pdp_cvr_no_diversity) * `Invalid combinations`: * *
   * (home_page_ctr_no_diversity, home_page_ctr_no_diversity) * (pdp_cvr,
   * pdp_cvr_no_diversity) *
   */
  public const RESTRICTION_UNIQUE_MODEL_RESTRICTION = 'UNIQUE_MODEL_RESTRICTION';
  /**
   * Do not allow multiple ServingConfigs with same Model.type to be shown on
   * different panels. Example: `Panel1 candidates`: * pdp_ctr, pdp_cvr,
   * home_page_ctr_no_diversity * `Panel2 candidates`: *
   * home_page_ctr_no_diversity, home_page_ctr_diversity_low,
   * pdp_cvr_no_diversity * `Restriction` = `UNIQUE_MODEL_RESTRICTION` `Valid
   * combinations`: * * (pdp_ctr, home_page_ctr_no_diversity) * (pdp_ctr,
   * home_page_ctr_diversity) * (pdp_cvr, home_page_ctr_no_diversity) *
   * (pdp_cvr, home_page_ctr_diversity_low) * (home_page_ctr_no_diversity,
   * pdp_cvr_no_diversity) * `Invalid combinations`: * * (pdp_ctr,
   * pdp_cvr_no_diversity) * (pdp_ctr, pdp_cvr_no_diversity) * (pdp_cvr,
   * pdp_cvr_no_diversity) * (home_page_ctr_no_diversity,
   * home_page_ctr_no_diversity) * (home_page_ctr_no_diversity,
   * home_page_ctr_diversity) *
   */
  public const RESTRICTION_UNIQUE_MODEL_TYPE_RESTRICTION = 'UNIQUE_MODEL_TYPE_RESTRICTION';
  protected $collection_key = 'panels';
  /**
   * Required. The type of UserEvent this page optimization is shown for. Each
   * page has an associated event type - this will be the corresponding event
   * type for the page that the page optimization model is used on. Supported
   * types: * `add-to-cart`: Products being added to cart. * `detail-page-view`:
   * Products detail page viewed. * `home-page-view`: Homepage viewed *
   * `category-page-view`: Homepage viewed * `shopping-cart-page-view`: User
   * viewing a shopping cart. `home-page-view` only allows models with type
   * `recommended-for-you`. All other page_optimization_event_type allow all
   * Model.types.
   *
   * @var string
   */
  public $pageOptimizationEventType;
  protected $panelsType = GoogleCloudRetailV2alphaModelPageOptimizationConfigPanel::class;
  protected $panelsDataType = 'array';
  /**
   * Optional. How to restrict results across panels e.g. can the same
   * ServingConfig be shown on multiple panels at once. If unspecified, default
   * to `UNIQUE_MODEL_RESTRICTION`.
   *
   * @var string
   */
  public $restriction;

  /**
   * Required. The type of UserEvent this page optimization is shown for. Each
   * page has an associated event type - this will be the corresponding event
   * type for the page that the page optimization model is used on. Supported
   * types: * `add-to-cart`: Products being added to cart. * `detail-page-view`:
   * Products detail page viewed. * `home-page-view`: Homepage viewed *
   * `category-page-view`: Homepage viewed * `shopping-cart-page-view`: User
   * viewing a shopping cart. `home-page-view` only allows models with type
   * `recommended-for-you`. All other page_optimization_event_type allow all
   * Model.types.
   *
   * @param string $pageOptimizationEventType
   */
  public function setPageOptimizationEventType($pageOptimizationEventType)
  {
    $this->pageOptimizationEventType = $pageOptimizationEventType;
  }
  /**
   * @return string
   */
  public function getPageOptimizationEventType()
  {
    return $this->pageOptimizationEventType;
  }
  /**
   * Required. A list of panel configurations. Limit = 5.
   *
   * @param GoogleCloudRetailV2alphaModelPageOptimizationConfigPanel[] $panels
   */
  public function setPanels($panels)
  {
    $this->panels = $panels;
  }
  /**
   * @return GoogleCloudRetailV2alphaModelPageOptimizationConfigPanel[]
   */
  public function getPanels()
  {
    return $this->panels;
  }
  /**
   * Optional. How to restrict results across panels e.g. can the same
   * ServingConfig be shown on multiple panels at once. If unspecified, default
   * to `UNIQUE_MODEL_RESTRICTION`.
   *
   * Accepted values: RESTRICTION_UNSPECIFIED, NO_RESTRICTION,
   * UNIQUE_SERVING_CONFIG_RESTRICTION, UNIQUE_MODEL_RESTRICTION,
   * UNIQUE_MODEL_TYPE_RESTRICTION
   *
   * @param self::RESTRICTION_* $restriction
   */
  public function setRestriction($restriction)
  {
    $this->restriction = $restriction;
  }
  /**
   * @return self::RESTRICTION_*
   */
  public function getRestriction()
  {
    return $this->restriction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2alphaModelPageOptimizationConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2alphaModelPageOptimizationConfig');
