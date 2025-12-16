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

class GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpecEndUserMetaDataChunkInfo extends \Google\Model
{
  /**
   * Chunk textual content. It is limited to 8000 characters.
   *
   * @var string
   */
  public $content;
  protected $documentMetadataType = GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpecEndUserMetaDataChunkInfoDocumentMetadata::class;
  protected $documentMetadataDataType = '';

  /**
   * Chunk textual content. It is limited to 8000 characters.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Metadata of the document from the current chunk.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpecEndUserMetaDataChunkInfoDocumentMetadata $documentMetadata
   */
  public function setDocumentMetadata(GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpecEndUserMetaDataChunkInfoDocumentMetadata $documentMetadata)
  {
    $this->documentMetadata = $documentMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpecEndUserMetaDataChunkInfoDocumentMetadata
   */
  public function getDocumentMetadata()
  {
    return $this->documentMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpecEndUserMetaDataChunkInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpecEndUserMetaDataChunkInfo');
