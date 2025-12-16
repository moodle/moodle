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

namespace Google\Service\DisplayVideo;

class PartnerRevenueModel extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const MARKUP_TYPE_PARTNER_REVENUE_MODEL_MARKUP_TYPE_UNSPECIFIED = 'PARTNER_REVENUE_MODEL_MARKUP_TYPE_UNSPECIFIED';
  /**
   * Calculate the partner revenue based on a fixed CPM.
   */
  public const MARKUP_TYPE_PARTNER_REVENUE_MODEL_MARKUP_TYPE_CPM = 'PARTNER_REVENUE_MODEL_MARKUP_TYPE_CPM';
  /**
   * Calculate the partner revenue based on a percentage surcharge of its media
   * cost.
   *
   * @deprecated
   */
  public const MARKUP_TYPE_PARTNER_REVENUE_MODEL_MARKUP_TYPE_MEDIA_COST_MARKUP = 'PARTNER_REVENUE_MODEL_MARKUP_TYPE_MEDIA_COST_MARKUP';
  /**
   * Calculate the partner revenue based on a percentage surcharge of its total
   * media cost, which includes all partner costs and data costs.
   */
  public const MARKUP_TYPE_PARTNER_REVENUE_MODEL_MARKUP_TYPE_TOTAL_MEDIA_COST_MARKUP = 'PARTNER_REVENUE_MODEL_MARKUP_TYPE_TOTAL_MEDIA_COST_MARKUP';
  /**
   * Required. The markup amount of the partner revenue model. Must be greater
   * than or equal to 0. * When the markup_type is set to be
   * `PARTNER_REVENUE_MODEL_MARKUP_TYPE_CPM`, this field represents the CPM
   * markup in micros of advertiser's currency. For example, 1500000 represents
   * 1.5 standard units of the currency. * When the markup_type is set to be
   * `PARTNER_REVENUE_MODEL_MARKUP_TYPE_MEDIA_COST_MARKUP`, this field
   * represents the media cost percent markup in millis. For example, 100
   * represents 0.1% (decimal 0.001). * When the markup_type is set to be
   * `PARTNER_REVENUE_MODEL_MARKUP_TYPE_TOTAL_MEDIA_COST_MARKUP`, this field
   * represents the total media cost percent markup in millis. For example, 100
   * represents 0.1% (decimal 0.001).
   *
   * @var string
   */
  public $markupAmount;
  /**
   * Required. The markup type of the partner revenue model.
   *
   * @var string
   */
  public $markupType;

  /**
   * Required. The markup amount of the partner revenue model. Must be greater
   * than or equal to 0. * When the markup_type is set to be
   * `PARTNER_REVENUE_MODEL_MARKUP_TYPE_CPM`, this field represents the CPM
   * markup in micros of advertiser's currency. For example, 1500000 represents
   * 1.5 standard units of the currency. * When the markup_type is set to be
   * `PARTNER_REVENUE_MODEL_MARKUP_TYPE_MEDIA_COST_MARKUP`, this field
   * represents the media cost percent markup in millis. For example, 100
   * represents 0.1% (decimal 0.001). * When the markup_type is set to be
   * `PARTNER_REVENUE_MODEL_MARKUP_TYPE_TOTAL_MEDIA_COST_MARKUP`, this field
   * represents the total media cost percent markup in millis. For example, 100
   * represents 0.1% (decimal 0.001).
   *
   * @param string $markupAmount
   */
  public function setMarkupAmount($markupAmount)
  {
    $this->markupAmount = $markupAmount;
  }
  /**
   * @return string
   */
  public function getMarkupAmount()
  {
    return $this->markupAmount;
  }
  /**
   * Required. The markup type of the partner revenue model.
   *
   * Accepted values: PARTNER_REVENUE_MODEL_MARKUP_TYPE_UNSPECIFIED,
   * PARTNER_REVENUE_MODEL_MARKUP_TYPE_CPM,
   * PARTNER_REVENUE_MODEL_MARKUP_TYPE_MEDIA_COST_MARKUP,
   * PARTNER_REVENUE_MODEL_MARKUP_TYPE_TOTAL_MEDIA_COST_MARKUP
   *
   * @param self::MARKUP_TYPE_* $markupType
   */
  public function setMarkupType($markupType)
  {
    $this->markupType = $markupType;
  }
  /**
   * @return self::MARKUP_TYPE_*
   */
  public function getMarkupType()
  {
    return $this->markupType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartnerRevenueModel::class, 'Google_Service_DisplayVideo_PartnerRevenueModel');
