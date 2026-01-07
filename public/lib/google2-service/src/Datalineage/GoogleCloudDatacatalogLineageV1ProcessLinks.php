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

class GoogleCloudDatacatalogLineageV1ProcessLinks extends \Google\Collection
{
  protected $collection_key = 'links';
  protected $linksType = GoogleCloudDatacatalogLineageV1ProcessLinkInfo::class;
  protected $linksDataType = 'array';
  /**
   * The process name in the format of
   * `projects/{project}/locations/{location}/processes/{process}`.
   *
   * @var string
   */
  public $process;

  /**
   * An array containing link details objects of the links provided in the
   * original request. A single process can result in creating multiple links.
   * If any of the links you provide in the request are created by the same
   * process, they all are included in this array.
   *
   * @param GoogleCloudDatacatalogLineageV1ProcessLinkInfo[] $links
   */
  public function setLinks($links)
  {
    $this->links = $links;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1ProcessLinkInfo[]
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * The process name in the format of
   * `projects/{project}/locations/{location}/processes/{process}`.
   *
   * @param string $process
   */
  public function setProcess($process)
  {
    $this->process = $process;
  }
  /**
   * @return string
   */
  public function getProcess()
  {
    return $this->process;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1ProcessLinks::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1ProcessLinks');
