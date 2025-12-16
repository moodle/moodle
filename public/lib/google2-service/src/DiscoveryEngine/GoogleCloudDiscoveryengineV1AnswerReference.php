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

class GoogleCloudDiscoveryengineV1AnswerReference extends \Google\Model
{
  protected $chunkInfoType = GoogleCloudDiscoveryengineV1AnswerReferenceChunkInfo::class;
  protected $chunkInfoDataType = '';
  protected $structuredDocumentInfoType = GoogleCloudDiscoveryengineV1AnswerReferenceStructuredDocumentInfo::class;
  protected $structuredDocumentInfoDataType = '';
  protected $unstructuredDocumentInfoType = GoogleCloudDiscoveryengineV1AnswerReferenceUnstructuredDocumentInfo::class;
  protected $unstructuredDocumentInfoDataType = '';

  /**
   * Chunk information.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerReferenceChunkInfo $chunkInfo
   */
  public function setChunkInfo(GoogleCloudDiscoveryengineV1AnswerReferenceChunkInfo $chunkInfo)
  {
    $this->chunkInfo = $chunkInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerReferenceChunkInfo
   */
  public function getChunkInfo()
  {
    return $this->chunkInfo;
  }
  /**
   * Structured document information.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerReferenceStructuredDocumentInfo $structuredDocumentInfo
   */
  public function setStructuredDocumentInfo(GoogleCloudDiscoveryengineV1AnswerReferenceStructuredDocumentInfo $structuredDocumentInfo)
  {
    $this->structuredDocumentInfo = $structuredDocumentInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerReferenceStructuredDocumentInfo
   */
  public function getStructuredDocumentInfo()
  {
    return $this->structuredDocumentInfo;
  }
  /**
   * Unstructured document information.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerReferenceUnstructuredDocumentInfo $unstructuredDocumentInfo
   */
  public function setUnstructuredDocumentInfo(GoogleCloudDiscoveryengineV1AnswerReferenceUnstructuredDocumentInfo $unstructuredDocumentInfo)
  {
    $this->unstructuredDocumentInfo = $unstructuredDocumentInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerReferenceUnstructuredDocumentInfo
   */
  public function getUnstructuredDocumentInfo()
  {
    return $this->unstructuredDocumentInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerReference::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerReference');
