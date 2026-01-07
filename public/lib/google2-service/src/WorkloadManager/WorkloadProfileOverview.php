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

namespace Google\Service\WorkloadManager;

class WorkloadProfileOverview extends \Google\Model
{
  protected $sapWorkloadOverviewType = SapWorkloadOverview::class;
  protected $sapWorkloadOverviewDataType = '';
  protected $sqlserverWorkloadOverviewType = SqlserverWorkloadOverview::class;
  protected $sqlserverWorkloadOverviewDataType = '';
  protected $threeTierWorkloadOverviewType = ThreeTierWorkloadOverview::class;
  protected $threeTierWorkloadOverviewDataType = '';

  /**
   * @param SapWorkloadOverview
   */
  public function setSapWorkloadOverview(SapWorkloadOverview $sapWorkloadOverview)
  {
    $this->sapWorkloadOverview = $sapWorkloadOverview;
  }
  /**
   * @return SapWorkloadOverview
   */
  public function getSapWorkloadOverview()
  {
    return $this->sapWorkloadOverview;
  }
  /**
   * @param SqlserverWorkloadOverview
   */
  public function setSqlserverWorkloadOverview(SqlserverWorkloadOverview $sqlserverWorkloadOverview)
  {
    $this->sqlserverWorkloadOverview = $sqlserverWorkloadOverview;
  }
  /**
   * @return SqlserverWorkloadOverview
   */
  public function getSqlserverWorkloadOverview()
  {
    return $this->sqlserverWorkloadOverview;
  }
  /**
   * @param ThreeTierWorkloadOverview
   */
  public function setThreeTierWorkloadOverview(ThreeTierWorkloadOverview $threeTierWorkloadOverview)
  {
    $this->threeTierWorkloadOverview = $threeTierWorkloadOverview;
  }
  /**
   * @return ThreeTierWorkloadOverview
   */
  public function getThreeTierWorkloadOverview()
  {
    return $this->threeTierWorkloadOverview;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkloadProfileOverview::class, 'Google_Service_WorkloadManager_WorkloadProfileOverview');
