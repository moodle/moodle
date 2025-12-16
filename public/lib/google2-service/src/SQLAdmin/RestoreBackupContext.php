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

class RestoreBackupContext extends \Google\Model
{
  /**
   * The ID of the backup run to restore from.
   *
   * @var string
   */
  public $backupRunId;
  /**
   * The ID of the instance that the backup was taken from.
   *
   * @var string
   */
  public $instanceId;
  /**
   * This is always `sql#restoreBackupContext`.
   *
   * @var string
   */
  public $kind;
  /**
   * The full project ID of the source instance.
   *
   * @var string
   */
  public $project;

  /**
   * The ID of the backup run to restore from.
   *
   * @param string $backupRunId
   */
  public function setBackupRunId($backupRunId)
  {
    $this->backupRunId = $backupRunId;
  }
  /**
   * @return string
   */
  public function getBackupRunId()
  {
    return $this->backupRunId;
  }
  /**
   * The ID of the instance that the backup was taken from.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * This is always `sql#restoreBackupContext`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The full project ID of the source instance.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreBackupContext::class, 'Google_Service_SQLAdmin_RestoreBackupContext');
