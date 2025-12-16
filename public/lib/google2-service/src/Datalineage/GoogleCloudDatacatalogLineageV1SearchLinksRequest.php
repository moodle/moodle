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

class GoogleCloudDatacatalogLineageV1SearchLinksRequest extends \Google\Model
{
  /**
   * Optional. The maximum number of links to return in a single page of the
   * response. A page may contain fewer links than this value. If unspecified,
   * at most 10 links are returned. Maximum value is 100; values greater than
   * 100 are reduced to 100.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. The page token received from a previous `SearchLinksRequest`
   * call. Use it to get the next page. When requesting subsequent pages of a
   * response, remember that all parameters must match the values you provided
   * in the original request.
   *
   * @var string
   */
  public $pageToken;
  protected $sourceType = GoogleCloudDatacatalogLineageV1EntityReference::class;
  protected $sourceDataType = '';
  protected $targetType = GoogleCloudDatacatalogLineageV1EntityReference::class;
  protected $targetDataType = '';

  /**
   * Optional. The maximum number of links to return in a single page of the
   * response. A page may contain fewer links than this value. If unspecified,
   * at most 10 links are returned. Maximum value is 100; values greater than
   * 100 are reduced to 100.
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
   * Optional. The page token received from a previous `SearchLinksRequest`
   * call. Use it to get the next page. When requesting subsequent pages of a
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
  /**
   * Optional. Send asset information in the **source** field to retrieve all
   * links that lead from the specified asset to downstream assets.
   *
   * @param GoogleCloudDatacatalogLineageV1EntityReference $source
   */
  public function setSource(GoogleCloudDatacatalogLineageV1EntityReference $source)
  {
    $this->source = $source;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1EntityReference
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Optional. Send asset information in the **target** field to retrieve all
   * links that lead from upstream assets to the specified asset.
   *
   * @param GoogleCloudDatacatalogLineageV1EntityReference $target
   */
  public function setTarget(GoogleCloudDatacatalogLineageV1EntityReference $target)
  {
    $this->target = $target;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1EntityReference
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1SearchLinksRequest::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1SearchLinksRequest');
