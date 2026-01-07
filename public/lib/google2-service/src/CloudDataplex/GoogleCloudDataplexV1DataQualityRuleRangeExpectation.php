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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataQualityRuleRangeExpectation extends \Google\Model
{
  /**
   * Optional. The maximum column value allowed for a row to pass this
   * validation. At least one of min_value and max_value need to be provided.
   *
   * @var string
   */
  public $maxValue;
  /**
   * Optional. The minimum column value allowed for a row to pass this
   * validation. At least one of min_value and max_value need to be provided.
   *
   * @var string
   */
  public $minValue;
  /**
   * Optional. Whether each value needs to be strictly lesser than ('<') the
   * maximum, or if equality is allowed.Only relevant if a max_value has been
   * defined. Default = false.
   *
   * @var bool
   */
  public $strictMaxEnabled;
  /**
   * Optional. Whether each value needs to be strictly greater than ('>') the
   * minimum, or if equality is allowed.Only relevant if a min_value has been
   * defined. Default = false.
   *
   * @var bool
   */
  public $strictMinEnabled;

  /**
   * Optional. The maximum column value allowed for a row to pass this
   * validation. At least one of min_value and max_value need to be provided.
   *
   * @param string $maxValue
   */
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return string
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * Optional. The minimum column value allowed for a row to pass this
   * validation. At least one of min_value and max_value need to be provided.
   *
   * @param string $minValue
   */
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return string
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * Optional. Whether each value needs to be strictly lesser than ('<') the
   * maximum, or if equality is allowed.Only relevant if a max_value has been
   * defined. Default = false.
   *
   * @param bool $strictMaxEnabled
   */
  public function setStrictMaxEnabled($strictMaxEnabled)
  {
    $this->strictMaxEnabled = $strictMaxEnabled;
  }
  /**
   * @return bool
   */
  public function getStrictMaxEnabled()
  {
    return $this->strictMaxEnabled;
  }
  /**
   * Optional. Whether each value needs to be strictly greater than ('>') the
   * minimum, or if equality is allowed.Only relevant if a min_value has been
   * defined. Default = false.
   *
   * @param bool $strictMinEnabled
   */
  public function setStrictMinEnabled($strictMinEnabled)
  {
    $this->strictMinEnabled = $strictMinEnabled;
  }
  /**
   * @return bool
   */
  public function getStrictMinEnabled()
  {
    return $this->strictMinEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualityRuleRangeExpectation::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualityRuleRangeExpectation');
