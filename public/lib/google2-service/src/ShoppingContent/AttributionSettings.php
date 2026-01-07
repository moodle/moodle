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

namespace Google\Service\ShoppingContent;

class AttributionSettings extends \Google\Collection
{
  public const ATTRIBUTION_MODEL_ATTRIBUTION_MODEL_UNSPECIFIED = 'ATTRIBUTION_MODEL_UNSPECIFIED';
  /**
   * Cross-channel Last Click model.
   */
  public const ATTRIBUTION_MODEL_CROSS_CHANNEL_LAST_CLICK = 'CROSS_CHANNEL_LAST_CLICK';
  /**
   * Ads-preferred Last Click model.
   */
  public const ATTRIBUTION_MODEL_ADS_PREFERRED_LAST_CLICK = 'ADS_PREFERRED_LAST_CLICK';
  /**
   * Cross-channel Data Driven model.
   */
  public const ATTRIBUTION_MODEL_CROSS_CHANNEL_DATA_DRIVEN = 'CROSS_CHANNEL_DATA_DRIVEN';
  /**
   * Cross-channel First Click model.
   */
  public const ATTRIBUTION_MODEL_CROSS_CHANNEL_FIRST_CLICK = 'CROSS_CHANNEL_FIRST_CLICK';
  /**
   * Cross-channel Linear model.
   */
  public const ATTRIBUTION_MODEL_CROSS_CHANNEL_LINEAR = 'CROSS_CHANNEL_LINEAR';
  /**
   * Cross-channel Position Based model.
   */
  public const ATTRIBUTION_MODEL_CROSS_CHANNEL_POSITION_BASED = 'CROSS_CHANNEL_POSITION_BASED';
  /**
   * Cross-channel Time Decay model.
   */
  public const ATTRIBUTION_MODEL_CROSS_CHANNEL_TIME_DECAY = 'CROSS_CHANNEL_TIME_DECAY';
  protected $collection_key = 'conversionType';
  /**
   * Required. Lookback windows (in days) used for attribution in this source.
   * Supported values are 7, 30, 40.
   *
   * @var int
   */
  public $attributionLookbackWindowInDays;
  /**
   * @var string
   */
  public $attributionModel;
  protected $conversionTypeType = AttributionSettingsConversionType::class;
  protected $conversionTypeDataType = 'array';

  /**
   * Required. Lookback windows (in days) used for attribution in this source.
   * Supported values are 7, 30, 40.
   *
   * @param int $attributionLookbackWindowInDays
   */
  public function setAttributionLookbackWindowInDays($attributionLookbackWindowInDays)
  {
    $this->attributionLookbackWindowInDays = $attributionLookbackWindowInDays;
  }
  /**
   * @return int
   */
  public function getAttributionLookbackWindowInDays()
  {
    return $this->attributionLookbackWindowInDays;
  }
  /**
   * @param self::ATTRIBUTION_MODEL_* $attributionModel
   */
  public function setAttributionModel($attributionModel)
  {
    $this->attributionModel = $attributionModel;
  }
  /**
   * @return self::ATTRIBUTION_MODEL_*
   */
  public function getAttributionModel()
  {
    return $this->attributionModel;
  }
  /**
   * Immutable. Unordered list. List of different conversion types a conversion
   * event can be classified as. A standard "purchase" type will be
   * automatically created if this list is empty at creation time.
   *
   * @param AttributionSettingsConversionType[] $conversionType
   */
  public function setConversionType($conversionType)
  {
    $this->conversionType = $conversionType;
  }
  /**
   * @return AttributionSettingsConversionType[]
   */
  public function getConversionType()
  {
    return $this->conversionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttributionSettings::class, 'Google_Service_ShoppingContent_AttributionSettings');
