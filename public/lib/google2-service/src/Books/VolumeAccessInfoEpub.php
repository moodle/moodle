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

class VolumeAccessInfoEpub extends \Google\Model
{
  /**
   * URL to retrieve ACS token for epub download. (In LITE projection.)
   *
   * @var string
   */
  public $acsTokenLink;
  /**
   * URL to download epub. (In LITE projection.)
   *
   * @var string
   */
  public $downloadLink;
  /**
   * Is a flowing text epub available either as public domain or for purchase.
   * (In LITE projection.)
   *
   * @var bool
   */
  public $isAvailable;

  /**
   * URL to retrieve ACS token for epub download. (In LITE projection.)
   *
   * @param string $acsTokenLink
   */
  public function setAcsTokenLink($acsTokenLink)
  {
    $this->acsTokenLink = $acsTokenLink;
  }
  /**
   * @return string
   */
  public function getAcsTokenLink()
  {
    return $this->acsTokenLink;
  }
  /**
   * URL to download epub. (In LITE projection.)
   *
   * @param string $downloadLink
   */
  public function setDownloadLink($downloadLink)
  {
    $this->downloadLink = $downloadLink;
  }
  /**
   * @return string
   */
  public function getDownloadLink()
  {
    return $this->downloadLink;
  }
  /**
   * Is a flowing text epub available either as public domain or for purchase.
   * (In LITE projection.)
   *
   * @param bool $isAvailable
   */
  public function setIsAvailable($isAvailable)
  {
    $this->isAvailable = $isAvailable;
  }
  /**
   * @return bool
   */
  public function getIsAvailable()
  {
    return $this->isAvailable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeAccessInfoEpub::class, 'Google_Service_Books_VolumeAccessInfoEpub');
