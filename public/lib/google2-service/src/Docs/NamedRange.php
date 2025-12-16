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

namespace Google\Service\Docs;

class NamedRange extends \Google\Collection
{
  protected $collection_key = 'ranges';
  /**
   * The name of the named range.
   *
   * @var string
   */
  public $name;
  /**
   * The ID of the named range.
   *
   * @var string
   */
  public $namedRangeId;
  protected $rangesType = Range::class;
  protected $rangesDataType = 'array';

  /**
   * The name of the named range.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The ID of the named range.
   *
   * @param string $namedRangeId
   */
  public function setNamedRangeId($namedRangeId)
  {
    $this->namedRangeId = $namedRangeId;
  }
  /**
   * @return string
   */
  public function getNamedRangeId()
  {
    return $this->namedRangeId;
  }
  /**
   * The ranges that belong to this named range.
   *
   * @param Range[] $ranges
   */
  public function setRanges($ranges)
  {
    $this->ranges = $ranges;
  }
  /**
   * @return Range[]
   */
  public function getRanges()
  {
    return $this->ranges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NamedRange::class, 'Google_Service_Docs_NamedRange');
