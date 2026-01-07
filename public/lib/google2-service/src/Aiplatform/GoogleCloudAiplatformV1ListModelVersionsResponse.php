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

class GoogleCloudAiplatformV1ListModelVersionsResponse extends \Google\Collection
{
  protected $collection_key = 'models';
  protected $modelsType = GoogleCloudAiplatformV1Model::class;
  protected $modelsDataType = 'array';
  /**
   * A token to retrieve the next page of results. Pass to
   * ListModelVersionsRequest.page_token to obtain that page.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * List of Model versions in the requested page. In the returned Model name
   * field, version ID instead of regvision tag will be included.
   *
   * @param GoogleCloudAiplatformV1Model[] $models
   */
  public function setModels($models)
  {
    $this->models = $models;
  }
  /**
   * @return GoogleCloudAiplatformV1Model[]
   */
  public function getModels()
  {
    return $this->models;
  }
  /**
   * A token to retrieve the next page of results. Pass to
   * ListModelVersionsRequest.page_token to obtain that page.
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
class_alias(GoogleCloudAiplatformV1ListModelVersionsResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ListModelVersionsResponse');
