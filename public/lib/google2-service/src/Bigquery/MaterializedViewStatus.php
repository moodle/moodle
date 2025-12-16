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

namespace Google\Service\Bigquery;

class MaterializedViewStatus extends \Google\Model
{
  protected $lastRefreshStatusType = ErrorProto::class;
  protected $lastRefreshStatusDataType = '';
  /**
   * Output only. Refresh watermark of materialized view. The base tables' data
   * were collected into the materialized view cache until this time.
   *
   * @var string
   */
  public $refreshWatermark;

  /**
   * Output only. Error result of the last automatic refresh. If present,
   * indicates that the last automatic refresh was unsuccessful.
   *
   * @param ErrorProto $lastRefreshStatus
   */
  public function setLastRefreshStatus(ErrorProto $lastRefreshStatus)
  {
    $this->lastRefreshStatus = $lastRefreshStatus;
  }
  /**
   * @return ErrorProto
   */
  public function getLastRefreshStatus()
  {
    return $this->lastRefreshStatus;
  }
  /**
   * Output only. Refresh watermark of materialized view. The base tables' data
   * were collected into the materialized view cache until this time.
   *
   * @param string $refreshWatermark
   */
  public function setRefreshWatermark($refreshWatermark)
  {
    $this->refreshWatermark = $refreshWatermark;
  }
  /**
   * @return string
   */
  public function getRefreshWatermark()
  {
    return $this->refreshWatermark;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaterializedViewStatus::class, 'Google_Service_Bigquery_MaterializedViewStatus');
