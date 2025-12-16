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

namespace Google\Service\Kmsinventory;

class GoogleCloudKmsInventoryV1SearchProtectedResourcesResponse extends \Google\Collection
{
  protected $collection_key = 'protectedResources';
  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $protectedResourcesType = GoogleCloudKmsInventoryV1ProtectedResource::class;
  protected $protectedResourcesDataType = 'array';

  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted, there are no subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Protected resources for this page.
   *
   * @param GoogleCloudKmsInventoryV1ProtectedResource[] $protectedResources
   */
  public function setProtectedResources($protectedResources)
  {
    $this->protectedResources = $protectedResources;
  }
  /**
   * @return GoogleCloudKmsInventoryV1ProtectedResource[]
   */
  public function getProtectedResources()
  {
    return $this->protectedResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudKmsInventoryV1SearchProtectedResourcesResponse::class, 'Google_Service_Kmsinventory_GoogleCloudKmsInventoryV1SearchProtectedResourcesResponse');
