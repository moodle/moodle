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

namespace Google\Service\Walletobjects;

class GroupingInfo extends \Google\Model
{
  /**
   * Optional grouping ID for grouping the passes with the same ID visually
   * together. Grouping with different types of passes is allowed.
   *
   * @var string
   */
  public $groupingId;
  /**
   * Optional index for sorting the passes when they are grouped with other
   * passes. Passes with lower sort index are shown before passes with higher
   * sort index. If unspecified, the value is assumed to be INT_MAX. For two
   * passes with the same sort index, the sorting behavior is undefined.
   *
   * @var int
   */
  public $sortIndex;

  /**
   * Optional grouping ID for grouping the passes with the same ID visually
   * together. Grouping with different types of passes is allowed.
   *
   * @param string $groupingId
   */
  public function setGroupingId($groupingId)
  {
    $this->groupingId = $groupingId;
  }
  /**
   * @return string
   */
  public function getGroupingId()
  {
    return $this->groupingId;
  }
  /**
   * Optional index for sorting the passes when they are grouped with other
   * passes. Passes with lower sort index are shown before passes with higher
   * sort index. If unspecified, the value is assumed to be INT_MAX. For two
   * passes with the same sort index, the sorting behavior is undefined.
   *
   * @param int $sortIndex
   */
  public function setSortIndex($sortIndex)
  {
    $this->sortIndex = $sortIndex;
  }
  /**
   * @return int
   */
  public function getSortIndex()
  {
    return $this->sortIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupingInfo::class, 'Google_Service_Walletobjects_GroupingInfo');
