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

namespace Google\Service\Bigquery;

class HighCardinalityJoin extends \Google\Model
{
  /**
   * Output only. Count of left input rows.
   *
   * @var string
   */
  public $leftRows;
  /**
   * Output only. Count of the output rows.
   *
   * @var string
   */
  public $outputRows;
  /**
   * Output only. Count of right input rows.
   *
   * @var string
   */
  public $rightRows;
  /**
   * Output only. The index of the join operator in the ExplainQueryStep lists.
   *
   * @var int
   */
  public $stepIndex;

  /**
   * Output only. Count of left input rows.
   *
   * @param string $leftRows
   */
  public function setLeftRows($leftRows)
  {
    $this->leftRows = $leftRows;
  }
  /**
   * @return string
   */
  public function getLeftRows()
  {
    return $this->leftRows;
  }
  /**
   * Output only. Count of the output rows.
   *
   * @param string $outputRows
   */
  public function setOutputRows($outputRows)
  {
    $this->outputRows = $outputRows;
  }
  /**
   * @return string
   */
  public function getOutputRows()
  {
    return $this->outputRows;
  }
  /**
   * Output only. Count of right input rows.
   *
   * @param string $rightRows
   */
  public function setRightRows($rightRows)
  {
    $this->rightRows = $rightRows;
  }
  /**
   * @return string
   */
  public function getRightRows()
  {
    return $this->rightRows;
  }
  /**
   * Output only. The index of the join operator in the ExplainQueryStep lists.
   *
   * @param int $stepIndex
   */
  public function setStepIndex($stepIndex)
  {
    $this->stepIndex = $stepIndex;
  }
  /**
   * @return int
   */
  public function getStepIndex()
  {
    return $this->stepIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HighCardinalityJoin::class, 'Google_Service_Bigquery_HighCardinalityJoin');
