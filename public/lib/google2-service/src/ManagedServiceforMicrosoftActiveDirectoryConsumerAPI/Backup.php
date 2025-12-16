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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class Backup extends \Google\Model
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Backup is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Backup has been created and validated.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Backup has been created but failed validation.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Backup is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Backup was manually created.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Backup was manually created.
   */
  public const TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * Backup was automatically created.
   */
  public const TYPE_SCHEDULED = 'SCHEDULED';
  /**
   * Output only. The time the backups was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The unique name of the Backup in the form of `projects/{projec
   * t_id}/locations/global/domains/{domain_name}/backups/{name}`
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
   * Output only. Additional information about the current status of this
   * backup, if available.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * Output only. Indicates whether it’s an on-demand backup or scheduled.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Last update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time the backups was created.
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
   * Optional. Resource labels to represent user provided metadata.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The unique name of the Backup in the form of `projects/{projec
   * t_id}/locations/global/domains/{domain_name}/backups/{name}`
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
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, FAILED, DELETING
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
  /**
   * Output only. Additional information about the current status of this
   * backup, if available.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Output only. Indicates whether it’s an on-demand backup or scheduled.
   *
   * Accepted values: TYPE_UNSPECIFIED, ON_DEMAND, SCHEDULED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Last update time.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_Backup');
