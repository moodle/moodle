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

namespace Google\Service\YouTube;

class VideoStatus extends \Google\Model
{
  /**
   * Unable to convert video content.
   */
  public const FAILURE_REASON_conversion = 'conversion';
  /**
   * Invalid file format.
   */
  public const FAILURE_REASON_invalidFile = 'invalidFile';
  /**
   * Empty file.
   */
  public const FAILURE_REASON_emptyFile = 'emptyFile';
  /**
   * File was too small.
   */
  public const FAILURE_REASON_tooSmall = 'tooSmall';
  /**
   * Unsupported codec.
   */
  public const FAILURE_REASON_codec = 'codec';
  /**
   * Upload wasn't finished.
   */
  public const FAILURE_REASON_uploadAborted = 'uploadAborted';
  public const LICENSE_youtube = 'youtube';
  public const LICENSE_creativeCommon = 'creativeCommon';
  public const PRIVACY_STATUS_public = 'public';
  public const PRIVACY_STATUS_unlisted = 'unlisted';
  public const PRIVACY_STATUS_private = 'private';
  /**
   * Copyright infringement.
   */
  public const REJECTION_REASON_copyright = 'copyright';
  /**
   * Inappropriate video content.
   */
  public const REJECTION_REASON_inappropriate = 'inappropriate';
  /**
   * Duplicate upload in the same channel.
   */
  public const REJECTION_REASON_duplicate = 'duplicate';
  /**
   * Terms of use violation.
   */
  public const REJECTION_REASON_termsOfUse = 'termsOfUse';
  /**
   * Uploader account was suspended.
   */
  public const REJECTION_REASON_uploaderAccountSuspended = 'uploaderAccountSuspended';
  /**
   * Video duration was too long.
   */
  public const REJECTION_REASON_length = 'length';
  /**
   * Blocked by content owner.
   */
  public const REJECTION_REASON_claim = 'claim';
  /**
   * Uploader closed his/her account.
   */
  public const REJECTION_REASON_uploaderAccountClosed = 'uploaderAccountClosed';
  /**
   * Trademark infringement.
   */
  public const REJECTION_REASON_trademark = 'trademark';
  /**
   * An unspecified legal reason.
   */
  public const REJECTION_REASON_legal = 'legal';
  /**
   * Video has been uploaded but not processed yet.
   */
  public const UPLOAD_STATUS_uploaded = 'uploaded';
  /**
   * Video has been successfully processed.
   */
  public const UPLOAD_STATUS_processed = 'processed';
  /**
   * Processing has failed. See FailureReason.
   */
  public const UPLOAD_STATUS_failed = 'failed';
  /**
   * Video has been rejected. See RejectionReason.
   */
  public const UPLOAD_STATUS_rejected = 'rejected';
  /**
   * Video has been deleted.
   */
  public const UPLOAD_STATUS_deleted = 'deleted';
  /**
   * Indicates if the video contains altered or synthetic media.
   *
   * @var bool
   */
  public $containsSyntheticMedia;
  /**
   * This value indicates if the video can be embedded on another website.
   * @mutable youtube.videos.insert youtube.videos.update
   *
   * @var bool
   */
  public $embeddable;
  /**
   * This value explains why a video failed to upload. This property is only
   * present if the uploadStatus property indicates that the upload failed.
   *
   * @var string
   */
  public $failureReason;
  /**
   * The video's license. @mutable youtube.videos.insert youtube.videos.update
   *
   * @var string
   */
  public $license;
  /**
   * @var bool
   */
  public $madeForKids;
  /**
   * The video's privacy status.
   *
   * @var string
   */
  public $privacyStatus;
  /**
   * This value indicates if the extended video statistics on the watch page can
   * be viewed by everyone. Note that the view count, likes, etc will still be
   * visible if this is disabled. @mutable youtube.videos.insert
   * youtube.videos.update
   *
   * @var bool
   */
  public $publicStatsViewable;
  /**
   * The date and time when the video is scheduled to publish. It can be set
   * only if the privacy status of the video is private..
   *
   * @var string
   */
  public $publishAt;
  /**
   * This value explains why YouTube rejected an uploaded video. This property
   * is only present if the uploadStatus property indicates that the upload was
   * rejected.
   *
   * @var string
   */
  public $rejectionReason;
  /**
   * @var bool
   */
  public $selfDeclaredMadeForKids;
  /**
   * The status of the uploaded video.
   *
   * @var string
   */
  public $uploadStatus;

