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

class GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResult extends \Google\Model
{
  protected $chunkInfoType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultChunkInfo::class;
  protected $chunkInfoDataType = '';
  protected $unstructuredDocumentInfoType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfo::class;
  protected $unstructuredDocumentInfoDataType = '';

  /**
   * Chunk information.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultChunkInfo $chunkInfo
   */
  public function setChunkInfo(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultChunkInfo $chunkInfo)
  {
    $this->chunkInfo = $chunkInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultChunkInfo
   */
  public function getChunkInfo()
  {
    return $this->chunkInfo;
  }
  /**
   * Unstructured document information.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfo $unstructuredDocumentInfo
   */
  public function setUnstructuredDocumentInfo(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfo $unstructuredDocumentInfo)
  {
    $this->unstructuredDocumentInfo = $unstructuredDocumentInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfo
   */
  public function getUnstructuredDocumentInfo()
  {
    return $this->unstructuredDocumentInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResult::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResult');
