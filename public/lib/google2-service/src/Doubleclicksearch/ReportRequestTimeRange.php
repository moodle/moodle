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

namespace Google\Service\Doubleclicksearch;

class ReportRequestTimeRange extends \Google\Model
{
  /**
   * Inclusive UTC timestamp in RFC format, e.g., `2013-07-16T10:16:23.555Z`.
   * See additional references on how changed attribute reports work.
   *
   * @var string
   */
  public $changedAttributesSinceTimestamp;
  /**
   * Inclusive UTC timestamp in RFC format, e.g., `2013-07-16T10:16:23.555Z`.
   * See additional references on how changed metrics reports work.
   *
   * @var string
   */
  public $changedMetricsSinceTimestamp;
  /**
   * Inclusive date in YYYY-MM-DD format.
   *
   * @var string
   */
  public $endDate;
  /**
   * Inclusive date in YYYY-MM-DD format.
   *
   * @var string
   */
  public $startDate;

  /**
   * Inclusive UTC timestamp in RFC format, e.g., `2013-07-16T10:16:23.555Z`.
   * See additional references on how changed attribute reports work.
   *
   * @param string $changedAttributesSinceTimestamp
   */
  public function setChangedAttributesSinceTimestamp($changedAttributesSinceTimestamp)
  {
    $this->changedAttributesSinceTimestamp = $changedAttributesSinceTimestamp;
  }
  /**
   * @return string
   */
  public function getChangedAttributesSinceTimestamp()
  {
    return $this->changedAttributesSinceTimestamp;
  }
  /**
   * Inclusive UTC timestamp in RFC format, e.g., `2013-07-16T10:16:23.555Z`.
   * See additional references on how changed metrics reports work.
   *
   * @param string $changedMetricsSinceTimestamp
   */
  public function setChangedMetricsSinceTimestamp($changedMetricsSinceTimestamp)
  {
    $this->changedMetricsSinceTimestamp = $changedMetricsSinceTimestamp;
  }
  /**
   * @return string
   */
  public function getChangedMetricsSinceTimestamp()
  {
    return $this->changedMetricsSinceTimestamp;
  }
  /**
   * Inclusive date in YYYY-MM-DD format.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Inclusive date in YYYY-MM-DD format.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportRequestTimeRange::class, 'Google_Service_Doubleclicksearch_ReportRequestTimeRange');