  /**
   * Indicates if the video contains altered or synthetic media.
   *
   * @param bool $containsSyntheticMedia
   */
  public function setContainsSyntheticMedia($containsSyntheticMedia)
  {
    $this->containsSyntheticMedia = $containsSyntheticMedia;
  }
  /**
   * @return bool
   */
  public function getContainsSyntheticMedia()
  {
    return $this->containsSyntheticMedia;
  }
  /**
   * This value indicates if the video can be embedded on another website.
   * @mutable youtube.videos.insert youtube.videos.update
   *
   * @param bool $embeddable
   */
  public function setEmbeddable($embeddable)
  {
    $this->embeddable = $embeddable;
  }
  /**
   * @return bool
   */
  public function getEmbeddable()
  {
    return $this->embeddable;
  }
  /**
   * This value explains why a video failed to upload. This property is only
   * present if the uploadStatus property indicates that the upload failed.
   *
   * Accepted values: conversion, invalidFile, emptyFile, tooSmall, codec,
   * uploadAborted
   *
   * @param self::FAILURE_REASON_* $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return self::FAILURE_REASON_*
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * The video's license. @mutable youtube.videos.insert youtube.videos.update
   *
   * Accepted values: youtube, creativeCommon
   *
   * @param self::LICENSE_* $license
   */
  public function setLicense($license)
  {
    $this->license = $license;
  }
  /**
   * @return self::LICENSE_*
   */
  public function getLicense()
  {
    return $this->license;
  }
  /**
   * @param bool $madeForKids
   */
  public function setMadeForKids($madeForKids)
  {
    $this->madeForKids = $madeForKids;
  }
  /**
   * @return bool
   */
  public function getMadeForKids()
  {
    return $this->madeForKids;
  }
  /**
   * The video's privacy status.
   *
   * Accepted values: public, unlisted, private
   *
   * @param self::PRIVACY_STATUS_* $privacyStatus
   */
  public function setPrivacyStatus($privacyStatus)
  {
    $this->privacyStatus = $privacyStatus;
  }
  /**
   * @return self::PRIVACY_STATUS_*
   */
  public function getPrivacyStatus()
  {
    return $this->privacyStatus;
  }
  /**
   * This value indicates if the extended video statistics on the watch page can
   * be viewed by everyone. Note that the view count, likes, etc will still be
   * visible if this is disabled. @mutable youtube.videos.insert
   * youtube.videos.update
   *
   * @param bool $publicStatsViewable
   */
  public function setPublicStatsViewable($publicStatsViewable)
  {
    $this->publicStatsViewable = $publicStatsViewable;
  }
  /**
   * @return bool
   */
  public function getPublicStatsViewable()
  {
    return $this->publicStatsViewable;
  }
  /**
   * The date and time when the video is scheduled to publish. It can be set
   * only if the privacy status of the video is private..
   *
   * @param string $publishAt
   */
  public function setPublishAt($publishAt)
  {
    $this->publishAt = $publishAt;
  }
  /**
   * @return string
   */
  public function getPublishAt()
  {
    return $this->publishAt;
  }
  /**
   * This value explains why YouTube rejected an uploaded video. This property
   * is only present if the uploadStatus property indicates that the upload was
   * rejected.
   *
   * Accepted values: copyright, inappropriate, duplicate, termsOfUse,
   * uploaderAccountSuspended, length, claim, uploaderAccountClosed, trademark,
   * legal
   *
   * @param self::REJECTION_REASON_* $rejectionReason
   */
  public function setRejectionReason($rejectionReason)
  {
    $this->rejectionReason = $rejectionReason;
  }
  /**
   * @return self::REJECTION_REASON_*
   */
  public function getRejectionReason()
  {
    return $this->rejectionReason;
  }
  /**
   * @param bool $selfDeclaredMadeForKids
   */
  public function setSelfDeclaredMadeForKids($selfDeclaredMadeForKids)
  {
    $this->selfDeclaredMadeForKids = $selfDeclaredMadeForKids;
  }
  /**
   * @return bool
   */
  public function getSelfDeclaredMadeForKids()
  {
    return $this->selfDeclaredMadeForKids;
  }
  /**
   * The status of the uploaded video.
   *
   * Accepted values: uploaded, processed, failed, rejected, deleted
   *
   * @param self::UPLOAD_STATUS_* $uploadStatus
   */
  public function setUploadStatus($uploadStatus)
  {
    $this->uploadStatus = $uploadStatus;
  }
  /**
   * @return self::UPLOAD_STATUS_*
   */
  public function getUploadStatus()
  {
    return $this->uploadStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoStatus::class, 'Google_Service_YouTube_VideoStatus');
