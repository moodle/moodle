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

namespace Google\Service\Clouderrorreporting;

class ErrorGroupStats extends \Google\Collection
{
  protected $collection_key = 'timedCounts';
  protected $affectedServicesType = ServiceContext::class;
  protected $affectedServicesDataType = 'array';
  /**
   * Approximate number of affected users in the given group that match the
   * filter criteria. Users are distinguished by data in the ErrorContext of the
   * individual error events, such as their login name or their remote IP
   * address in case of HTTP requests. The number of affected users can be zero
   * even if the number of errors is non-zero if no data was provided from which
   * the affected user could be deduced. Users are counted based on data in the
   * request context that was provided in the error report. If more users are
   * implicitly affected, such as due to a crash of the whole service, this is
   * not reflected here.
   *
   * @var string
   */
  public $affectedUsersCount;
  /**
   * Approximate total number of events in the given group that match the filter
   * criteria.
   *
   * @var string
   */
  public $count;
  /**
   * Approximate first occurrence that was ever seen for this group and which
   * matches the given filter criteria, ignoring the time_range that was
   * specified in the request.
   *
   * @var string
   */
  public $firstSeenTime;
  protected $groupType = ErrorGroup::class;
  protected $groupDataType = '';
  /**
   * Approximate last occurrence that was ever seen for this group and which
   * matches the given filter criteria, ignoring the time_range that was
   * specified in the request.
   *
   * @var string
   */
  public $lastSeenTime;
  /**
   * The total number of services with a non-zero error count for the given
   * filter criteria.
   *
   * @var int
   */
  public $numAffectedServices;
  protected $representativeType = ErrorEvent::class;
  protected $representativeDataType = '';
  protected $timedCountsType = TimedCount::class;
  protected $timedCountsDataType = 'array';

  /**
   * Service contexts with a non-zero error count for the given filter criteria.
   * This list can be truncated if multiple services are affected. Refer to
   * `num_affected_services` for the total count.
   *
   * @param ServiceContext[] $affectedServices
   */
  public function setAffectedServices($affectedServices)
  {
    $this->affectedServices = $affectedServices;
  }
  /**
   * @return ServiceContext[]
   */
  public function getAffectedServices()
  {
    return $this->affectedServices;
  }
  /**
   * Approximate number of affected users in the given group that match the
   * filter criteria. Users are distinguished by data in the ErrorContext of the
   * individual error events, such as their login name or their remote IP
   * address in case of HTTP requests. The number of affected users can be zero
   * even if the number of errors is non-zero if no data was provided from which
   * the affected user could be deduced. Users are counted based on data in the
   * request context that was provided in the error report. If more users are
   * implicitly affected, such as due to a crash of the whole service, this is
   * not reflected here.
   *
   * @param string $affectedUsersCount
   */
  public function setAffectedUsersCount($affectedUsersCount)
  {
    $this->affectedUsersCount = $affectedUsersCount;
  }
  /**
   * @return string
   */
  public function getAffectedUsersCount()
  {
    return $this->affectedUsersCount;
  }
  /**
   * Approximate total number of events in the given group that match the filter
   * criteria.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Approximate first occurrence that was ever seen for this group and which
   * matches the given filter criteria, ignoring the time_range that was
   * specified in the request.
   *
   * @param string $firstSeenTime
   */
  public function setFirstSeenTime($firstSeenTime)
  {
    $this->firstSeenTime = $firstSeenTime;
  }
  /**
   * @return string
   */
  public function getFirstSeenTime()
  {
    return $this->firstSeenTime;
  }
  /**
   * Group data that is independent of the filter criteria.
   *
   * @param ErrorGroup $group
   */
  public function setGroup(ErrorGroup $group)
  {
    $this->group = $group;
  }
  /**
   * @return ErrorGroup
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Approximate last occurrence that was ever seen for this group and which
   * matches the given filter criteria, ignoring the time_range that was
   * specified in the request.
   *
   * @param string $lastSeenTime
   */
  public function setLastSeenTime($lastSeenTime)
  {
    $this->lastSeenTime = $lastSeenTime;
  }
  /**
   * @return string
   */
  public function getLastSeenTime()
  {
    return $this->lastSeenTime;
  }
  /**
   * The total number of services with a non-zero error count for the given
   * filter criteria.
   *
   * @param int $numAffectedServices
   */
  public function setNumAffectedServices($numAffectedServices)
  {
    $this->numAffectedServices = $numAffectedServices;
  }
  /**
   * @return int
   */
  public function getNumAffectedServices()
  {
    return $this->numAffectedServices;
  }
  /**
   * An arbitrary event that is chosen as representative for the whole group.
   * The representative event is intended to be used as a quick preview for the
   * whole group. Events in the group are usually sufficiently similar to each
   * other such that showing an arbitrary representative provides insight into
   * the characteristics of the group as a whole.
   *
   * @param ErrorEvent $representative
   */
  public function setRepresentative(ErrorEvent $representative)
  {
    $this->representative = $representative;
  }
  /**
   * @return ErrorEvent
   */
  public function getRepresentative()
  {
    return $this->representative;
  }
  /**
   * Approximate number of occurrences over time. Timed counts returned by
   * ListGroups are guaranteed to be: - Inside the requested time interval -
   * Non-overlapping, and - Ordered by ascending time.
   *
   * @param TimedCount[] $timedCounts
   */
  public function setTimedCounts($timedCounts)
  {
    $this->timedCounts = $timedCounts;
  }
  /**
   * @return TimedCount[]
   */
  public function getTimedCounts()
  {
    return $this->timedCounts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorGroupStats::class, 'Google_Service_Clouderrorreporting_ErrorGroupStats');
