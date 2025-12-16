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

class SchemaSegment extends \Google\Model
{
  /**
   * The maximum number of times this segment can be present in this group. 0 or
   * -1 means unbounded.
   *
   * @var int
   */
  public $maxOccurs;
  /**
   * The minimum number of times this segment can be present in this group.
   *
   * @var int
   */
  public $minOccurs;
  /**
   * The Segment type. For example, "PID".
   *
   * @var string
   */
  public $type;

  /**
   * The maximum number of times this segment can be present in this group. 0 or
   * -1 means unbounded.
   *
   * @param int $maxOccurs
   */
  public function setMaxOccurs($maxOccurs)
  {
    $this->maxOccurs = $maxOccurs;
  }
  /**
   * @return int
   */
  public function getMaxOccurs()
  {
    return $this->maxOccurs;
  }
  /**
   * The minimum number of times this segment can be present in this group.
   *
   * @param int $minOccurs
   */
  public function setMinOccurs($minOccurs)
  {
    $this->minOccurs = $minOccurs;
  }
  /**
   * @return int
   */
  public function getMinOccurs()
  {
    return $this->minOccurs;
  }
  /**
   * The Segment type. For example, "PID".
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SchemaSegment::class, 'Google_Service_CloudHealthcare_SchemaSegment');
