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

namespace Google\Service\DoubleClickBidManager;

class ReportKey extends \Google\Model
{
  /**
   * Output only. The unique ID of the query that generated the report.
   *
   * @var string
   */
  public $queryId;
  /**
   * Output only. The unique ID of the report.
   *
   * @var string
   */
  public $reportId;

  /**
   * Output only. The unique ID of the query that generated the report.
   *
   * @param string $queryId
   */
  public function setQueryId($queryId)
  {
    $this->queryId = $queryId;
  }
  /**
   * @return string
   */
  public function getQueryId()
  {
    return $this->queryId;
  }
  /**
   * Output only. The unique ID of the report.
   *
   * @param string $reportId
   */
  public function setReportId($reportId)
  {
    $this->reportId = $reportId;
  }
  /**
   * @return string
   */
  public function getReportId()
  {
    return $this->reportId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportKey::class, 'Google_Service_DoubleClickBidManager_ReportKey');
