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

namespace Google\Service\AndroidPublisher;

class UserComment extends \Google\Model
{
  /**
   * Integer Android SDK version of the user's device at the time the review was
   * written, e.g. 23 is Marshmallow. May be absent.
   *
   * @var int
   */
  public $androidOsVersion;
  /**
   * Integer version code of the app as installed at the time the review was
   * written. May be absent.
   *
   * @var int
   */
  public $appVersionCode;
  /**
   * String version name of the app as installed at the time the review was
   * written. May be absent.
   *
   * @var string
   */
  public $appVersionName;
  /**
   * Codename for the reviewer's device, e.g. klte, flounder. May be absent.
   *
   * @var string
   */
  public $device;
  protected $deviceMetadataType = DeviceMetadata::class;
  protected $deviceMetadataDataType = '';
  protected $lastModifiedType = Timestamp::class;
  protected $lastModifiedDataType = '';
  /**
   * Untranslated text of the review, where the review was translated. If the
   * review was not translated this is left blank.
   *
   * @var string
   */
  public $originalText;
  /**
   * Language code for the reviewer. This is taken from the device settings so
   * is not guaranteed to match the language the review is written in. May be
   * absent.
   *
   * @var string
   */
  public $reviewerLanguage;
  /**
   * The star rating associated with the review, from 1 to 5.
   *
   * @var int
   */
  public $starRating;
  /**
   * The content of the comment, i.e. review body. In some cases users have been
   * able to write a review with separate title and body; in those cases the
   * title and body are concatenated and separated by a tab character.
   *
   * @var string
   */
  public $text;
  /**
   * Number of users who have given this review a thumbs down.
   *
   * @var int
   */
  public $thumbsDownCount;
  /**
   * Number of users who have given this review a thumbs up.
   *
   * @var int
   */
  public $thumbsUpCount;

  /**
   * Integer Android SDK version of the user's device at the time the review was
   * written, e.g. 23 is Marshmallow. May be absent.
   *
   * @param int $androidOsVersion
   */
  public function setAndroidOsVersion($androidOsVersion)
  {
    $this->androidOsVersion = $androidOsVersion;
  }
  /**
   * @return int
   */
  public function getAndroidOsVersion()
  {
    return $this->androidOsVersion;
  }
  /**
   * Integer version code of the app as installed at the time the review was
   * written. May be absent.
   *
   * @param int $appVersionCode
   */
  public function setAppVersionCode($appVersionCode)
  {
    $this->appVersionCode = $appVersionCode;
  }
  /**
   * @return int
   */
  public function getAppVersionCode()
  {
    return $this->appVersionCode;
  }
  /**
   * String version name of the app as installed at the time the review was
   * written. May be absent.
   *
   * @param string $appVersionName
   */
  public function setAppVersionName($appVersionName)
  {
    $this->appVersionName = $appVersionName;
  }
  /**
   * @return string
   */
  public function getAppVersionName()
  {
    return $this->appVersionName;
  }
  /**
   * Codename for the reviewer's device, e.g. klte, flounder. May be absent.
   *
   * @param string $device
   */
  public function setDevice($device)
  {
    $this->device = $device;
  }
  /**
   * @return string
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * Information about the characteristics of the user's device.
   *
   * @param DeviceMetadata $deviceMetadata
   */
  public function setDeviceMetadata(DeviceMetadata $deviceMetadata)
  {
    $this->deviceMetadata = $deviceMetadata;
  }
  /**
   * @return DeviceMetadata
   */
  public function getDeviceMetadata()
  {
    return $this->deviceMetadata;
  }
  /**
   * The last time at which this comment was updated.
   *
   * @param Timestamp $lastModified
   */
  public function setLastModified(Timestamp $lastModified)
  {
    $this->lastModified = $lastModified;
  }
  /**
   * @return Timestamp
   */
  public function getLastModified()
  {
    return $this->lastModified;
  }
  /**
   * Untranslated text of the review, where the review was translated. If the
   * review was not translated this is left blank.
   *
   * @param string $originalText
   */
  public function setOriginalText($originalText)
  {
    $this->originalText = $originalText;
  }
  /**
   * @return string
   */
  public function getOriginalText()
  {
    return $this->originalText;
  }
  /**
   * Language code for the reviewer. This is taken from the device settings so
   * is not guaranteed to match the language the review is written in. May be
   * absent.
   *
   * @param string $reviewerLanguage
   */
  public function setReviewerLanguage($reviewerLanguage)
  {
    $this->reviewerLanguage = $reviewerLanguage;
  }
  /**
   * @return string
   */
  public function getReviewerLanguage()
  {
    return $this->reviewerLanguage;
  }
  /**
   * The star rating associated with the review, from 1 to 5.
   *
   * @param int $starRating
   */
  public function setStarRating($starRating)
  {
    $this->starRating = $starRating;
  }
  /**
   * @return int
   */
  public function getStarRating()
  {
    return $this->starRating;
  }
  /**
   * The content of the comment, i.e. review body. In some cases users have been
   * able to write a review with separate title and body; in those cases the
   * title and body are concatenated and separated by a tab character.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Number of users who have given this review a thumbs down.
   *
   * @param int $thumbsDownCount
   */
  public function setThumbsDownCount($thumbsDownCount)
  {
    $this->thumbsDownCount = $thumbsDownCount;
  }
  /**
   * @return int
   */
  public function getThumbsDownCount()
  {
    return $this->thumbsDownCount;
  }
  /**
   * Number of users who have given this review a thumbs up.
   *
   * @param int $thumbsUpCount
   */
  public function setThumbsUpCount($thumbsUpCount)
  {
    $this->thumbsUpCount = $thumbsUpCount;
  }
  /**
   * @return int
   */
  public function getThumbsUpCount()
  {
    return $this->thumbsUpCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserComment::class, 'Google_Service_AndroidPublisher_UserComment');
