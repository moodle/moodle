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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Citation extends \Google\Model
{
  /**
   * Output only. The end index of the citation in the content.
   *
   * @var int
   */
  public $endIndex;
  /**
   * Output only. The license of the source of the citation.
   *
   * @var string
   */
  public $license;
  protected $publicationDateType = GoogleTypeDate::class;
  protected $publicationDateDataType = '';
  /**
   * Output only. The start index of the citation in the content.
   *
   * @var int
   */
  public $startIndex;
  /**
   * Output only. The title of the source of the citation.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. The URI of the source of the citation.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. The end index of the citation in the content.
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
   * Output only. The license of the source of the citation.
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
   * Output only. The publication date of the source of the citation.
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
   * Output only. The start index of the citation in the content.
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
   * Output only. The title of the source of the citation.
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
   * Output only. The URI of the source of the citation.
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
class_alias(GoogleCloudAiplatformV1Citation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Citation');
