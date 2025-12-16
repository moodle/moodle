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

namespace Google\Service\AirQuality;

class AirQualityIndex extends \Google\Model
{
  /**
   * The index's numeric score. Examples: 10, 100. The value is not normalized
   * and should only be interpreted in the context of its related air-quality
   * index. For non-numeric indexes, this field will not be returned. Note: This
   * field should be used for calculations, graph display, etc. For displaying
   * the index score, you should use the AQI display field.
   *
   * @var int
   */
  public $aqi;
  /**
   * Textual representation of the index numeric score, that may include prefix
   * or suffix symbols, which usually represents the worst index score. Example:
   * >100 or 10+. Note: This field should be used when you want to display the
   * index score. For non-numeric indexes, this field is empty.
   *
   * @var string
   */
  public $aqiDisplay;
  /**
   * Textual classification of the index numeric score interpretation. For
   * example: "Excellent air quality".
   *
   * @var string
   */
  public $category;
  /**
   * The index's code. This field represents the index for programming purposes
   * by using snake case instead of spaces. Examples: "uaqi", "fra_atmo".
   *
   * @var string
   */
  public $code;
  protected $colorType = Color::class;
  protected $colorDataType = '';
  /**
   * A human readable representation of the index name. Example: "AQI (US)"
   *
   * @var string
   */
  public $displayName;
  /**
   * The chemical symbol of the dominant pollutant. For example: "CO".
   *
   * @var string
   */
  public $dominantPollutant;

  /**
   * The index's numeric score. Examples: 10, 100. The value is not normalized
   * and should only be interpreted in the context of its related air-quality
   * index. For non-numeric indexes, this field will not be returned. Note: This
   * field should be used for calculations, graph display, etc. For displaying
   * the index score, you should use the AQI display field.
   *
   * @param int $aqi
   */
  public function setAqi($aqi)
  {
    $this->aqi = $aqi;
  }
  /**
   * @return int
   */
  public function getAqi()
  {
    return $this->aqi;
  }
  /**
   * Textual representation of the index numeric score, that may include prefix
   * or suffix symbols, which usually represents the worst index score. Example:
   * >100 or 10+. Note: This field should be used when you want to display the
   * index score. For non-numeric indexes, this field is empty.
   *
   * @param string $aqiDisplay
   */
  public function setAqiDisplay($aqiDisplay)
  {
    $this->aqiDisplay = $aqiDisplay;
  }
  /**
   * @return string
   */
  public function getAqiDisplay()
  {
    return $this->aqiDisplay;
  }
  /**
   * Textual classification of the index numeric score interpretation. For
   * example: "Excellent air quality".
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The index's code. This field represents the index for programming purposes
   * by using snake case instead of spaces. Examples: "uaqi", "fra_atmo".
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * The color used to represent the AQI numeric score.
   *
   * @param Color $color
   */
  public function setColor(Color $color)
  {
    $this->color = $color;
  }
  /**
   * @return Color
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * A human readable representation of the index name. Example: "AQI (US)"
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The chemical symbol of the dominant pollutant. For example: "CO".
   *
   * @param string $dominantPollutant
   */
  public function setDominantPollutant($dominantPollutant)
  {
    $this->dominantPollutant = $dominantPollutant;
  }
  /**
   * @return string
   */
  public function getDominantPollutant()
  {
    return $this->dominantPollutant;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AirQualityIndex::class, 'Google_Service_AirQuality_AirQualityIndex');
