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

class GoogleCloudDataplexV1DataQualityColumnResult extends \Google\Collection
{
  protected $collection_key = 'dimensions';
  /**
   * Output only. The column specified in the DataQualityRule.
   *
   * @var string
   */
  public $column;
  protected $dimensionsType = GoogleCloudDataplexV1DataQualityDimensionResult::class;
  protected $dimensionsDataType = 'array';
  /**
   * Output only. Whether the column passed or failed.
   *
   * @var bool
   */
  public $passed;
  /**
   * Output only. The column-level data quality score for this data scan job if
   * and only if the 'column' field is set.The score ranges between between 0,
   * 100 (up to two decimal points).
   *
   * @var float
   */
  public $score;

  /**
   * Output only. The column specified in the DataQualityRule.
   *
   * @param string $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return string
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * Output only. The dimension-level results for this column.
   *
   * @param GoogleCloudDataplexV1DataQualityDimensionResult[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityDimensionResult[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Output only. Whether the column passed or failed.
   *
   * @param bool $passed
   */
  public function setPassed($passed)
  {
    $this->passed = $passed;
  }
  /**
   * @return bool
   */
  public function getPassed()
  {
    return $this->passed;
  }
  /**
   * Output only. The column-level data quality score for this data scan job if
   * and only if the 'column' field is set.The score ranges between between 0,
   * 100 (up to two decimal points).
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualityColumnResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualityColumnResult');
