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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionWindowConfig extends \Google\Model
{
  /**
   * Name of the column that should be used to generate sliding windows. The
   * column should contain either booleans or string booleans; if the value of
   * the row is True, generate a sliding window with the horizon starting at
   * that row. The column will not be used as a feature in training.
   *
   * @var string
   */
  public $column;
  /**
   * Maximum number of windows that should be generated across all time series.
   *
   * @var string
   */
  public $maxCount;
  /**
   * Stride length used to generate input examples. Within one time series,
   * every {$STRIDE_LENGTH} rows will be used to generate a sliding window.
   *
   * @var string
   */
  public $strideLength;

  /**
   * Name of the column that should be used to generate sliding windows. The
   * column should contain either booleans or string booleans; if the value of
   * the row is True, generate a sliding window with the horizon starting at
   * that row. The column will not be used as a feature in training.
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
   * Maximum number of windows that should be generated across all time series.
   *
   * @param string $maxCount
   */
  public function setMaxCount($maxCount)
  {
    $this->maxCount = $maxCount;
  }
  /**
   * @return string
   */
  public function getMaxCount()
  {
    return $this->maxCount;
  }
  /**
   * Stride length used to generate input examples. Within one time series,
   * every {$STRIDE_LENGTH} rows will be used to generate a sliding window.
   *
   * @param string $strideLength
   */
  public function setStrideLength($strideLength)
  {
    $this->strideLength = $strideLength;
  }
  /**
   * @return string
   */
  public function getStrideLength()
  {
    return $this->strideLength;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionWindowConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionWindowConfig');
