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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1CatalogItemLevelConfig extends \Google\Model
{
  /**
   * Unknown value - should never be used.
   */
  public const EVENT_ITEM_LEVEL_CATALOG_ITEM_LEVEL_UNSPECIFIED = 'CATALOG_ITEM_LEVEL_UNSPECIFIED';
  /**
   * Catalog items are at variant level.
   */
  public const EVENT_ITEM_LEVEL_VARIANT = 'VARIANT';
  /**
   * Catalog items are at master level.
   */
  public const EVENT_ITEM_LEVEL_MASTER = 'MASTER';
  /**
   * Unknown value - should never be used.
   */
  public const PREDICT_ITEM_LEVEL_CATALOG_ITEM_LEVEL_UNSPECIFIED = 'CATALOG_ITEM_LEVEL_UNSPECIFIED';
  /**
   * Catalog items are at variant level.
   */
  public const PREDICT_ITEM_LEVEL_VARIANT = 'VARIANT';
  /**
   * Catalog items are at master level.
   */
  public const PREDICT_ITEM_LEVEL_MASTER = 'MASTER';
  /**
   * Optional. Level of the catalog at which events are uploaded. See
   * https://cloud.google.com/recommendations-ai/docs/catalog#catalog-levels for
   * more details.
   *
   * @var string
   */
  public $eventItemLevel;
  /**
   * Optional. Level of the catalog at which predictions are made. See
   * https://cloud.google.com/recommendations-ai/docs/catalog#catalog-levels for
   * more details.
   *
   * @var string
   */
  public $predictItemLevel;

  /**
   * Optional. Level of the catalog at which events are uploaded. See
   * https://cloud.google.com/recommendations-ai/docs/catalog#catalog-levels for
   * more details.
   *
   * Accepted values: CATALOG_ITEM_LEVEL_UNSPECIFIED, VARIANT, MASTER
   *
   * @param self::EVENT_ITEM_LEVEL_* $eventItemLevel
   */
  public function setEventItemLevel($eventItemLevel)
  {
    $this->eventItemLevel = $eventItemLevel;
  }
  /**
   * @return self::EVENT_ITEM_LEVEL_*
   */
  public function getEventItemLevel()
  {
    return $this->eventItemLevel;
  }
  /**
   * Optional. Level of the catalog at which predictions are made. See
   * https://cloud.google.com/recommendations-ai/docs/catalog#catalog-levels for
   * more details.
   *
   * Accepted values: CATALOG_ITEM_LEVEL_UNSPECIFIED, VARIANT, MASTER
   *
   * @param self::PREDICT_ITEM_LEVEL_* $predictItemLevel
   */
  public function setPredictItemLevel($predictItemLevel)
  {
    $this->predictItemLevel = $predictItemLevel;
  }
  /**
   * @return self::PREDICT_ITEM_LEVEL_*
   */
  public function getPredictItemLevel()
  {
    return $this->predictItemLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1CatalogItemLevelConfig::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1CatalogItemLevelConfig');
