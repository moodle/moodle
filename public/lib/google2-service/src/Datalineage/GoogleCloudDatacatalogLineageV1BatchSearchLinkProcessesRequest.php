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

class GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesRequest extends \Google\Collection
{
  protected $collection_key = 'links';
  /**
   * Required. An array of links to check for their associated LineageProcesses.
   * The maximum number of items in this array is 100. If the request contains
   * more than 100 links, it returns the `INVALID_ARGUMENT` error. Format:
   * `projects/{project}/locations/{location}/links/{link}`.
   *
   * @var string[]
   */
  public $links;
  /**
   * The maximum number of processes to return in a single page of the response.
   * A page may contain fewer results than this value.
   *
   * @var int
   */
  public $pageSize;
  /**
   * The page token received from a previous `BatchSearchLinkProcesses` call.
   * Use it to get the next page. When requesting subsequent pages of a
   * response, remember that all parameters must match the values you provided
   * in the original request.
   *
   * @var string
   */
  public $pageToken;

  /**
   * Required. An array of links to check for their associated LineageProcesses.
   * The maximum number of items in this array is 100. If the request contains
   * more than 100 links, it returns the `INVALID_ARGUMENT` error. Format:
   * `projects/{project}/locations/{location}/links/{link}`.
   *
   * @param string[] $links
   */
  public function setLinks($links)
  {
    $this->links = $links;
  }
  /**
   * @return string[]
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * The maximum number of processes to return in a single page of the response.
   * A page may contain fewer results than this value.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * The page token received from a previous `BatchSearchLinkProcesses` call.
   * Use it to get the next page. When requesting subsequent pages of a
   * response, remember that all parameters must match the values you provided
   * in the original request.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesRequest::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesRequest');
