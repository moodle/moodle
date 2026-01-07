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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2LabelLock extends \Google\Model
{
  /**
   * Unknown state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The label lock is active and is being enforced by the server.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The label lock is being deleted. The label lock will continue to be
   * enforced by the server until it has been fully removed.
   */
  public const STATE_DELETING = 'DELETING';
  protected $capabilitiesType = GoogleAppsDriveLabelsV2LabelLockCapabilities::class;
  protected $capabilitiesDataType = '';
  /**
   * The ID of the selection field choice that should be locked. If present,
   * `field_id` must also be present.
   *
   * @var string
   */
  public $choiceId;
  /**
   * Output only. The time this label lock was created.
   *
   * @var string
   */
  public $createTime;
  protected $creatorType = GoogleAppsDriveLabelsV2UserInfo::class;
  protected $creatorDataType = '';
  /**
   * Output only. A timestamp indicating when this label lock was scheduled for
   * deletion. Present only if this label lock is in the `DELETING` state.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * The ID of the field that should be locked. Empty if the whole label should
   * be locked.
   *
   * @var string
   */
  public $fieldId;
  /**
   * Output only. Resource name of this label lock.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. This label lock's state.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The user's capabilities on this label lock.
   *
   * @param GoogleAppsDriveLabelsV2LabelLockCapabilities $capabilities
   */
  public function setCapabilities(GoogleAppsDriveLabelsV2LabelLockCapabilities $capabilities)
  {
    $this->capabilities = $capabilities;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelLockCapabilities
   */
  public function getCapabilities()
  {
    return $this->capabilities;
  }
  /**
   * The ID of the selection field choice that should be locked. If present,
   * `field_id` must also be present.
   *
   * @param string $choiceId
   */
  public function setChoiceId($choiceId)
  {
    $this->choiceId = $choiceId;
  }
  /**
   * @return string
   */
  public function getChoiceId()
  {
    return $this->choiceId;
  }
  /**
   * Output only. The time this label lock was created.
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
   * Output only. The user whose credentials were used to create the label lock.
   * Not present if no user was responsible for creating the label lock.
   *
   * @param GoogleAppsDriveLabelsV2UserInfo $creator
   */
  public function setCreator(GoogleAppsDriveLabelsV2UserInfo $creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return GoogleAppsDriveLabelsV2UserInfo
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Output only. A timestamp indicating when this label lock was scheduled for
   * deletion. Present only if this label lock is in the `DELETING` state.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * The ID of the field that should be locked. Empty if the whole label should
   * be locked.
   *
   * @param string $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return string
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * Output only. Resource name of this label lock.
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
   * Output only. This label lock's state.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DELETING
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
class_alias(GoogleAppsDriveLabelsV2LabelLock::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2LabelLock');
