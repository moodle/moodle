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

namespace Google\Service\StreetViewPublish;

class Photo extends \Google\Collection
{
  /**
   * The status of the photo is unknown.
   */
  public const MAPS_PUBLISH_STATUS_UNSPECIFIED_MAPS_PUBLISH_STATUS = 'UNSPECIFIED_MAPS_PUBLISH_STATUS';
  /**
   * The photo is published to the public through Google Maps.
   */
  public const MAPS_PUBLISH_STATUS_PUBLISHED = 'PUBLISHED';
  /**
   * The photo has been rejected for an unknown reason.
   */
  public const MAPS_PUBLISH_STATUS_REJECTED_UNKNOWN = 'REJECTED_UNKNOWN';
  /**
   * The status of this transfer is unspecified.
   */
  public const TRANSFER_STATUS_TRANSFER_STATUS_UNKNOWN = 'TRANSFER_STATUS_UNKNOWN';
  /**
   * This photo has never been in a transfer.
   */
  public const TRANSFER_STATUS_NEVER_TRANSFERRED = 'NEVER_TRANSFERRED';
  /**
   * This photo transfer has been initiated, but the receiver has not yet
   * responded.
   */
  public const TRANSFER_STATUS_PENDING = 'PENDING';
  /**
   * The photo transfer has been completed, and this photo has been transferred
   * to the recipient.
   */
  public const TRANSFER_STATUS_COMPLETED = 'COMPLETED';
  /**
   * The recipient rejected this photo transfer.
   */
  public const TRANSFER_STATUS_REJECTED = 'REJECTED';
  /**
   * The photo transfer expired before the recipient took any action.
   */
  public const TRANSFER_STATUS_EXPIRED = 'EXPIRED';
  /**
   * The sender cancelled this photo transfer.
   */
  public const TRANSFER_STATUS_CANCELLED = 'CANCELLED';
  /**
   * The recipient owns this photo due to a rights transfer.
   */
  public const TRANSFER_STATUS_RECEIVED_VIA_TRANSFER = 'RECEIVED_VIA_TRANSFER';
  protected $collection_key = 'places';
  /**
   * Optional. Absolute time when the photo was captured. When the photo has no
   * exif timestamp, this is used to set a timestamp in the photo metadata.
   *
   * @var string
   */
  public $captureTime;
  protected $connectionsType = Connection::class;
  protected $connectionsDataType = 'array';
  /**
   * Output only. The download URL for the photo bytes. This field is set only
   * when GetPhotoRequest.view is set to PhotoView.INCLUDE_DOWNLOAD_URL.
   *
   * @var string
   */
  public $downloadUrl;
  /**
   * Output only. Status in Google Maps, whether this photo was published or
   * rejected.
   *
   * @var string
   */
  public $mapsPublishStatus;
  protected $photoIdType = PhotoId::class;
  protected $photoIdDataType = '';
  protected $placesType = Place::class;
  protected $placesDataType = 'array';
  protected $poseType = Pose::class;
  protected $poseDataType = '';
  /**
   * Output only. The share link for the photo.
   *
   * @var string
   */
  public $shareLink;
  /**
   * Output only. The thumbnail URL for showing a preview of the given photo.
   *
   * @var string
   */
  public $thumbnailUrl;
  /**
   * Output only. Status of rights transfer on this photo.
   *
   * @var string
   */
  public $transferStatus;
  protected $uploadReferenceType = UploadRef::class;
  protected $uploadReferenceDataType = '';
  /**
   * Output only. Time when the image was uploaded.
   *
   * @var string
   */
  public $uploadTime;
  /**
   * Output only. View count of the photo.
   *
   * @var string
   */
  public $viewCount;

