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

class GoogleAdsSearchads360V0ResourcesConversionCustomVariableFloodlightConversionCustomVariableInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const FLOODLIGHT_VARIABLE_DATA_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const FLOODLIGHT_VARIABLE_DATA_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Represents a floodlight variable of "Number" type. This variable may be
   * assigned to floodlight variables of DIMENSION or METRIC types.
   */
  public const FLOODLIGHT_VARIABLE_DATA_TYPE_NUMBER = 'NUMBER';
  /**
   * Represents a floodlight variable of "String" type. This variable may be
   * assigned to floodlight variables of DIMENSION type.
   */
  public const FLOODLIGHT_VARIABLE_DATA_TYPE_STRING = 'STRING';
  /**
   * Not specified.
   */
  public const FLOODLIGHT_VARIABLE_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const FLOODLIGHT_VARIABLE_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Dimension floodlight variable type.
   */
  public const FLOODLIGHT_VARIABLE_TYPE_DIMENSION = 'DIMENSION';
  /**
   * Metric floodlight variable type.
   */
  public const FLOODLIGHT_VARIABLE_TYPE_METRIC = 'METRIC';
  /**
   * Floodlight variable type is unset.
   */
  public const FLOODLIGHT_VARIABLE_TYPE_UNSET = 'UNSET';
  /**
   * Output only. Floodlight variable data type defined in Search Ads 360.
   *
   * @var string
   */
  public $floodlightVariableDataType;
  /**
   * Output only. Floodlight variable type defined in Search Ads 360.
   *
   * @var string
   */
  public $floodlightVariableType;

  /**
   * Output only. Floodlight variable data type defined in Search Ads 360.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, NUMBER, STRING
   *
   * @param self::FLOODLIGHT_VARIABLE_DATA_TYPE_* $floodlightVariableDataType
   */
  public function setFloodlightVariableDataType($floodlightVariableDataType)
  {
    $this->floodlightVariableDataType = $floodlightVariableDataType;
  }
  /**
   * @return self::FLOODLIGHT_VARIABLE_DATA_TYPE_*
   */
  public function getFloodlightVariableDataType()
  {
    return $this->floodlightVariableDataType;
  }
  /**
   * Output only. Floodlight variable type defined in Search Ads 360.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, DIMENSION, METRIC, UNSET
   *
   * @param self::FLOODLIGHT_VARIABLE_TYPE_* $floodlightVariableType
   */
  public function setFloodlightVariableType($floodlightVariableType)
  {
    $this->floodlightVariableType = $floodlightVariableType;
  }
  /**
   * @return self::FLOODLIGHT_VARIABLE_TYPE_*
   */
  public function getFloodlightVariableType()
  {
    return $this->floodlightVariableType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesConversionCustomVariableFloodlightConversionCustomVariableInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesConversionCustomVariableFloodlightConversionCustomVariableInfo');
