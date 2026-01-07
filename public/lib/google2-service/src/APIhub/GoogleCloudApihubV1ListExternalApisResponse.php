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

class GoogleCloudApihubV1ListExternalApisResponse extends \Google\Collection
{
  protected $collection_key = 'externalApis';
  protected $externalApisType = GoogleCloudApihubV1ExternalApi::class;
  protected $externalApisDataType = 'array';
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The External API resources present in the API hub.
   *
   * @param GoogleCloudApihubV1ExternalApi[] $externalApis
   */
  public function setExternalApis($externalApis)
  {
    $this->externalApis = $externalApis;
  }
  /**
   * @return GoogleCloudApihubV1ExternalApi[]
   */
  public function getExternalApis()
  {
    return $this->externalApis;
  }
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
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
class_alias(GoogleCloudApihubV1ListExternalApisResponse::class, 'Google_Service_APIhub_GoogleCloudApihubV1ListExternalApisResponse');
