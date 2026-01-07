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

class GoogleCloudDiscoveryengineV1alphaGetUriPatternDocumentDataResponse extends \Google\Model
{
  /**
   * Document data keyed by URI pattern. For example: document_data_map = {
   * "www.url1.com": { "Categories": ["category1", "category2"] },
   * "www.url2.com": { "Categories": ["category3"] } }
   *
   * @var array[]
   */
  public $documentDataMap;

  /**
   * Document data keyed by URI pattern. For example: document_data_map = {
   * "www.url1.com": { "Categories": ["category1", "category2"] },
   * "www.url2.com": { "Categories": ["category3"] } }
   *
   * @param array[] $documentDataMap
   */
  public function setDocumentDataMap($documentDataMap)
  {
    $this->documentDataMap = $documentDataMap;
  }
  /**
   * @return array[]
   */
  public function getDocumentDataMap()
  {
    return $this->documentDataMap;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaGetUriPatternDocumentDataResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaGetUriPatternDocumentDataResponse');
