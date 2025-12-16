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

class Database extends \Google\Model
{
  /**
   * @var string
   */
  public $backupFile;
  /**
   * @var string
   */
  public $backupSchedule;
  /**
   * @var string
   */
  public $hostVm;
  /**
   * @var string
   */
  public $name;

  /**
   * @param string
   */
  public function setBackupFile($backupFile)
  {
    $this->backupFile = $backupFile;
  }
  /**
   * @return string
   */
  public function getBackupFile()
  {
    return $this->backupFile;
  }
  /**
   * @param string
   */
  public function setBackupSchedule($backupSchedule)
  {
    $this->backupSchedule = $backupSchedule;
  }
  /**
   * @return string
   */
  public function getBackupSchedule()
  {
    return $this->backupSchedule;
  }
  /**
   * @param string
   */
  public function setHostVm($hostVm)
  {
    $this->hostVm = $hostVm;
  }
  /**
   * @return string
   */
  public function getHostVm()
  {
    return $this->hostVm;
  }
  /**
   * @param string
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Database::class, 'Google_Service_WorkloadManager_Database');
