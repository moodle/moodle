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

namespace Google\Service\Dataproc;

class SourceProgress extends \Google\Model
{
  /**
   * @var string
   */
  public $description;
  /**
   * @var string
   */
  public $endOffset;
  public $inputRowsPerSecond;
  /**
   * @var string
   */
  public $latestOffset;
  /**
   * @var string[]
   */
  public $metrics;
  /**
   * @var string
   */
  public $numInputRows;
  public $processedRowsPerSecond;
  /**
   * @var string
   */
  public $startOffset;

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * @param string $endOffset
   */
  public function setEndOffset($endOffset)
  {
    $this->endOffset = $endOffset;
  }
  /**
   * @return string
   */
  public function getEndOffset()
  {
    return $this->endOffset;
  }
  public function setInputRowsPerSecond($inputRowsPerSecond)
  {
    $this->inputRowsPerSecond = $inputRowsPerSecond;
  }
  public function getInputRowsPerSecond()
  {
    return $this->inputRowsPerSecond;
  }
  /**
   * @param string $latestOffset
   */
  public function setLatestOffset($latestOffset)
  {
    $this->latestOffset = $latestOffset;
  }
  /**
   * @return string
   */
  public function getLatestOffset()
  {
    return $this->latestOffset;
  }
  /**
   * @param string[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return string[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * @param string $numInputRows
   */
  public function setNumInputRows($numInputRows)
  {
    $this->numInputRows = $numInputRows;
  }
  /**
   * @return string
   */
  public function getNumInputRows()
  {
    return $this->numInputRows;
  }
  public function setProcessedRowsPerSecond($processedRowsPerSecond)
  {
    $this->processedRowsPerSecond = $processedRowsPerSecond;
  }
  public function getProcessedRowsPerSecond()
  {
    return $this->processedRowsPerSecond;
  }
  /**
   * @param string $startOffset
   */
  public function setStartOffset($startOffset)
  {
    $this->startOffset = $startOffset;
  }
  /**
   * @return string
   */
  public function getStartOffset()
  {
    return $this->startOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceProgress::class, 'Google_Service_Dataproc_SourceProgress');
