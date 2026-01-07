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

namespace Google\Service\BigtableAdmin;

class GcRule extends \Google\Model
{
  protected $intersectionType = Intersection::class;
  protected $intersectionDataType = '';
  /**
   * Delete cells in a column older than the given age. Values must be at least
   * one millisecond, and will be truncated to microsecond granularity.
   *
   * @var string
   */
  public $maxAge;
  /**
   * Delete all cells in a column except the most recent N.
   *
   * @var int
   */
  public $maxNumVersions;
  protected $unionType = Union::class;
  protected $unionDataType = '';

  /**
   * Delete cells that would be deleted by every nested rule.
   *
   * @param Intersection $intersection
   */
  public function setIntersection(Intersection $intersection)
  {
    $this->intersection = $intersection;
  }
  /**
   * @return Intersection
   */
  public function getIntersection()
  {
    return $this->intersection;
  }
  /**
   * Delete cells in a column older than the given age. Values must be at least
   * one millisecond, and will be truncated to microsecond granularity.
   *
   * @param string $maxAge
   */
  public function setMaxAge($maxAge)
  {
    $this->maxAge = $maxAge;
  }
  /**
   * @return string
   */
  public function getMaxAge()
  {
    return $this->maxAge;
  }
  /**
   * Delete all cells in a column except the most recent N.
   *
   * @param int $maxNumVersions
   */
  public function setMaxNumVersions($maxNumVersions)
  {
    $this->maxNumVersions = $maxNumVersions;
  }
  /**
   * @return int
   */
  public function getMaxNumVersions()
  {
    return $this->maxNumVersions;
  }
  /**
   * Delete cells that would be deleted by any nested rule.
   *
   * @param Union $union
   */
  public function setUnion(Union $union)
  {
    $this->union = $union;
  }
  /**
   * @return Union
   */
  public function getUnion()
  {
    return $this->union;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcRule::class, 'Google_Service_BigtableAdmin_GcRule');
