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

namespace Google\Service\ManufacturerCenter;

class Image extends \Google\Model
{
  /**
   * The image status is unspecified. Should not be used.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The image was uploaded and is being processed.
   */
  public const STATUS_PENDING_PROCESSING = 'PENDING_PROCESSING';
  /**
   * The image crawl is still pending.
   */
  public const STATUS_PENDING_CRAWL = 'PENDING_CRAWL';
  /**
   * The image was processed and it meets the requirements.
   */
  public const STATUS_OK = 'OK';
  /**
   * The image URL is protected by robots.txt file and cannot be crawled.
   */
  public const STATUS_ROBOTED = 'ROBOTED';
  /**
   * The image URL is protected by X-Robots-Tag and cannot be crawled.
   */
  public const STATUS_XROBOTED = 'XROBOTED';
  /**
   * There was an error while crawling the image.
   */
  public const STATUS_CRAWL_ERROR = 'CRAWL_ERROR';
  /**
   * The image cannot be processed.
   */
  public const STATUS_PROCESSING_ERROR = 'PROCESSING_ERROR';
  /**
   * The image cannot be decoded.
   */
  public const STATUS_DECODING_ERROR = 'DECODING_ERROR';
  /**
   * The image is too big.
   */
  public const STATUS_TOO_BIG = 'TOO_BIG';
  /**
   * The image was manually overridden and will not be crawled.
   */
  public const STATUS_CRAWL_SKIPPED = 'CRAWL_SKIPPED';
  /**
   * The image crawl was postponed to avoid overloading the host.
   */
  public const STATUS_HOSTLOADED = 'HOSTLOADED';
  /**
   * The image URL returned a "404 Not Found" error.
   */
  public const STATUS_HTTP_404 = 'HTTP_404';
  /**
   * Type is unspecified. Should not be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The image was crawled from a provided URL.
   */
  public const TYPE_CRAWLED = 'CRAWLED';
  /**
   * The image was uploaded.
   */
  public const TYPE_UPLOADED = 'UPLOADED';
  /**
   * The URL of the image. For crawled images, this is the provided URL. For
   * uploaded images, this is a serving URL from Google if the image has been
   * processed successfully.
   *
   * @var string
   */
  public $imageUrl;
  /**
   * The status of the image. @OutputOnly
   *
   * @var string
   */
  public $status;
  /**
   * The type of the image, i.e., crawled or uploaded. @OutputOnly
   *
   * @var string
   */
  public $type;

  /**
   * The URL of the image. For crawled images, this is the provided URL. For
   * uploaded images, this is a serving URL from Google if the image has been
   * processed successfully.
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * The status of the image. @OutputOnly
   *
   * Accepted values: STATUS_UNSPECIFIED, PENDING_PROCESSING, PENDING_CRAWL, OK,
   * ROBOTED, XROBOTED, CRAWL_ERROR, PROCESSING_ERROR, DECODING_ERROR, TOO_BIG,
   * CRAWL_SKIPPED, HOSTLOADED, HTTP_404
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
   * The type of the image, i.e., crawled or uploaded. @OutputOnly
   *
   * Accepted values: TYPE_UNSPECIFIED, CRAWLED, UPLOADED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Image::class, 'Google_Service_ManufacturerCenter_Image');
