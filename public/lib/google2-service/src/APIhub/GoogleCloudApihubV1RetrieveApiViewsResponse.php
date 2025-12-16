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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1RetrieveApiViewsResponse extends \Google\Collection
{
  protected $collection_key = 'apiViews';
  protected $apiViewsType = GoogleCloudApihubV1ApiView::class;
  protected $apiViewsDataType = 'array';
  /**
   * Next page token.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of API views.
   *
   * @param GoogleCloudApihubV1ApiView[] $apiViews
   */
  public function setApiViews($apiViews)
  {
    $this->apiViews = $apiViews;
  }
  /**
   * @return GoogleCloudApihubV1ApiView[]
   */
  public function getApiViews()
  {
    return $this->apiViews;
  }
  /**
   * Next page token.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1RetrieveApiViewsResponse::class, 'Google_Service_APIhub_GoogleCloudApihubV1RetrieveApiViewsResponse');
