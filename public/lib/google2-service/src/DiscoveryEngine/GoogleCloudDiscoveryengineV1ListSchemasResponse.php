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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1ListSchemasResponse extends \Google\Collection
{
  protected $collection_key = 'schemas';
  /**
   * A token that can be sent as ListSchemasRequest.page_token to retrieve the
   * next page. If this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $schemasType = GoogleCloudDiscoveryengineV1Schema::class;
  protected $schemasDataType = 'array';

  /**
   * A token that can be sent as ListSchemasRequest.page_token to retrieve the
   * next page. If this field is omitted, there are no subsequent pages.
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
   * The Schemas.
   *
   * @param GoogleCloudDiscoveryengineV1Schema[] $schemas
   */
  public function setSchemas($schemas)
  {
    $this->schemas = $schemas;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Schema[]
   */
  public function getSchemas()
  {
    return $this->schemas;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ListSchemasResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ListSchemasResponse');
