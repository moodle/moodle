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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1ListLinkedSourcesRequest extends \Google\Model
{
  /**
   * The maximum number of document-links to return. The service may return
   * fewer than this value. If unspecified, at most 50 document-links will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A page token, received from a previous `ListLinkedSources` call. Provide
   * this to retrieve the subsequent page. When paginating, all other parameters
   * provided to `ListLinkedSources` must match the call that provided the page
   * token.
   *
   * @var string
   */
  public $pageToken;
  protected $requestMetadataType = GoogleCloudContentwarehouseV1RequestMetadata::class;
  protected $requestMetadataDataType = '';

  /**
   * The maximum number of document-links to return. The service may return
   * fewer than this value. If unspecified, at most 50 document-links will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
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
   * A page token, received from a previous `ListLinkedSources` call. Provide
   * this to retrieve the subsequent page. When paginating, all other parameters
   * provided to `ListLinkedSources` must match the call that provided the page
   * token.
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
   * The meta information collected about the document creator, used to enforce
   * access control for the service.
   *
   * @param GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata
   */
  public function setRequestMetadata(GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata)
  {
    $this->requestMetadata = $requestMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RequestMetadata
   */
  public function getRequestMetadata()
  {
    return $this->requestMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1ListLinkedSourcesRequest::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1ListLinkedSourcesRequest');
