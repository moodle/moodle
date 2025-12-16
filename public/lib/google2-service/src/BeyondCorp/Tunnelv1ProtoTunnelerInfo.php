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

namespace Google\Service\BeyondCorp;

class Tunnelv1ProtoTunnelerInfo extends \Google\Model
{
  /**
   * backoff_retry_count stores the number of times the tunneler has been
   * retried by tunManager for current backoff sequence. Gets reset to 0 if time
   * difference between 2 consecutive retries exceeds backoffRetryResetTime.
   *
   * @var string
   */
  public $backoffRetryCount;
  /**
   * id is the unique id of a tunneler.
   *
   * @var string
   */
  public $id;
  protected $latestErrType = Tunnelv1ProtoTunnelerError::class;
  protected $latestErrDataType = '';
  /**
   * latest_retry_time stores the time when the tunneler was last restarted.
   *
   * @var string
   */
  public $latestRetryTime;
  /**
   * total_retry_count stores the total number of times the tunneler has been
   * retried by tunManager.
   *
   * @var string
   */
  public $totalRetryCount;

  /**
   * backoff_retry_count stores the number of times the tunneler has been
   * retried by tunManager for current backoff sequence. Gets reset to 0 if time
   * difference between 2 consecutive retries exceeds backoffRetryResetTime.
   *
   * @param string $backoffRetryCount
   */
  public function setBackoffRetryCount($backoffRetryCount)
  {
    $this->backoffRetryCount = $backoffRetryCount;
  }
  /**
   * @return string
   */
  public function getBackoffRetryCount()
  {
    return $this->backoffRetryCount;
  }
  /**
   * id is the unique id of a tunneler.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * latest_err stores the Error for the latest tunneler failure. Gets reset
   * everytime the tunneler is retried by tunManager.
   *
   * @param Tunnelv1ProtoTunnelerError $latestErr
   */
  public function setLatestErr(Tunnelv1ProtoTunnelerError $latestErr)
  {
    $this->latestErr = $latestErr;
  }
  /**
   * @return Tunnelv1ProtoTunnelerError
   */
  public function getLatestErr()
  {
    return $this->latestErr;
  }
  /**
   * latest_retry_time stores the time when the tunneler was last restarted.
   *
   * @param string $latestRetryTime
   */
  public function setLatestRetryTime($latestRetryTime)
  {
    $this->latestRetryTime = $latestRetryTime;
  }
  /**
   * @return string
   */
  public function getLatestRetryTime()
  {
    return $this->latestRetryTime;
  }
  /**
   * total_retry_count stores the total number of times the tunneler has been
   * retried by tunManager.
   *
   * @param string $totalRetryCount
   */
  public function setTotalRetryCount($totalRetryCount)
  {
    $this->totalRetryCount = $totalRetryCount;
  }
  /**
   * @return string
   */
  public function getTotalRetryCount()
  {
    return $this->totalRetryCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Tunnelv1ProtoTunnelerInfo::class, 'Google_Service_BeyondCorp_Tunnelv1ProtoTunnelerInfo');
