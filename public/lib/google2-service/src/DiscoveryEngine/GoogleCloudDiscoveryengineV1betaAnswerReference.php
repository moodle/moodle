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

class GoogleCloudDiscoveryengineV1betaAnswerReference extends \Google\Model
{
  protected $chunkInfoType = GoogleCloudDiscoveryengineV1betaAnswerReferenceChunkInfo::class;
  protected $chunkInfoDataType = '';
  protected $unstructuredDocumentInfoType = GoogleCloudDiscoveryengineV1betaAnswerReferenceUnstructuredDocumentInfo::class;
  protected $unstructuredDocumentInfoDataType = '';

  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerReferenceChunkInfo
   */
  public function setChunkInfo(GoogleCloudDiscoveryengineV1betaAnswerReferenceChunkInfo $chunkInfo)
  {
    $this->chunkInfo = $chunkInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerReferenceChunkInfo
   */
  public function getChunkInfo()
  {
    return $this->chunkInfo;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerReferenceUnstructuredDocumentInfo
   */
  public function setUnstructuredDocumentInfo(GoogleCloudDiscoveryengineV1betaAnswerReferenceUnstructuredDocumentInfo $unstructuredDocumentInfo)
  {
    $this->unstructuredDocumentInfo = $unstructuredDocumentInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerReferenceUnstructuredDocumentInfo
   */
  public function getUnstructuredDocumentInfo()
  {
    return $this->unstructuredDocumentInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaAnswerReference::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaAnswerReference');
