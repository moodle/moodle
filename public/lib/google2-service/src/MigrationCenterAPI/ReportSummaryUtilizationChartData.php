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

namespace Google\Service\MigrationCenterAPI;

class ReportSummaryUtilizationChartData extends \Google\Model
{
  /**
   * Aggregate value which falls into the "Free" bucket.
   *
   * @var string
   */
  public $free;
  /**
   * Aggregate value which falls into the "Used" bucket.
   *
   * @var string
   */
  public $used;

  /**
   * Aggregate value which falls into the "Free" bucket.
   *
   * @param string $free
   */
  public function setFree($free)
  {
    $this->free = $free;
  }
  /**
   * @return string
   */
  public function getFree()
  {
    return $this->free;
  }
  /**
   * Aggregate value which falls into the "Used" bucket.
   *
   * @param string $used
   */
  public function setUsed($used)
  {
    $this->used = $used;
  }
  /**
   * @return string
   */
  public function getUsed()
  {
    return $this->used;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSummaryUtilizationChartData::class, 'Google_Service_MigrationCenterAPI_ReportSummaryUtilizationChartData');
