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

namespace Google\Service\Monitoring;

class NotificationChannel extends \Google\Collection
{
  /**
   * Sentinel value used to indicate that the state is unknown, omitted, or is
   * not applicable (as in the case of channels that neither support nor require
   * verification in order to function).
   */
  public const VERIFICATION_STATUS_VERIFICATION_STATUS_UNSPECIFIED = 'VERIFICATION_STATUS_UNSPECIFIED';
  /**
   * The channel has yet to be verified and requires verification to function.
   * Note that this state also applies to the case where the verification
   * process has been initiated by sending a verification code but where the
   * verification code has not been submitted to complete the process.
   */
  public const VERIFICATION_STATUS_UNVERIFIED = 'UNVERIFIED';
  /**
   * It has been proven that notifications can be received on this notification
   * channel and that someone on the project has access to messages that are
   * delivered to that channel.
   */
  public const VERIFICATION_STATUS_VERIFIED = 'VERIFIED';
  protected $collection_key = 'mutationRecords';
  protected $creationRecordType = MutationRecord::class;
  protected $creationRecordDataType = '';
  /**
   * An optional human-readable description of this notification channel. This
   * description may provide additional details, beyond the display name, for
   * the channel. This may not exceed 1024 Unicode characters.
   *
   * @var string
   */
  public $description;
  /**
   * An optional human-readable name for this notification channel. It is
   * recommended that you specify a non-empty and unique name in order to make
   * it easier to identify the channels in your project, though this is not
   * enforced. The display name is limited to 512 Unicode characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Whether notifications are forwarded to the described channel. This makes it
   * possible to disable delivery of notifications to a particular channel
   * without removing the channel from all alerting policies that reference the
   * channel. This is a more convenient approach when the change is temporary
   * and you want to receive notifications from the same set of alerting
   * policies on the channel at some point in the future.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Configuration fields that define the channel and its behavior. The
   * permissible and required labels are specified in the
   * NotificationChannelDescriptor.labels of the NotificationChannelDescriptor
   * corresponding to the type field.
   *
   * @var string[]
   */
  public $labels;
  protected $mutationRecordsType = MutationRecord::class;
  protected $mutationRecordsDataType = 'array';
  /**
   * Identifier. The full REST resource name for this channel. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/notificationChannels/[CHANNEL_ID] The
   * [CHANNEL_ID] is automatically assigned by the server on creation.
   *
   * @var string
   */
  public $name;
  /**
   * The type of the notification channel. This field matches the value of the
   * NotificationChannelDescriptor.type field.
   *
   * @var string
   */
  public $type;
  /**
   * User-supplied key/value data that does not need to conform to the
   * corresponding NotificationChannelDescriptor's schema, unlike the labels
   * field. This field is intended to be used for organizing and identifying the
   * NotificationChannel objects.The field can contain up to 64 entries. Each
   * key and value is limited to 63 Unicode characters or 128 bytes, whichever
   * is smaller. Labels and values can contain only lowercase letters, numerals,
   * underscores, and dashes. Keys must begin with a letter.
   *
   * @var string[]
   */
  public $userLabels;
  /**
   * Indicates whether this channel has been verified or not. On a
   * ListNotificationChannels or GetNotificationChannel operation, this field is
   * expected to be populated.If the value is UNVERIFIED, then it indicates that
   * the channel is non-functioning (it both requires verification and lacks
   * verification); otherwise, it is assumed that the channel works.If the
   * channel is neither VERIFIED nor UNVERIFIED, it implies that the channel is
   * of a type that does not require verification or that this specific channel
   * has been exempted from verification because it was created prior to
   * verification being required for channels of this type.This field cannot be
   * modified using a standard UpdateNotificationChannel operation. To change
   * the value of this field, you must call VerifyNotificationChannel.
   *
   * @var string
   */
  public $verificationStatus;

  /**
   * Record of the creation of this channel.
   *
   * @param MutationRecord $creationRecord
   */
  public function setCreationRecord(MutationRecord $creationRecord)
  {
    $this->creationRecord = $creationRecord;
  }
  /**
   * @return MutationRecord
   */
  public function getCreationRecord()
  {
    return $this->creationRecord;
  }
  /**
   * An optional human-readable description of this notification channel. This
   * description may provide additional details, beyond the display name, for
   * the channel. This may not exceed 1024 Unicode characters.
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
   * An optional human-readable name for this notification channel. It is
   * recommended that you specify a non-empty and unique name in order to make
   * it easier to identify the channels in your project, though this is not
   * enforced. The display name is limited to 512 Unicode characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Whether notifications are forwarded to the described channel. This makes it
   * possible to disable delivery of notifications to a particular channel
   * without removing the channel from all alerting policies that reference the
   * channel. This is a more convenient approach when the change is temporary
   * and you want to receive notifications from the same set of alerting
   * policies on the channel at some point in the future.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Configuration fields that define the channel and its behavior. The
   * permissible and required labels are specified in the
   * NotificationChannelDescriptor.labels of the NotificationChannelDescriptor
   * corresponding to the type field.
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
   * Records of the modification of this channel.
   *
   * @param MutationRecord[] $mutationRecords
   */
  public function setMutationRecords($mutationRecords)
  {
    $this->mutationRecords = $mutationRecords;
  }
  /**
   * @return MutationRecord[]
   */
  public function getMutationRecords()
  {
    return $this->mutationRecords;
  }
  /**
   * Identifier. The full REST resource name for this channel. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/notificationChannels/[CHANNEL_ID] The
   * [CHANNEL_ID] is automatically assigned by the server on creation.
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
   * The type of the notification channel. This field matches the value of the
   * NotificationChannelDescriptor.type field.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * User-supplied key/value data that does not need to conform to the
   * corresponding NotificationChannelDescriptor's schema, unlike the labels
   * field. This field is intended to be used for organizing and identifying the
   * NotificationChannel objects.The field can contain up to 64 entries. Each
   * key and value is limited to 63 Unicode characters or 128 bytes, whichever
   * is smaller. Labels and values can contain only lowercase letters, numerals,
   * underscores, and dashes. Keys must begin with a letter.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
  /**
   * Indicates whether this channel has been verified or not. On a
   * ListNotificationChannels or GetNotificationChannel operation, this field is
   * expected to be populated.If the value is UNVERIFIED, then it indicates that
   * the channel is non-functioning (it both requires verification and lacks
   * verification); otherwise, it is assumed that the channel works.If the
   * channel is neither VERIFIED nor UNVERIFIED, it implies that the channel is
   * of a type that does not require verification or that this specific channel
   * has been exempted from verification because it was created prior to
   * verification being required for channels of this type.This field cannot be
   * modified using a standard UpdateNotificationChannel operation. To change
   * the value of this field, you must call VerifyNotificationChannel.
   *
   * Accepted values: VERIFICATION_STATUS_UNSPECIFIED, UNVERIFIED, VERIFIED
   *
   * @param self::VERIFICATION_STATUS_* $verificationStatus
   */
  public function setVerificationStatus($verificationStatus)
  {
    $this->verificationStatus = $verificationStatus;
  }
  /**
   * @return self::VERIFICATION_STATUS_*
   */
  public function getVerificationStatus()
  {
    return $this->verificationStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationChannel::class, 'Google_Service_Monitoring_NotificationChannel');