  /**
   * Optional. Absolute time when the photo was captured. When the photo has no
   * exif timestamp, this is used to set a timestamp in the photo metadata.
   *
   * @param string $captureTime
   */
  public function setCaptureTime($captureTime)
  {
    $this->captureTime = $captureTime;
  }
  /**
   * @return string
   */
  public function getCaptureTime()
  {
    return $this->captureTime;
  }
  /**
   * Optional. Connections to other photos. A connection represents the link
   * from this photo to another photo.
   *
   * @param Connection[] $connections
   */
  public function setConnections($connections)
  {
    $this->connections = $connections;
  }
  /**
   * @return Connection[]
   */
  public function getConnections()
  {
    return $this->connections;
  }
  /**
   * Output only. The download URL for the photo bytes. This field is set only
   * when GetPhotoRequest.view is set to PhotoView.INCLUDE_DOWNLOAD_URL.
   *
   * @param string $downloadUrl
   */
  public function setDownloadUrl($downloadUrl)
  {
    $this->downloadUrl = $downloadUrl;
  }
  /**
   * @return string
   */
  public function getDownloadUrl()
  {
    return $this->downloadUrl;
  }
  /**
   * Output only. Status in Google Maps, whether this photo was published or
   * rejected.
   *
   * Accepted values: UNSPECIFIED_MAPS_PUBLISH_STATUS, PUBLISHED,
   * REJECTED_UNKNOWN
   *
   * @param self::MAPS_PUBLISH_STATUS_* $mapsPublishStatus
   */
  public function setMapsPublishStatus($mapsPublishStatus)
  {
    $this->mapsPublishStatus = $mapsPublishStatus;
  }
  /**
   * @return self::MAPS_PUBLISH_STATUS_*
   */
  public function getMapsPublishStatus()
  {
    return $this->mapsPublishStatus;
  }
  /**
   * Required. Output only. Required when updating a photo. Output only when
   * creating a photo. Identifier for the photo, which is unique among all
   * photos in Google.
   *
   * @param PhotoId $photoId
   */
  public function setPhotoId(PhotoId $photoId)
  {
    $this->photoId = $photoId;
  }
  /**
   * @return PhotoId
   */
  public function getPhotoId()
  {
    return $this->photoId;
  }
  /**
   * Optional. Places where this photo belongs.
   *
   * @param Place[] $places
   */
  public function setPlaces($places)
  {
    $this->places = $places;
  }
  /**
   * @return Place[]
   */
  public function getPlaces()
  {
    return $this->places;
  }
  /**
   * Optional. Pose of the photo.
   *
   * @param Pose $pose
   */
  public function setPose(Pose $pose)
  {
    $this->pose = $pose;
  }
  /**
   * @return Pose
   */
  public function getPose()
  {
    return $this->pose;
  }
  /**
   * Output only. The share link for the photo.
   *
   * @param string $shareLink
   */
  public function setShareLink($shareLink)
  {
    $this->shareLink = $shareLink;
  }
  /**
   * @return string
   */
  public function getShareLink()
  {
    return $this->shareLink;
  }
  /**
   * Output only. The thumbnail URL for showing a preview of the given photo.
   *
   * @param string $thumbnailUrl
   */
  public function setThumbnailUrl($thumbnailUrl)
  {
    $this->thumbnailUrl = $thumbnailUrl;
  }
  /**
   * @return string
   */
  public function getThumbnailUrl()
  {
    return $this->thumbnailUrl;
  }
  /**
   * Output only. Status of rights transfer on this photo.
   *
   * Accepted values: TRANSFER_STATUS_UNKNOWN, NEVER_TRANSFERRED, PENDING,
   * COMPLETED, REJECTED, EXPIRED, CANCELLED, RECEIVED_VIA_TRANSFER
   *
   * @param self::TRANSFER_STATUS_* $transferStatus
   */
  public function setTransferStatus($transferStatus)
  {
    $this->transferStatus = $transferStatus;
  }
  /**
   * @return self::TRANSFER_STATUS_*
   */
  public function getTransferStatus()
  {
    return $this->transferStatus;
  }
  /**
   * Input only. Required when creating a photo. Input only. The resource URL
   * where the photo bytes are uploaded to.
   *
   * @param UploadRef $uploadReference
   */
  public function setUploadReference(UploadRef $uploadReference)
  {
    $this->uploadReference = $uploadReference;
  }
  /**
   * @return UploadRef
   */
  public function getUploadReference()
  {
    return $this->uploadReference;
  }
  /**
   * Output only. Time when the image was uploaded.
   *
   * @param string $uploadTime
   */
  public function setUploadTime($uploadTime)
  {
    $this->uploadTime = $uploadTime;
  }
  /**
   * @return string
   */
  public function getUploadTime()
  {
    return $this->uploadTime;
  }
  /**
   * Output only. View count of the photo.
   *
   * @param string $viewCount
   */
  public function setViewCount($viewCount)
  {
    $this->viewCount = $viewCount;
  }
  /**
   * @return string
   */
  public function getViewCount()
  {
    return $this->viewCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Photo::class, 'Google_Service_StreetViewPublish_Photo');
