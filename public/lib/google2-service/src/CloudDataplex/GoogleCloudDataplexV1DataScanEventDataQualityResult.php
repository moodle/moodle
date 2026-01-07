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

class GoogleCloudDataplexV1DataScanEventDataQualityResult extends \Google\Model
{
  /**
   * The score of each column scanned in the data scan job. The key of the map
   * is the name of the column. The value is the data quality score for the
   * column.The score ranges between 0, 100 (up to two decimal points).
   *
   * @var float[]
   */
  public $columnScore;
  /**
   * The result of each dimension for data quality result. The key of the map is
   * the name of the dimension. The value is the bool value depicting whether
   * the dimension result was pass or not.
   *
   * @var bool[]
   */
  public $dimensionPassed;
  /**
   * The score of each dimension for data quality result. The key of the map is
   * the name of the dimension. The value is the data quality score for the
   * dimension.The score ranges between 0, 100 (up to two decimal points).
   *
   * @var float[]
   */
  public $dimensionScore;
  /**
   * Whether the data quality result was pass or not.
   *
   * @var bool
   */
  public $passed;
  /**
   * The count of rows processed in the data scan job.
   *
   * @var string
   */
  public $rowCount;
  /**
   * The table-level data quality score for the data scan job.The data quality
   * score ranges between 0, 100 (up to two decimal points).
   *
   * @var float
   */
  public $score;

  /**
   * The score of each column scanned in the data scan job. The key of the map
   * is the name of the column. The value is the data quality score for the
   * column.The score ranges between 0, 100 (up to two decimal points).
   *
   * @param float[] $columnScore
   */
  public function setColumnScore($columnScore)
  {
    $this->columnScore = $columnScore;
  }
  /**
   * @return float[]
   */
  public function getColumnScore()
  {
    return $this->columnScore;
  }
  /**
   * The result of each dimension for data quality result. The key of the map is
   * the name of the dimension. The value is the bool value depicting whether
   * the dimension result was pass or not.
   *
   * @param bool[] $dimensionPassed
   */
  public function setDimensionPassed($dimensionPassed)
  {
    $this->dimensionPassed = $dimensionPassed;
  }
  /**
   * @return bool[]
   */
  public function getDimensionPassed()
  {
    return $this->dimensionPassed;
  }
  /**
   * The score of each dimension for data quality result. The key of the map is
   * the name of the dimension. The value is the data quality score for the
   * dimension.The score ranges between 0, 100 (up to two decimal points).
   *
   * @param float[] $dimensionScore
   */
  public function setDimensionScore($dimensionScore)
  {
    $this->dimensionScore = $dimensionScore;
  }
  /**
   * @return float[]
   */
  public function getDimensionScore()
  {
    return $this->dimensionScore;
  }
  /**
   * Whether the data quality result was pass or not.
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
   * The count of rows processed in the data scan job.
   *
   * @param string $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return string
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
  /**
   * The table-level data quality score for the data scan job.The data quality
   * score ranges between 0, 100 (up to two decimal points).
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
class_alias(GoogleCloudDataplexV1DataScanEventDataQualityResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataScanEventDataQualityResult');
