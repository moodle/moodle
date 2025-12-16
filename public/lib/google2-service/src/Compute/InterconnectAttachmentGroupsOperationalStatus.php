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

namespace Google\Service\Compute;

class InterconnectAttachmentGroupsOperationalStatus extends \Google\Collection
{
  public const GROUP_STATUS_DEGRADED = 'DEGRADED';
  public const GROUP_STATUS_FULLY_DOWN = 'FULLY_DOWN';
  public const GROUP_STATUS_FULLY_UP = 'FULLY_UP';
  public const GROUP_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  protected $collection_key = 'attachmentStatuses';
  protected $attachmentStatusesType = InterconnectAttachmentGroupsOperationalStatusAttachmentStatus::class;
  protected $attachmentStatusesDataType = 'array';
  protected $configuredType = InterconnectAttachmentGroupConfigured::class;
  protected $configuredDataType = '';
  /**
   * Output only. Summarizes the status of the group.
   *
   * @var string
   */
  public $groupStatus;
  protected $intentType = InterconnectAttachmentGroupIntent::class;
  protected $intentDataType = '';
  protected $operationalType = InterconnectAttachmentGroupConfigured::class;
  protected $operationalDataType = '';

  /**
   * @param InterconnectAttachmentGroupsOperationalStatusAttachmentStatus[] $attachmentStatuses
   */
  public function setAttachmentStatuses($attachmentStatuses)
  {
    $this->attachmentStatuses = $attachmentStatuses;
  }
  /**
   * @return InterconnectAttachmentGroupsOperationalStatusAttachmentStatus[]
   */
  public function getAttachmentStatuses()
  {
    return $this->attachmentStatuses;
  }
  /**
   * @param InterconnectAttachmentGroupConfigured $configured
   */
  public function setConfigured(InterconnectAttachmentGroupConfigured $configured)
  {
    $this->configured = $configured;
  }
  /**
   * @return InterconnectAttachmentGroupConfigured
   */
  public function getConfigured()
  {
    return $this->configured;
  }
  /**
   * Output only. Summarizes the status of the group.
   *
   * Accepted values: DEGRADED, FULLY_DOWN, FULLY_UP, UNSPECIFIED
   *
   * @param self::GROUP_STATUS_* $groupStatus
   */
  public function setGroupStatus($groupStatus)
  {
    $this->groupStatus = $groupStatus;
  }
  /**
   * @return self::GROUP_STATUS_*
   */
  public function getGroupStatus()
  {
    return $this->groupStatus;
  }
  /**
   * @param InterconnectAttachmentGroupIntent $intent
   */
  public function setIntent(InterconnectAttachmentGroupIntent $intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return InterconnectAttachmentGroupIntent
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * Output only. The operational state of the group, including only active
   * Attachments.
   *
   * @param InterconnectAttachmentGroupConfigured $operational
   */
  public function setOperational(InterconnectAttachmentGroupConfigured $operational)
  {
    $this->operational = $operational;
  }
  /**
   * @return InterconnectAttachmentGroupConfigured
   */
  public function getOperational()
  {
    return $this->operational;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentGroupsOperationalStatus::class, 'Google_Service_Compute_InterconnectAttachmentGroupsOperationalStatus');
