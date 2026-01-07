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

class AlgorithmRulesFloodlightActivityConversionSignal extends \Google\Model
{
  /**
   * The action is not specified.
   */
  public const CONVERSION_COUNTING_CONVERSION_COUNTING_UNSPECIFIED = 'CONVERSION_COUNTING_UNSPECIFIED';
  /**
   * All conversions.
   */
  public const CONVERSION_COUNTING_ALL_CONVERSIONS = 'ALL_CONVERSIONS';
  /**
   * Post-click conversions.
   */
  public const CONVERSION_COUNTING_POST_CLICK = 'POST_CLICK';
  /**
   * Post-view conversions.
   */
  public const CONVERSION_COUNTING_POST_VIEW = 'POST_VIEW';
  /**
   * The action is not specified.
   */
  public const COUNTING_METHOD_COUNTING_METHOD_UNSPECIFIED = 'COUNTING_METHOD_UNSPECIFIED';
  /**
   * The count of conversions associated with the conversion activity.
   */
  public const COUNTING_METHOD_CONVERSIONS_COUNT = 'CONVERSIONS_COUNT';
  /**
   * The number of sales items associated with the conversion activity.
   */
  public const COUNTING_METHOD_SALES_QUANTITY = 'SALES_QUANTITY';
  /**
   * The sales revenue associated with the conversion activity.
   */
  public const COUNTING_METHOD_SALES_VALUE = 'SALES_VALUE';
  /**
   * The count of unique conversions associated with the conversion activity.
   * Only one conversion can be counted per impression.
   */
  public const COUNTING_METHOD_UNIQUE_COUNT = 'UNIQUE_COUNT';
  /**
   * Required. The type of conversions to be used in impression value
   * computation, for example, post-click conversions.
   *
   * @var string
   */
  public $conversionCounting;
  /**
   * Required. The way to acquire value from the floodlight activity, for
   * example, count of the conversion.
   *
   * @var string
   */
  public $countingMethod;
  /**
   * Required. Id of the floodlight activity.
   *
   * @var string
   */
  public $floodlightActivityId;

  /**
   * Required. The type of conversions to be used in impression value
   * computation, for example, post-click conversions.
   *
   * Accepted values: CONVERSION_COUNTING_UNSPECIFIED, ALL_CONVERSIONS,
   * POST_CLICK, POST_VIEW
   *
   * @param self::CONVERSION_COUNTING_* $conversionCounting
   */
  public function setConversionCounting($conversionCounting)
  {
    $this->conversionCounting = $conversionCounting;
  }
  /**
   * @return self::CONVERSION_COUNTING_*
   */
  public function getConversionCounting()
  {
    return $this->conversionCounting;
  }
  /**
   * Required. The way to acquire value from the floodlight activity, for
   * example, count of the conversion.
   *
   * Accepted values: COUNTING_METHOD_UNSPECIFIED, CONVERSIONS_COUNT,
   * SALES_QUANTITY, SALES_VALUE, UNIQUE_COUNT
   *
   * @param self::COUNTING_METHOD_* $countingMethod
   */
  public function setCountingMethod($countingMethod)
  {
    $this->countingMethod = $countingMethod;
  }
  /**
   * @return self::COUNTING_METHOD_*
   */
  public function getCountingMethod()
  {
    return $this->countingMethod;
  }
  /**
   * Required. Id of the floodlight activity.
   *
   * @param string $floodlightActivityId
   */
  public function setFloodlightActivityId($floodlightActivityId)
  {
    $this->floodlightActivityId = $floodlightActivityId;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityId()
  {
    return $this->floodlightActivityId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRulesFloodlightActivityConversionSignal::class, 'Google_Service_DisplayVideo_AlgorithmRulesFloodlightActivityConversionSignal');
