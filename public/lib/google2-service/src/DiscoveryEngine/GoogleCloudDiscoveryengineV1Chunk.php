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

class GoogleCloudDiscoveryengineV1Chunk extends \Google\Collection
{
  protected $collection_key = 'dataUrls';
  /**
   * Output only. Annotation contents if the current chunk contains annotations.
   *
   * @var string[]
   */
  public $annotationContents;
  protected $annotationMetadataType = GoogleCloudDiscoveryengineV1ChunkAnnotationMetadata::class;
  protected $annotationMetadataDataType = 'array';
  protected $chunkMetadataType = GoogleCloudDiscoveryengineV1ChunkChunkMetadata::class;
  protected $chunkMetadataDataType = '';
  /**
   * Content is a string from a document (parsed content).
   *
   * @var string
   */
  public $content;
  /**
   * Output only. Image Data URLs if the current chunk contains images. Data
   * URLs are composed of four parts: a prefix (data:), a MIME type indicating
   * the type of data, an optional base64 token if non-textual, and the data
   * itself: data:,
   *
   * @var string[]
   */
  public $dataUrls;
  /**
   * Output only. This field is OUTPUT_ONLY. It contains derived data that are
   * not in the original input document.
   *
   * @var array[]
   */
  public $derivedStructData;
  protected $documentMetadataType = GoogleCloudDiscoveryengineV1ChunkDocumentMetadata::class;
  protected $documentMetadataDataType = '';
  /**
   * Unique chunk ID of the current chunk.
   *
   * @var string
   */
  public $id;
  /**
   * The full resource name of the chunk. Format: `projects/{project}/locations/
   * {location}/collections/{collection}/dataStores/{data_store}/branches/{branc
   * h}/documents/{document_id}/chunks/{chunk_id}`. This field must be a UTF-8
   * encoded string with a length limit of 1024 characters.
   *
   * @var string
   */
  public $name;
  protected $pageSpanType = GoogleCloudDiscoveryengineV1ChunkPageSpan::class;
  protected $pageSpanDataType = '';
  /**
   * Output only. Represents the relevance score based on similarity. Higher
   * score indicates higher chunk relevance. The score is in range [-1.0, 1.0].
   * Only populated on SearchResponse.
   *
   * @var 
   */
  public $relevanceScore;

  /**
   * Output only. Annotation contents if the current chunk contains annotations.
   *
   * @param string[] $annotationContents
   */
  public function setAnnotationContents($annotationContents)
  {
    $this->annotationContents = $annotationContents;
  }
  /**
   * @return string[]
   */
  public function getAnnotationContents()
  {
    return $this->annotationContents;
  }
  /**
   * Output only. The annotation metadata includes structured content in the
   * current chunk.
   *
   * @param GoogleCloudDiscoveryengineV1ChunkAnnotationMetadata[] $annotationMetadata
   */
  public function setAnnotationMetadata($annotationMetadata)
  {
    $this->annotationMetadata = $annotationMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ChunkAnnotationMetadata[]
   */
  public function getAnnotationMetadata()
  {
    return $this->annotationMetadata;
  }
  /**
   * Output only. Metadata of the current chunk.
   *
   * @param GoogleCloudDiscoveryengineV1ChunkChunkMetadata $chunkMetadata
   */
  public function setChunkMetadata(GoogleCloudDiscoveryengineV1ChunkChunkMetadata $chunkMetadata)
  {
    $this->chunkMetadata = $chunkMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ChunkChunkMetadata
   */
  public function getChunkMetadata()
  {
    return $this->chunkMetadata;
  }
  /**
   * Content is a string from a document (parsed content).
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
   * Output only. Image Data URLs if the current chunk contains images. Data
   * URLs are composed of four parts: a prefix (data:), a MIME type indicating
   * the type of data, an optional base64 token if non-textual, and the data
   * itself: data:,
   *
   * @param string[] $dataUrls
   */
  public function setDataUrls($dataUrls)
  {
    $this->dataUrls = $dataUrls;
  }
  /**
   * @return string[]
   */
  public function getDataUrls()
  {
    return $this->dataUrls;
  }
  /**
   * Output only. This field is OUTPUT_ONLY. It contains derived data that are
   * not in the original input document.
   *
   * @param array[] $derivedStructData
   */
  public function setDerivedStructData($derivedStructData)
  {
    $this->derivedStructData = $derivedStructData;
  }
  /**
   * @return array[]
   */
  public function getDerivedStructData()
  {
    return $this->derivedStructData;
  }
  /**
   * Metadata of the document from the current chunk.
   *
   * @param GoogleCloudDiscoveryengineV1ChunkDocumentMetadata $documentMetadata
   */
  public function setDocumentMetadata(GoogleCloudDiscoveryengineV1ChunkDocumentMetadata $documentMetadata)
  {
    $this->documentMetadata = $documentMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ChunkDocumentMetadata
   */
  public function getDocumentMetadata()
  {
    return $this->documentMetadata;
  }
  /**
   * Unique chunk ID of the current chunk.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The full resource name of the chunk. Format: `projects/{project}/locations/
   * {location}/collections/{collection}/dataStores/{data_store}/branches/{branc
   * h}/documents/{document_id}/chunks/{chunk_id}`. This field must be a UTF-8
   * encoded string with a length limit of 1024 characters.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Page span of the chunk.
   *
   * @param GoogleCloudDiscoveryengineV1ChunkPageSpan $pageSpan
   */
  public function setPageSpan(GoogleCloudDiscoveryengineV1ChunkPageSpan $pageSpan)
  {
    $this->pageSpan = $pageSpan;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ChunkPageSpan
   */
  public function getPageSpan()
  {
    return $this->pageSpan;
  }
  public function setRelevanceScore($relevanceScore)
  {
    $this->relevanceScore = $relevanceScore;
  }
  public function getRelevanceScore()
  {
    return $this->relevanceScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1Chunk::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Chunk');
