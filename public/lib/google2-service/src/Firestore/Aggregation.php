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

namespace Google\Service\Firestore;

class Aggregation extends \Google\Model
{
  /**
   * Optional. Optional name of the field to store the result of the aggregation
   * into. If not provided, Firestore will pick a default name following the
   * format `field_`. For example: ``` AGGREGATE COUNT_UP_TO(1) AS
   * count_up_to_1, COUNT_UP_TO(2), COUNT_UP_TO(3) AS count_up_to_3, COUNT(*)
   * OVER ( ... ); ``` becomes: ``` AGGREGATE COUNT_UP_TO(1) AS count_up_to_1,
   * COUNT_UP_TO(2) AS field_1, COUNT_UP_TO(3) AS count_up_to_3, COUNT(*) AS
   * field_2 OVER ( ... ); ``` Requires: * Must be unique across all aggregation
   * aliases. * Conform to document field name limitations.
   *
   * @var string
   */
  public $alias;
  protected $avgType = Avg::class;
  protected $avgDataType = '';
  protected $countType = Count::class;
  protected $countDataType = '';
  protected $sumType = Sum::class;
  protected $sumDataType = '';

  /**
   * Optional. Optional name of the field to store the result of the aggregation
   * into. If not provided, Firestore will pick a default name following the
   * format `field_`. For example: ``` AGGREGATE COUNT_UP_TO(1) AS
   * count_up_to_1, COUNT_UP_TO(2), COUNT_UP_TO(3) AS count_up_to_3, COUNT(*)
   * OVER ( ... ); ``` becomes: ``` AGGREGATE COUNT_UP_TO(1) AS count_up_to_1,
   * COUNT_UP_TO(2) AS field_1, COUNT_UP_TO(3) AS count_up_to_3, COUNT(*) AS
   * field_2 OVER ( ... ); ``` Requires: * Must be unique across all aggregation
   * aliases. * Conform to document field name limitations.
   *
   * @param string $alias
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
  }
  /**
   * @return string
   */
  public function getAlias()
  {
    return $this->alias;
  }
  /**
   * Average aggregator.
   *
   * @param Avg $avg
   */
  public function setAvg(Avg $avg)
  {
    $this->avg = $avg;
  }
  /**
   * @return Avg
   */
  public function getAvg()
  {
    return $this->avg;
  }
  /**
   * Count aggregator.
   *
   * @param Count $count
   */
  public function setCount(Count $count)
  {
    $this->count = $count;
  }
  /**
   * @return Count
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Sum aggregator.
   *
   * @param Sum $sum
   */
  public function setSum(Sum $sum)
  {
    $this->sum = $sum;
  }
  /**
   * @return Sum
   */
  public function getSum()
  {
    return $this->sum;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Aggregation::class, 'Google_Service_Firestore_Aggregation');
