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

class GoogleCloudAiplatformV1GroundingChunkRetrievedContext extends \Google\Model
{
  /**
   * Output only. The full resource name of the referenced Vertex AI Search
   * document. This is used to identify the specific document that was
   * retrieved. The format is `projects/{project}/locations/{location}/collectio
   * ns/{collection}/dataStores/{data_store}/branches/{branch}/documents/{docume
   * nt}`.
   *
   * @var string
   */
  public $documentName;
  protected $ragChunkType = GoogleCloudAiplatformV1RagChunk::class;
  protected $ragChunkDataType = '';
  /**
   * The content of the retrieved data source.
   *
   * @var string
   */
  public $text;
  /**
   * The title of the retrieved data source.
   *
   * @var string
   */
  public $title;
  /**
   * The URI of the retrieved data source.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. The full resource name of the referenced Vertex AI Search
   * document. This is used to identify the specific document that was
   * retrieved. The format is `projects/{project}/locations/{location}/collectio
   * ns/{collection}/dataStores/{data_store}/branches/{branch}/documents/{docume
   * nt}`.
   *
   * @param string $documentName
   */
  public function setDocumentName($documentName)
  {
    $this->documentName = $documentName;
  }
  /**
   * @return string
   */
  public function getDocumentName()
  {
    return $this->documentName;
  }
  /**
   * Additional context for a Retrieval-Augmented Generation (RAG) retrieval
   * result. This is populated only when the RAG retrieval tool is used.
   *
   * @param GoogleCloudAiplatformV1RagChunk $ragChunk
   */
  public function setRagChunk(GoogleCloudAiplatformV1RagChunk $ragChunk)
  {
    $this->ragChunk = $ragChunk;
  }
  /**
   * @return GoogleCloudAiplatformV1RagChunk
   */
  public function getRagChunk()
  {
    return $this->ragChunk;
  }
  /**
   * The content of the retrieved data source.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The title of the retrieved data source.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The URI of the retrieved data source.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingChunkRetrievedContext::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingChunkRetrievedContext');
