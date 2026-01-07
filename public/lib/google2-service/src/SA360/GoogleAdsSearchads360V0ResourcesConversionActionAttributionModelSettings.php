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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesConversionActionAttributionModelSettings extends \Google\Model
{
  /**
   * Not specified.
   */
  public const ATTRIBUTION_MODEL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const ATTRIBUTION_MODEL_UNKNOWN = 'UNKNOWN';
  /**
   * Uses external attribution.
   */
  public const ATTRIBUTION_MODEL_EXTERNAL = 'EXTERNAL';
  /**
   * Attributes all credit for a conversion to its last click.
   */
  public const ATTRIBUTION_MODEL_GOOGLE_ADS_LAST_CLICK = 'GOOGLE_ADS_LAST_CLICK';
  /**
   * Attributes all credit for a conversion to its first click using Google
   * Search attribution.
   */
  public const ATTRIBUTION_MODEL_GOOGLE_SEARCH_ATTRIBUTION_FIRST_CLICK = 'GOOGLE_SEARCH_ATTRIBUTION_FIRST_CLICK';
  /**
   * Attributes credit for a conversion equally across all of its clicks using
   * Google Search attribution.
   */
  public const ATTRIBUTION_MODEL_GOOGLE_SEARCH_ATTRIBUTION_LINEAR = 'GOOGLE_SEARCH_ATTRIBUTION_LINEAR';
  /**
   * Attributes exponentially more credit for a conversion to its more recent
   * clicks using Google Search attribution (half-life is 1 week).
   */
  public const ATTRIBUTION_MODEL_GOOGLE_SEARCH_ATTRIBUTION_TIME_DECAY = 'GOOGLE_SEARCH_ATTRIBUTION_TIME_DECAY';
  /**
   * Attributes 40% of the credit for a conversion to its first and last clicks.
   * Remaining 20% is evenly distributed across all other clicks. This uses
   * Google Search attribution.
   */
  public const ATTRIBUTION_MODEL_GOOGLE_SEARCH_ATTRIBUTION_POSITION_BASED = 'GOOGLE_SEARCH_ATTRIBUTION_POSITION_BASED';
  /**
   * Flexible model that uses machine learning to determine the appropriate
   * distribution of credit among clicks using Google Search attribution.
   */
  public const ATTRIBUTION_MODEL_GOOGLE_SEARCH_ATTRIBUTION_DATA_DRIVEN = 'GOOGLE_SEARCH_ATTRIBUTION_DATA_DRIVEN';
  /**
   * Not specified.
   */
  public const DATA_DRIVEN_MODEL_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const DATA_DRIVEN_MODEL_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The data driven model is available.
   */
  public const DATA_DRIVEN_MODEL_STATUS_AVAILABLE = 'AVAILABLE';
  /**
   * The data driven model is stale. It hasn't been updated for at least 7 days.
   * It is still being used, but will become expired if it does not get updated
   * for 30 days.
   */
  public const DATA_DRIVEN_MODEL_STATUS_STALE = 'STALE';
  /**
   * The data driven model expired. It hasn't been updated for at least 30 days
   * and cannot be used. Most commonly this is because there hasn't been the
   * required number of events in a recent 30-day period.
   */
  public const DATA_DRIVEN_MODEL_STATUS_EXPIRED = 'EXPIRED';
  /**
   * The data driven model has never been generated. Most commonly this is
   * because there has never been the required number of events in any 30-day
   * period.
   */
  public const DATA_DRIVEN_MODEL_STATUS_NEVER_GENERATED = 'NEVER_GENERATED';
  /**
   * The attribution model type of this conversion action.
   *
   * @var string
   */
  public $attributionModel;
  /**
   * Output only. The status of the data-driven attribution model for the
   * conversion action.
   *
   * @var string
   */
  public $dataDrivenModelStatus;

  /**
   * The attribution model type of this conversion action.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, EXTERNAL, GOOGLE_ADS_LAST_CLICK,
   * GOOGLE_SEARCH_ATTRIBUTION_FIRST_CLICK, GOOGLE_SEARCH_ATTRIBUTION_LINEAR,
   * GOOGLE_SEARCH_ATTRIBUTION_TIME_DECAY,
   * GOOGLE_SEARCH_ATTRIBUTION_POSITION_BASED,
   * GOOGLE_SEARCH_ATTRIBUTION_DATA_DRIVEN
   *
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
   * Output only. The status of the data-driven attribution model for the
   * conversion action.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, AVAILABLE, STALE, EXPIRED,
   * NEVER_GENERATED
   *
   * @param self::DATA_DRIVEN_MODEL_STATUS_* $dataDrivenModelStatus
   */
  public function setDataDrivenModelStatus($dataDrivenModelStatus)
  {
    $this->dataDrivenModelStatus = $dataDrivenModelStatus;
  }
  /**
   * @return self::DATA_DRIVEN_MODEL_STATUS_*
   */
  public function getDataDrivenModelStatus()
  {
    return $this->dataDrivenModelStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesConversionActionAttributionModelSettings::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesConversionActionAttributionModelSettings');
