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

namespace Google\Service\MigrationCenterAPI;

class AggregationResult extends \Google\Model
{
  protected $countType = AggregationResultCount::class;
  protected $countDataType = '';
  /**
   * @var string
   */
  public $field;
  protected $frequencyType = AggregationResultFrequency::class;
  protected $frequencyDataType = '';
  protected $histogramType = AggregationResultHistogram::class;
  protected $histogramDataType = '';
  protected $sumType = AggregationResultSum::class;
  protected $sumDataType = '';

  /**
   * @param AggregationResultCount $count
   */
  public function setCount(AggregationResultCount $count)
  {
    $this->count = $count;
  }
  /**
   * @return AggregationResultCount
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * @param AggregationResultFrequency $frequency
   */
  public function setFrequency(AggregationResultFrequency $frequency)
  {
    $this->frequency = $frequency;
  }
  /**
   * @return AggregationResultFrequency
   */
  public function getFrequency()
  {
    return $this->frequency;
  }
  /**
   * @param AggregationResultHistogram $histogram
   */
  public function setHistogram(AggregationResultHistogram $histogram)
  {
    $this->histogram = $histogram;
  }
  /**
   * @return AggregationResultHistogram
   */
  public function getHistogram()
  {
    return $this->histogram;
  }
  /**
   * @param AggregationResultSum $sum
   */
  public function setSum(AggregationResultSum $sum)
  {
    $this->sum = $sum;
  }
  /**
   * @return AggregationResultSum
   */
  public function getSum()
  {
    return $this->sum;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregationResult::class, 'Google_Service_MigrationCenterAPI_AggregationResult');
