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

namespace Google\Service\Books;

class VolumeVolumeInfoImageLinks extends \Google\Model
{
  /**
   * Image link for extra large size (width of ~1280 pixels). (In LITE
   * projection)
   *
   * @var string
   */
  public $extraLarge;
  /**
   * Image link for large size (width of ~800 pixels). (In LITE projection)
   *
   * @var string
   */
  public $large;
  /**
   * Image link for medium size (width of ~575 pixels). (In LITE projection)
   *
   * @var string
   */
  public $medium;
  /**
   * Image link for small size (width of ~300 pixels). (In LITE projection)
   *
   * @var string
   */
  public $small;
  /**
   * Image link for small thumbnail size (width of ~80 pixels). (In LITE
   * projection)
   *
   * @var string
   */
  public $smallThumbnail;
  /**
   * Image link for thumbnail size (width of ~128 pixels). (In LITE projection)
   *
   * @var string
   */
  public $thumbnail;

  /**
   * Image link for extra large size (width of ~1280 pixels). (In LITE
   * projection)
   *
   * @param string $extraLarge
   */
  public function setExtraLarge($extraLarge)
  {
    $this->extraLarge = $extraLarge;
  }
  /**
   * @return string
   */
  public function getExtraLarge()
  {
    return $this->extraLarge;
  }
  /**
   * Image link for large size (width of ~800 pixels). (In LITE projection)
   *
   * @param string $large
   */
  public function setLarge($large)
  {
    $this->large = $large;
  }
  /**
   * @return string
   */
  public function getLarge()
  {
    return $this->large;
  }
  /**
   * Image link for medium size (width of ~575 pixels). (In LITE projection)
   *
   * @param string $medium
   */
  public function setMedium($medium)
  {
    $this->medium = $medium;
  }
  /**
   * @return string
   */
  public function getMedium()
  {
    return $this->medium;
  }
  /**
   * Image link for small size (width of ~300 pixels). (In LITE projection)
   *
   * @param string $small
   */
  public function setSmall($small)
  {
    $this->small = $small;
  }
  /**
   * @return string
   */
  public function getSmall()
  {
    return $this->small;
  }
  /**
   * Image link for small thumbnail size (width of ~80 pixels). (In LITE
   * projection)
   *
   * @param string $smallThumbnail
   */
  public function setSmallThumbnail($smallThumbnail)
  {
    $this->smallThumbnail = $smallThumbnail;
  }
  /**
   * @return string
   */
  public function getSmallThumbnail()
  {
    return $this->smallThumbnail;
  }
  /**
   * Image link for thumbnail size (width of ~128 pixels). (In LITE projection)
   *
   * @param string $thumbnail
   */
  public function setThumbnail($thumbnail)
  {
    $this->thumbnail = $thumbnail;
  }
  /**
   * @return string
   */
  public function getThumbnail()
  {
    return $this->thumbnail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeVolumeInfoImageLinks::class, 'Google_Service_Books_VolumeVolumeInfoImageLinks');
