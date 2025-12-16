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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DataProfilePubSubMessage extends \Google\Model
{
  /**
   * Unused.
   */
  public const EVENT_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * New profile (not a re-profile).
   */
  public const EVENT_NEW_PROFILE = 'NEW_PROFILE';
  /**
   * One of the following profile metrics changed: Data risk score, Sensitivity
   * score, Resource visibility, Encryption type, Predicted infoTypes, Other
   * infoTypes
   */
  public const EVENT_CHANGED_PROFILE = 'CHANGED_PROFILE';
  /**
   * Table data risk score or sensitivity score increased.
   */
  public const EVENT_SCORE_INCREASED = 'SCORE_INCREASED';
  /**
   * A user (non-internal) error occurred.
   */
  public const EVENT_ERROR_CHANGED = 'ERROR_CHANGED';
  /**
   * The event that caused the Pub/Sub message to be sent.
   *
   * @var string
   */
  public $event;
  protected $fileStoreProfileType = GooglePrivacyDlpV2FileStoreDataProfile::class;
  protected $fileStoreProfileDataType = '';
  protected $profileType = GooglePrivacyDlpV2TableDataProfile::class;
  protected $profileDataType = '';

  /**
   * The event that caused the Pub/Sub message to be sent.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, NEW_PROFILE, CHANGED_PROFILE,
   * SCORE_INCREASED, ERROR_CHANGED
   *
   * @param self::EVENT_* $event
   */
  public function setEvent($event)
  {
    $this->event = $event;
  }
  /**
   * @return self::EVENT_*
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * If `DetailLevel` is `FILE_STORE_PROFILE` this will be fully populated.
   * Otherwise, if `DetailLevel` is `RESOURCE_NAME`, then only `name` and
   * `file_store_path` will be populated.
   *
   * @param GooglePrivacyDlpV2FileStoreDataProfile $fileStoreProfile
   */
  public function setFileStoreProfile(GooglePrivacyDlpV2FileStoreDataProfile $fileStoreProfile)
  {
    $this->fileStoreProfile = $fileStoreProfile;
  }
  /**
   * @return GooglePrivacyDlpV2FileStoreDataProfile
   */
  public function getFileStoreProfile()
  {
    return $this->fileStoreProfile;
  }
  /**
   * If `DetailLevel` is `TABLE_PROFILE` this will be fully populated.
   * Otherwise, if `DetailLevel` is `RESOURCE_NAME`, then only `name` and
   * `full_resource` will be populated.
   *
   * @param GooglePrivacyDlpV2TableDataProfile $profile
   */
  public function setProfile(GooglePrivacyDlpV2TableDataProfile $profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return GooglePrivacyDlpV2TableDataProfile
   */
  public function getProfile()
  {
    return $this->profile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DataProfilePubSubMessage::class, 'Google_Service_DLP_GooglePrivacyDlpV2DataProfilePubSubMessage');
