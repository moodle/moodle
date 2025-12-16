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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaCitation extends \Google\Model
{
  /**
   * Output only. End index into the content.
   *
   * @var int
   */
  public $endIndex;
  /**
   * Output only. License of the attribution.
   *
   * @var string
   */
  public $license;
  protected $publicationDateType = GoogleTypeDate::class;
  protected $publicationDateDataType = '';
  /**
   * Output only. Start index into the content.
   *
   * @var int
   */
  public $startIndex;
  /**
   * Output only. Title of the attribution.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. Url reference of the attribution.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. End index into the content.
   *
   * @param int $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return int
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * Output only. License of the attribution.
   *
   * @param string $license
   */
  public function setLicense($license)
  {
    $this->license = $license;
  }
  /**
   * @return string
   */
  public function getLicense()
  {
    return $this->license;
  }
  /**
   * Output only. Publication date of the attribution.
   *
   * @param GoogleTypeDate $publicationDate
   */
  public function setPublicationDate(GoogleTypeDate $publicationDate)
  {
    $this->publicationDate = $publicationDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getPublicationDate()
  {
    return $this->publicationDate;
  }
  /**
   * Output only. Start index into the content.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * Output only. Title of the attribution.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Output only. Url reference of the attribution.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaCitation::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaCitation');
