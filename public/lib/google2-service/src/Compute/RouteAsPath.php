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

namespace Google\Service\Compute;

class RouteAsPath extends \Google\Collection
{
  public const PATH_SEGMENT_TYPE_AS_CONFED_SEQUENCE = 'AS_CONFED_SEQUENCE';
  public const PATH_SEGMENT_TYPE_AS_CONFED_SET = 'AS_CONFED_SET';
  public const PATH_SEGMENT_TYPE_AS_SEQUENCE = 'AS_SEQUENCE';
  public const PATH_SEGMENT_TYPE_AS_SET = 'AS_SET';
  protected $collection_key = 'asLists';
  /**
   * [Output Only] The AS numbers of the AS Path.
   *
   * @var string[]
   */
  public $asLists;
  /**
   * [Output Only] The type of the AS Path, which can be one of the following
   * values:  - 'AS_SET': unordered set of autonomous systems that the route in
   * has traversed   - 'AS_SEQUENCE': ordered set of autonomous systems that the
   * route has traversed   - 'AS_CONFED_SEQUENCE': ordered set of Member
   * Autonomous Systems in the local confederation that the route has traversed
   * - 'AS_CONFED_SET': unordered set of Member Autonomous Systems in the local
   * confederation that the route has traversed
   *
   * @var string
   */
  public $pathSegmentType;

  /**
   * [Output Only] The AS numbers of the AS Path.
   *
   * @param string[] $asLists
   */
  public function setAsLists($asLists)
  {
    $this->asLists = $asLists;
  }
  /**
   * @return string[]
   */
  public function getAsLists()
  {
    return $this->asLists;
  }
  /**
   * [Output Only] The type of the AS Path, which can be one of the following
   * values:  - 'AS_SET': unordered set of autonomous systems that the route in
   * has traversed   - 'AS_SEQUENCE': ordered set of autonomous systems that the
   * route has traversed   - 'AS_CONFED_SEQUENCE': ordered set of Member
   * Autonomous Systems in the local confederation that the route has traversed
   * - 'AS_CONFED_SET': unordered set of Member Autonomous Systems in the local
   * confederation that the route has traversed
   *
   * Accepted values: AS_CONFED_SEQUENCE, AS_CONFED_SET, AS_SEQUENCE, AS_SET
   *
   * @param self::PATH_SEGMENT_TYPE_* $pathSegmentType
   */
  public function setPathSegmentType($pathSegmentType)
  {
    $this->pathSegmentType = $pathSegmentType;
  }
  /**
   * @return self::PATH_SEGMENT_TYPE_*
   */
  public function getPathSegmentType()
  {
    return $this->pathSegmentType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouteAsPath::class, 'Google_Service_Compute_RouteAsPath');
