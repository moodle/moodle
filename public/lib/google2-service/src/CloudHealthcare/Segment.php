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

namespace Google\Service\CloudHealthcare;

class Segment extends \Google\Model
{
  /**
   * A mapping from the positional location to the value. The key string uses
   * zero-based indexes separated by dots to identify Fields, components and
   * sub-components. A bracket notation is also used to identify different
   * instances of a repeated field. Regex for key: (\d+)(\[\d+\])?(.\d+)?(.\d+)?
   * Examples of (key, value) pairs: * (0.1, "hemoglobin") denotes that the
   * first component of Field 0 has the value "hemoglobin". * (1.1.2, "CBC")
   * denotes that the second sub-component of the first component of Field 1 has
   * the value "CBC". * (1[0].1, "HbA1c") denotes that the first component of
   * the first Instance of Field 1, which is repeated, has the value "HbA1c".
   *
   * @var string[]
   */
  public $fields;
  /**
   * A string that indicates the type of segment. For example, EVN or PID.
   *
   * @var string
   */
  public $segmentId;
  /**
   * Set ID for segments that can be in a set. This can be empty if it's missing
   * or isn't applicable.
   *
   * @var string
   */
  public $setId;

  /**
   * A mapping from the positional location to the value. The key string uses
   * zero-based indexes separated by dots to identify Fields, components and
   * sub-components. A bracket notation is also used to identify different
   * instances of a repeated field. Regex for key: (\d+)(\[\d+\])?(.\d+)?(.\d+)?
   * Examples of (key, value) pairs: * (0.1, "hemoglobin") denotes that the
   * first component of Field 0 has the value "hemoglobin". * (1.1.2, "CBC")
   * denotes that the second sub-component of the first component of Field 1 has
   * the value "CBC". * (1[0].1, "HbA1c") denotes that the first component of
   * the first Instance of Field 1, which is repeated, has the value "HbA1c".
   *
   * @param string[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return string[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * A string that indicates the type of segment. For example, EVN or PID.
   *
   * @param string $segmentId
   */
  public function setSegmentId($segmentId)
  {
    $this->segmentId = $segmentId;
  }
  /**
   * @return string
   */
  public function getSegmentId()
  {
    return $this->segmentId;
  }
  /**
   * Set ID for segments that can be in a set. This can be empty if it's missing
   * or isn't applicable.
   *
   * @param string $setId
   */
  public function setSetId($setId)
  {
    $this->setId = $setId;
  }
  /**
   * @return string
   */
  public function getSetId()
  {
    return $this->setId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Segment::class, 'Google_Service_CloudHealthcare_Segment');
