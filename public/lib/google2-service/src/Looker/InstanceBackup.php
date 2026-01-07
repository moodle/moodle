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

namespace Google\Service\Looker;

class InstanceBackup extends \Google\Model
{
  /**
   * The state of the backup is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The backup is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The backup is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The backup is active and ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The backup failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The time when the backup was started.
   *
   * @var string
   */
  public $createTime;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Output only. The time when the backup will be deleted.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Immutable. The relative resource name of the backup, in the following form:
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}/
   * backups/{backup}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of the backup.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The time when the backup was started.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Current status of the CMEK encryption
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Output only. The time when the backup will be deleted.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Immutable. The relative resource name of the backup, in the following form:
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}/
   * backups/{backup}`
   *
   * @param string $name
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
  /**
   * Output only. The current state of the backup.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, DELETING, ACTIVE, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceBackup::class, 'Google_Service_Looker_InstanceBackup');
