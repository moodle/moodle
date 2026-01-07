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

namespace Google\Service\CloudRedis;

class BackupClusterRequest extends \Google\Model
{
  /**
   * Optional. The id of the backup to be created. If not specified, the default
   * value ([YYYYMMDDHHMMSS]_[Shortened Cluster UID] is used.
   *
   * @var string
   */
  public $backupId;
  /**
   * Optional. TTL for the backup to expire. Value range is 1 day to 100 years.
   * If not specified, the default value is 100 years.
   *
   * @var string
   */
  public $ttl;

  /**
   * Optional. The id of the backup to be created. If not specified, the default
   * value ([YYYYMMDDHHMMSS]_[Shortened Cluster UID] is used.
   *
   * @param string $backupId
   */
  public function setBackupId($backupId)
  {
    $this->backupId = $backupId;
  }
  /**
   * @return string
   */
  public function getBackupId()
  {
    return $this->backupId;
  }
  /**
   * Optional. TTL for the backup to expire. Value range is 1 day to 100 years.
   * If not specified, the default value is 100 years.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupClusterRequest::class, 'Google_Service_CloudRedis_BackupClusterRequest');
