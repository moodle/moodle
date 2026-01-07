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

namespace Google\Service\Dataproc;

class ProcessSummary extends \Google\Model
{
  /**
   * @var string
   */
  public $addTime;
  /**
   * @var string
   */
  public $hostPort;
  /**
   * @var bool
   */
  public $isActive;
  /**
   * @var string
   */
  public $processId;
  /**
   * @var string[]
   */
  public $processLogs;
  /**
   * @var string
   */
  public $removeTime;
  /**
   * @var int
   */
  public $totalCores;

  /**
   * @param string $addTime
   */
  public function setAddTime($addTime)
  {
    $this->addTime = $addTime;
  }
  /**
   * @return string
   */
  public function getAddTime()
  {
    return $this->addTime;
  }
  /**
   * @param string $hostPort
   */
  public function setHostPort($hostPort)
  {
    $this->hostPort = $hostPort;
  }
  /**
   * @return string
   */
  public function getHostPort()
  {
    return $this->hostPort;
  }
  /**
   * @param bool $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }
  /**
   * @return bool
   */
  public function getIsActive()
  {
    return $this->isActive;
  }
  /**
   * @param string $processId
   */
  public function setProcessId($processId)
  {
    $this->processId = $processId;
  }
  /**
   * @return string
   */
  public function getProcessId()
  {
    return $this->processId;
  }
  /**
   * @param string[] $processLogs
   */
  public function setProcessLogs($processLogs)
  {
    $this->processLogs = $processLogs;
  }
  /**
   * @return string[]
   */
  public function getProcessLogs()
  {
    return $this->processLogs;
  }
  /**
   * @param string $removeTime
   */
  public function setRemoveTime($removeTime)
  {
    $this->removeTime = $removeTime;
  }
  /**
   * @return string
   */
  public function getRemoveTime()
  {
    return $this->removeTime;
  }
  /**
   * @param int $totalCores
   */
  public function setTotalCores($totalCores)
  {
    $this->totalCores = $totalCores;
  }
  /**
   * @return int
   */
  public function getTotalCores()
  {
    return $this->totalCores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProcessSummary::class, 'Google_Service_Dataproc_ProcessSummary');
