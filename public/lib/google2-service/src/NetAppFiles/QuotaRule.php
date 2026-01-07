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

namespace Google\Service\NetAppFiles;

class QuotaRule extends \Google\Model
{
  /**
   * Unspecified state for quota rule
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Quota rule is creating
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Quota rule is updating
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Quota rule is deleting
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Quota rule is ready
   */
  public const STATE_READY = 'READY';
  /**
   * Quota rule is in error state.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Unspecified type for quota rule
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Individual user quota rule
   */
  public const TYPE_INDIVIDUAL_USER_QUOTA = 'INDIVIDUAL_USER_QUOTA';
  /**
   * Individual group quota rule
   */
  public const TYPE_INDIVIDUAL_GROUP_QUOTA = 'INDIVIDUAL_GROUP_QUOTA';
  /**
   * Default user quota rule
   */
  public const TYPE_DEFAULT_USER_QUOTA = 'DEFAULT_USER_QUOTA';
  /**
   * Default group quota rule
   */
  public const TYPE_DEFAULT_GROUP_QUOTA = 'DEFAULT_GROUP_QUOTA';
  /**
   * Output only. Create time of the quota rule
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the quota rule
   *
   * @var string
   */
  public $description;
  /**
   * Required. The maximum allowed disk space in MiB.
   *
   * @var int
   */
  public $diskLimitMib;
  /**
   * Optional. Labels of the quota rule
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the quota rule. Format: `projects/{project
   * _number}/locations/{location_id}/volumes/volumes/{volume_id}/quotaRules/{qu
   * ota_rule_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the quota rule
   *
   * @var string
   */
  public $state;
  /**
   * Output only. State details of the quota rule
   *
   * @var string
   */
  public $stateDetails;
  /**
   * Optional. The quota rule applies to the specified user or group, identified
   * by a Unix UID/GID, Windows SID, or null for default.
   *
   * @var string
   */
  public $target;
  /**
   * Required. The type of quota rule.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Create time of the quota rule
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
   * Optional. Description of the quota rule
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The maximum allowed disk space in MiB.
   *
   * @param int $diskLimitMib
   */
  public function setDiskLimitMib($diskLimitMib)
  {
    $this->diskLimitMib = $diskLimitMib;
  }
  /**
   * @return int
   */
  public function getDiskLimitMib()
  {
    return $this->diskLimitMib;
  }
  /**
   * Optional. Labels of the quota rule
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
   * Identifier. The resource name of the quota rule. Format: `projects/{project
   * _number}/locations/{location_id}/volumes/volumes/{volume_id}/quotaRules/{qu
   * ota_rule_id}`.
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
   * Output only. State of the quota rule
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, UPDATING, DELETING, READY,
   * ERROR
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
   * Output only. State details of the quota rule
   *
   * @param string $stateDetails
   */
  public function setStateDetails($stateDetails)
  {
    $this->stateDetails = $stateDetails;
  }
  /**
   * @return string
   */
  public function getStateDetails()
  {
    return $this->stateDetails;
  }
  /**
   * Optional. The quota rule applies to the specified user or group, identified
   * by a Unix UID/GID, Windows SID, or null for default.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Required. The type of quota rule.
   *
   * Accepted values: TYPE_UNSPECIFIED, INDIVIDUAL_USER_QUOTA,
   * INDIVIDUAL_GROUP_QUOTA, DEFAULT_USER_QUOTA, DEFAULT_GROUP_QUOTA
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuotaRule::class, 'Google_Service_NetAppFiles_QuotaRule');
