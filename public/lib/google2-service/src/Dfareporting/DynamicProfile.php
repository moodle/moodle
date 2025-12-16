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

namespace Google\Service\Dfareporting;

class DynamicProfile extends \Google\Model
{
  /**
   * The dynamic profile archive status is unknown. This value is unused.
   */
  public const ARCHIVE_STATUS_ARCHIVE_STATUS_UNKNOWN = 'ARCHIVE_STATUS_UNKNOWN';
  /**
   * The dynamic profile archive status is unarchived.
   */
  public const ARCHIVE_STATUS_UNARCHIVED = 'UNARCHIVED';
  /**
   * The dynamic profile archive status is archived.
   */
  public const ARCHIVE_STATUS_ARCHIVED = 'ARCHIVED';
  /**
   * The dynamic profile status is unknown. This value is unused.
   */
  public const STATUS_STATUS_UNKNOWN = 'STATUS_UNKNOWN';
  /**
   * The dynamic profile is active.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * The dynamic profile is inactive.
   */
  public const STATUS_INACTIVE = 'INACTIVE';
  /**
   * The dynamic profile is deleted.
   */
  public const STATUS_DELETED = 'DELETED';
  protected $activeType = DynamicProfileVersion::class;
  protected $activeDataType = '';
  /**
   * Optional. Archive status of this dynamic profile.
   *
   * @var string
   */
  public $archiveStatus;
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  /**
   * Optional. Description of this dynamic profile.
   *
   * @var string
   */
  public $description;
  protected $draftType = DynamicProfileVersion::class;
  protected $draftDataType = '';
  /**
   * Output only. Unique ID of this dynamic profile. This is a read-only, auto-
   * generated field.
   *
   * @var string
   */
  public $dynamicProfileId;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string "dfareporting#dynamicProfile".
   *
   * @var string
   */
  public $kind;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  /**
   * Required. Identifier. Name of this dynamic profile. This is a required
   * field and must be less than 256 characters long.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Status of this dynamic profile.
   *
   * @var string
   */
  public $status;
  /**
   * Required. Advertiser ID of this dynamic profile. This is a required field
   * on insertion.
   *
   * @var string
   */
  public $studioAdvertiserId;

  /**
   * Optional. Active version of the dynamic profile.
   *
   * @param DynamicProfileVersion $active
   */
  public function setActive(DynamicProfileVersion $active)
  {
    $this->active = $active;
  }
  /**
   * @return DynamicProfileVersion
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Optional. Archive status of this dynamic profile.
   *
   * Accepted values: ARCHIVE_STATUS_UNKNOWN, UNARCHIVED, ARCHIVED
   *
   * @param self::ARCHIVE_STATUS_* $archiveStatus
   */
  public function setArchiveStatus($archiveStatus)
  {
    $this->archiveStatus = $archiveStatus;
  }
  /**
   * @return self::ARCHIVE_STATUS_*
   */
  public function getArchiveStatus()
  {
    return $this->archiveStatus;
  }
  /**
   * Output only. The creation timestamp of the dynamic profile. This is a read-
   * only field.
   *
   * @param LastModifiedInfo $createInfo
   */
  public function setCreateInfo(LastModifiedInfo $createInfo)
  {
    $this->createInfo = $createInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getCreateInfo()
  {
    return $this->createInfo;
  }
  /**
   * Optional. Description of this dynamic profile.
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
   * Optional. Draft version of the dynamic profile.
   *
   * @param DynamicProfileVersion $draft
   */
  public function setDraft(DynamicProfileVersion $draft)
  {
    $this->draft = $draft;
  }
  /**
   * @return DynamicProfileVersion
   */
  public function getDraft()
  {
    return $this->draft;
  }
  /**
   * Output only. Unique ID of this dynamic profile. This is a read-only, auto-
   * generated field.
   *
   * @param string $dynamicProfileId
   */
  public function setDynamicProfileId($dynamicProfileId)
  {
    $this->dynamicProfileId = $dynamicProfileId;
  }
  /**
   * @return string
   */
  public function getDynamicProfileId()
  {
    return $this->dynamicProfileId;
  }
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string "dfareporting#dynamicProfile".
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
   * Output only. The last modified timestamp of the dynamic profile. This is a
   * read-only field.
   *
   * @param LastModifiedInfo $lastModifiedInfo
   */
  public function setLastModifiedInfo(LastModifiedInfo $lastModifiedInfo)
  {
    $this->lastModifiedInfo = $lastModifiedInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getLastModifiedInfo()
  {
    return $this->lastModifiedInfo;
  }
  /**
   * Required. Identifier. Name of this dynamic profile. This is a required
   * field and must be less than 256 characters long.
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
   * Optional. Status of this dynamic profile.
   *
   * Accepted values: STATUS_UNKNOWN, ACTIVE, INACTIVE, DELETED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Required. Advertiser ID of this dynamic profile. This is a required field
   * on insertion.
   *
   * @param string $studioAdvertiserId
   */
  public function setStudioAdvertiserId($studioAdvertiserId)
  {
    $this->studioAdvertiserId = $studioAdvertiserId;
  }
  /**
   * @return string
   */
  public function getStudioAdvertiserId()
  {
    return $this->studioAdvertiserId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicProfile::class, 'Google_Service_Dfareporting_DynamicProfile');
