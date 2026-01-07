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

class StudioCreativeAsset extends \Google\Model
{
  /**
   * Unknown type of asset.
   */
  public const TYPE_UNKNOWN_TYPE = 'UNKNOWN_TYPE';
  /**
   * The asset is an HTML file.
   */
  public const TYPE_HTML = 'HTML';
  /**
   * The asset is a video file.
   */
  public const TYPE_VIDEO = 'VIDEO';
  /**
   * The asset is an image file.
   */
  public const TYPE_IMAGE = 'IMAGE';
  /**
   * The asset is a font file.
   */
  public const TYPE_FONT = 'FONT';
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  /**
   * The filename of the studio creative asset. It is default to the original
   * filename of the asset.
   *
   * @var string
   */
  public $filename;
  /**
   * The filesize of the studio creative asset. This is a read-only field.
   *
   * @var string
   */
  public $filesize;
  /**
   * Output only. Unique ID of this studio creative asset. This is a read-only,
   * auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  /**
   * Studio account ID of this studio creative asset. This field, if left unset,
   * will be auto-populated..
   *
   * @var string
   */
  public $studioAccountId;
  /**
   * Studio advertiser ID of this studio creative asset. This is a required
   * field on insertion.
   *
   * @var string
   */
  public $studioAdvertiserId;
  /**
   * Studio creative ID of this studio creative asset. The asset will be
   * associated to the creative if creative id is set.
   *
   * @var string
   */
  public $studioCreativeId;
  /**
   * The type of the studio creative asset. It is a auto-generated, read-only
   * field.
   *
   * @var string
   */
  public $type;
  protected $videoProcessingDataType = VideoProcessingData::class;
  protected $videoProcessingDataDataType = '';

  /**
   * Output only. The creation timestamp of the studio creative asset. This is a
   * read-only field.
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
   * The filename of the studio creative asset. It is default to the original
   * filename of the asset.
   *
   * @param string $filename
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }
  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }
  /**
   * The filesize of the studio creative asset. This is a read-only field.
   *
   * @param string $filesize
   */
  public function setFilesize($filesize)
  {
    $this->filesize = $filesize;
  }
  /**
   * @return string
   */
  public function getFilesize()
  {
    return $this->filesize;
  }
  /**
   * Output only. Unique ID of this studio creative asset. This is a read-only,
   * auto-generated field.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The last modified timestamp of the studio creative asset. This
   * is a read-only field.
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
   * Studio account ID of this studio creative asset. This field, if left unset,
   * will be auto-populated..
   *
   * @param string $studioAccountId
   */
  public function setStudioAccountId($studioAccountId)
  {
    $this->studioAccountId = $studioAccountId;
  }
  /**
   * @return string
   */
  public function getStudioAccountId()
  {
    return $this->studioAccountId;
  }
  /**
   * Studio advertiser ID of this studio creative asset. This is a required
   * field on insertion.
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
  /**
   * Studio creative ID of this studio creative asset. The asset will be
   * associated to the creative if creative id is set.
   *
   * @param string $studioCreativeId
   */
  public function setStudioCreativeId($studioCreativeId)
  {
    $this->studioCreativeId = $studioCreativeId;
  }
  /**
   * @return string
   */
  public function getStudioCreativeId()
  {
    return $this->studioCreativeId;
  }
  /**
   * The type of the studio creative asset. It is a auto-generated, read-only
   * field.
   *
   * Accepted values: UNKNOWN_TYPE, HTML, VIDEO, IMAGE, FONT
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
   * The processing data of the studio creative asset. This is a read-only
   * field.
   *
   * @param VideoProcessingData $videoProcessingData
   */
  public function setVideoProcessingData(VideoProcessingData $videoProcessingData)
  {
    $this->videoProcessingData = $videoProcessingData;
  }
  /**
   * @return VideoProcessingData
   */
  public function getVideoProcessingData()
  {
    return $this->videoProcessingData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StudioCreativeAsset::class, 'Google_Service_Dfareporting_StudioCreativeAsset');
