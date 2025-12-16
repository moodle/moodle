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

namespace Google\Service\SecurityCommandCenter;

class ListFindingsResult extends \Google\Model
{
  /**
   * State change is unused, this is the canonical default for this enum.
   */
  public const STATE_CHANGE_UNUSED = 'UNUSED';
  /**
   * The finding has changed state in some way between the points in time and
   * existed at both points.
   */
  public const STATE_CHANGE_CHANGED = 'CHANGED';
  /**
   * The finding has not changed state between the points in time and existed at
   * both points.
   */
  public const STATE_CHANGE_UNCHANGED = 'UNCHANGED';
  /**
   * The finding was created between the points in time.
   */
  public const STATE_CHANGE_ADDED = 'ADDED';
  /**
   * The finding at timestamp does not match the filter specified, but it did at
   * timestamp - compare_duration.
   */
  public const STATE_CHANGE_REMOVED = 'REMOVED';
  protected $findingType = Finding::class;
  protected $findingDataType = '';
  protected $resourceType = SecuritycenterResource::class;
  protected $resourceDataType = '';
  /**
   * State change of the finding between the points in time.
   *
   * @var string
   */
  public $stateChange;

  /**
   * Finding matching the search request.
   *
   * @param Finding $finding
   */
  public function setFinding(Finding $finding)
  {
    $this->finding = $finding;
  }
  /**
   * @return Finding
   */
  public function getFinding()
  {
    return $this->finding;
  }
  /**
   * Output only. Resource that is associated with this finding.
   *
   * @param SecuritycenterResource $resource
   */
  public function setResource(SecuritycenterResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return SecuritycenterResource
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * State change of the finding between the points in time.
   *
   * Accepted values: UNUSED, CHANGED, UNCHANGED, ADDED, REMOVED
   *
   * @param self::STATE_CHANGE_* $stateChange
   */
  public function setStateChange($stateChange)
  {
    $this->stateChange = $stateChange;
  }
  /**
   * @return self::STATE_CHANGE_*
   */
  public function getStateChange()
  {
    return $this->stateChange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListFindingsResult::class, 'Google_Service_SecurityCommandCenter_ListFindingsResult');
