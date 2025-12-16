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

namespace Google\Service\Datalineage;

class GoogleCloudDatacatalogLineageV1ProcessLinkInfo extends \Google\Model
{
  /**
   * The end of the last event establishing this link-process tuple.
   *
   * @var string
   */
  public $endTime;
  /**
   * The name of the link in the format of
   * `projects/{project}/locations/{location}/links/{link}`.
   *
   * @var string
   */
  public $link;
  /**
   * The start of the first event establishing this link-process tuple.
   *
   * @var string
   */
  public $startTime;

  /**
   * The end of the last event establishing this link-process tuple.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The name of the link in the format of
   * `projects/{project}/locations/{location}/links/{link}`.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * The start of the first event establishing this link-process tuple.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1ProcessLinkInfo::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1ProcessLinkInfo');
