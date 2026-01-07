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

class SapWorkloadOverview extends \Google\Model
{
  /**
   * @var string
   */
  public $appSid;
  /**
   * @var string
   */
  public $dbSid;
  /**
   * @var string
   */
  public $sapSystemId;

  /**
   * @param string
   */
  public function setAppSid($appSid)
  {
    $this->appSid = $appSid;
  }
  /**
   * @return string
   */
  public function getAppSid()
  {
    return $this->appSid;
  }
  /**
   * @param string
   */
  public function setDbSid($dbSid)
  {
    $this->dbSid = $dbSid;
  }
  /**
   * @return string
   */
  public function getDbSid()
  {
    return $this->dbSid;
  }
  /**
   * @param string
   */
  public function setSapSystemId($sapSystemId)
  {
    $this->sapSystemId = $sapSystemId;
  }
  /**
   * @return string
   */
  public function getSapSystemId()
  {
    return $this->sapSystemId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapWorkloadOverview::class, 'Google_Service_WorkloadManager_SapWorkloadOverview');
