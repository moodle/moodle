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

namespace Google\Service\SQLAdmin;

class PITRFields extends \Google\Model
{
  /**
   * @var bool
   */
  public $enableBinLog;
  /**
   * @var bool
   */
  public $replicationLogArchivingEnabled;
  /**
   * @var bool
   */
  public $sqlserverPitrEnabled;
  /**
   * @var int
   */
  public $transactionLogRetentionDays;

  /**
   * @param bool
   */
  public function setEnableBinLog($enableBinLog)
  {
    $this->enableBinLog = $enableBinLog;
  }
  /**
   * @return bool
   */
  public function getEnableBinLog()
  {
    return $this->enableBinLog;
  }
  /**
   * @param bool
   */
  public function setReplicationLogArchivingEnabled($replicationLogArchivingEnabled)
  {
    $this->replicationLogArchivingEnabled = $replicationLogArchivingEnabled;
  }
  /**
   * @return bool
   */
  public function getReplicationLogArchivingEnabled()
  {
    return $this->replicationLogArchivingEnabled;
  }
  /**
   * @param bool
   */
  public function setSqlserverPitrEnabled($sqlserverPitrEnabled)
  {
    $this->sqlserverPitrEnabled = $sqlserverPitrEnabled;
  }
  /**
   * @return bool
   */
  public function getSqlserverPitrEnabled()
  {
    return $this->sqlserverPitrEnabled;
  }
  /**
   * @param int
   */
  public function setTransactionLogRetentionDays($transactionLogRetentionDays)
  {
    $this->transactionLogRetentionDays = $transactionLogRetentionDays;
  }
  /**
   * @return int
   */
  public function getTransactionLogRetentionDays()
  {
    return $this->transactionLogRetentionDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PITRFields::class, 'Google_Service_SQLAdmin_PITRFields');
