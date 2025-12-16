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

class VideoFormat extends \Google\Model
{
  public const FILE_TYPE_FLV = 'FLV';
  public const FILE_TYPE_THREEGPP = 'THREEGPP';
  public const FILE_TYPE_MP4 = 'MP4';
  public const FILE_TYPE_WEBM = 'WEBM';
  public const FILE_TYPE_M3U8 = 'M3U8';
  /**
   * File type of the video format.
   *
   * @var string
   */
  public $fileType;
  /**
   * ID of the video format.
   *
   * @var int
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#videoFormat".
   *
   * @var string
   */
  public $kind;
  protected $resolutionType = Size::class;
  protected $resolutionDataType = '';
  /**
   * The target bit rate of this video format.
   *
   * @var int
   */
  public $targetBitRate;

  /**
   * File type of the video format.
   *
   * Accepted values: FLV, THREEGPP, MP4, WEBM, M3U8
   *
   * @param self::FILE_TYPE_* $fileType
   */
  public function setFileType($fileType)
  {
    $this->fileType = $fileType;
  }
  /**
   * @return self::FILE_TYPE_*
   */
  public function getFileType()
  {
    return $this->fileType;
  }
  /**
   * ID of the video format.
   *
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#videoFormat".
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
   * The resolution of this video format.
   *
   * @param Size $resolution
   */
  public function setResolution(Size $resolution)
  {
    $this->resolution = $resolution;
  }
  /**
   * @return Size
   */
  public function getResolution()
  {
    return $this->resolution;
  }
  /**
   * The target bit rate of this video format.
   *
   * @param int $targetBitRate
   */
  public function setTargetBitRate($targetBitRate)
  {
    $this->targetBitRate = $targetBitRate;
  }
  /**
   * @return int
   */
  public function getTargetBitRate()
  {
    return $this->targetBitRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoFormat::class, 'Google_Service_Dfareporting_VideoFormat');
