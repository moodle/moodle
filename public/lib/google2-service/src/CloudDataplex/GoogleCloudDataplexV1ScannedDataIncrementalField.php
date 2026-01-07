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

class GoogleCloudDataplexV1ScannedDataIncrementalField extends \Google\Model
{
  /**
   * Output only. Value that marks the end of the range.
   *
   * @var string
   */
  public $end;
  /**
   * Output only. The field that contains values which monotonically increases
   * over time (e.g. a timestamp column).
   *
   * @var string
   */
  public $field;
  /**
   * Output only. Value that marks the start of the range.
   *
   * @var string
   */
  public $start;

  /**
   * Output only. Value that marks the end of the range.
   *
   * @param string $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return string
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Output only. The field that contains values which monotonically increases
   * over time (e.g. a timestamp column).
   *
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
   * Output only. Value that marks the start of the range.
   *
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ScannedDataIncrementalField::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ScannedDataIncrementalField');
