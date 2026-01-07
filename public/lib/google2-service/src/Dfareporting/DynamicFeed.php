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

class DynamicFeed extends \Google\Model
{
  /**
   * The status is unknown.
   */
  public const STATUS_STATUS_UNKNOWN = 'STATUS_UNKNOWN';
  /**
   * The feedstatus is active.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * The feed status is inactive.
   */
  public const STATUS_INACTIVE = 'INACTIVE';
  /**
   * The feed status is deleted.
   */
  public const STATUS_DELETED = 'DELETED';
  protected $contentSourceType = ContentSource::class;
  protected $contentSourceDataType = '';
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  /**
   * Output only. Unique ID of this dynamic feed. This is a read-only, auto-
   * generated field.
   *
   * @var string
   */
  public $dynamicFeedId;
  /**
   * Optional. Name of this dynamic feed. It is defaulted to content source file
   * name if not provided.
   *
   * @var string
   */
  public $dynamicFeedName;
  protected $elementType = Element::class;
  protected $elementDataType = '';
  protected $feedIngestionStatusType = FeedIngestionStatus::class;
  protected $feedIngestionStatusDataType = '';
  protected $feedScheduleType = FeedSchedule::class;
  protected $feedScheduleDataType = '';
  /**
   * Output only. Indicates whether the dynamic feed has a published version.
   * This is a read-only field.
   *
   * @var bool
   */
  public $hasPublished;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  /**
   * Output only. The status of the feed. It is a read-only field that depends
   * on the the feed ingestion status. The default value is INACTIVE, and it
   * will be updated to ACTIVE once the feed is ingested successfully.
   *
   * @var string
   */
  public $status;
  /**
   * Required. Advertiser ID of this dynamic feed. This is a required field.
   *
   * @var string
   */
  public $studioAdvertiserId;

  /**
   * Required. The content source of the dynamic feed. This is a required field.
   *
   * @param ContentSource $contentSource
   */
  public function setContentSource(ContentSource $contentSource)
  {
    $this->contentSource = $contentSource;
  }
  /**
   * @return ContentSource
   */
  public function getContentSource()
  {
    return $this->contentSource;
  }
  /**
   * Output only. The creation timestamp of the dynamic feed. This is a read-
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
   * Output only. Unique ID of this dynamic feed. This is a read-only, auto-
   * generated field.
   *
   * @param string $dynamicFeedId
   */
  public function setDynamicFeedId($dynamicFeedId)
  {
    $this->dynamicFeedId = $dynamicFeedId;
  }
  /**
   * @return string
   */
  public function getDynamicFeedId()
  {
    return $this->dynamicFeedId;
  }
  /**
   * Optional. Name of this dynamic feed. It is defaulted to content source file
   * name if not provided.
   *
   * @param string $dynamicFeedName
   */
  public function setDynamicFeedName($dynamicFeedName)
  {
    $this->dynamicFeedName = $dynamicFeedName;
  }
  /**
   * @return string
   */
  public function getDynamicFeedName()
  {
    return $this->dynamicFeedName;
  }
  /**
   * Required. The element of the dynamic feed that is to specify the schema of
   * the feed. This is a required field.
   *
   * @param Element $element
   */
  public function setElement(Element $element)
  {
    $this->element = $element;
  }
  /**
   * @return Element
   */
  public function getElement()
  {
    return $this->element;
  }
  /**
   * Output only. The ingestion status of the dynamic feed. This is a read-only
   * field.
   *
   * @param FeedIngestionStatus $feedIngestionStatus
   */
  public function setFeedIngestionStatus(FeedIngestionStatus $feedIngestionStatus)
  {
    $this->feedIngestionStatus = $feedIngestionStatus;
  }
  /**
   * @return FeedIngestionStatus
   */
  public function getFeedIngestionStatus()
  {
    return $this->feedIngestionStatus;
  }
  /**
   * Optional. The schedule of the dynamic feed. It can be set if the feed is
   * published.
   *
   * @param FeedSchedule $feedSchedule
   */
  public function setFeedSchedule(FeedSchedule $feedSchedule)
  {
    $this->feedSchedule = $feedSchedule;
  }
  /**
   * @return FeedSchedule
   */
  public function getFeedSchedule()
  {
    return $this->feedSchedule;
  }
  /**
   * Output only. Indicates whether the dynamic feed has a published version.
   * This is a read-only field.
   *
   * @param bool $hasPublished
   */
  public function setHasPublished($hasPublished)
  {
    $this->hasPublished = $hasPublished;
  }
  /**
   * @return bool
   */
  public function getHasPublished()
  {
    return $this->hasPublished;
  }
  /**
   * Output only. The last modified timestamp of the dynamic feed. This is a
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
   * Output only. The status of the feed. It is a read-only field that depends
   * on the the feed ingestion status. The default value is INACTIVE, and it
   * will be updated to ACTIVE once the feed is ingested successfully.
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
   * Required. Advertiser ID of this dynamic feed. This is a required field.
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
class_alias(DynamicFeed::class, 'Google_Service_Dfareporting_DynamicFeed');
