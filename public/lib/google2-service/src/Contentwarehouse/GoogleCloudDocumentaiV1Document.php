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

namespace Google\Service\Contentwarehouse;

class GoogleCloudDocumentaiV1Document extends \Google\Collection
{
  protected $collection_key = 'textStyles';
  protected $chunkedDocumentType = GoogleCloudDocumentaiV1DocumentChunkedDocument::class;
  protected $chunkedDocumentDataType = '';
  /**
   * Optional. Inline document content, represented as a stream of bytes. Note:
   * As with all `bytes` fields, protobuffers use a pure binary representation,
   * whereas JSON representations use base64.
   *
   * @var string
   */
  public $content;
  protected $documentLayoutType = GoogleCloudDocumentaiV1DocumentDocumentLayout::class;
  protected $documentLayoutDataType = '';
  protected $entitiesType = GoogleCloudDocumentaiV1DocumentEntity::class;
  protected $entitiesDataType = 'array';
  protected $entityRelationsType = GoogleCloudDocumentaiV1DocumentEntityRelation::class;
  protected $entityRelationsDataType = 'array';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * An IANA published [media type (MIME
   * type)](https://www.iana.org/assignments/media-types/media-types.xhtml).
   *
   * @var string
   */
  public $mimeType;
  protected $pagesType = GoogleCloudDocumentaiV1DocumentPage::class;
  protected $pagesDataType = 'array';
  protected $revisionsType = GoogleCloudDocumentaiV1DocumentRevision::class;
  protected $revisionsDataType = 'array';
  protected $shardInfoType = GoogleCloudDocumentaiV1DocumentShardInfo::class;
  protected $shardInfoDataType = '';
  /**
   * Optional. UTF-8 encoded text in reading order from the document.
   *
   * @var string
   */
  public $text;
  protected $textChangesType = GoogleCloudDocumentaiV1DocumentTextChange::class;
  protected $textChangesDataType = 'array';
  protected $textStylesType = GoogleCloudDocumentaiV1DocumentStyle::class;
  protected $textStylesDataType = 'array';
  /**
   * Optional. Currently supports Google Cloud Storage URI of the form
   * `gs://bucket_name/object_name`. Object versioning is not supported. For
   * more information, refer to [Google Cloud Storage Request
   * URIs](https://cloud.google.com/storage/docs/reference-uris).
   *
   * @var string
   */
  public $uri;

  /**
   * Document chunked based on chunking config.
   *
   * @param GoogleCloudDocumentaiV1DocumentChunkedDocument $chunkedDocument
   */
  public function setChunkedDocument(GoogleCloudDocumentaiV1DocumentChunkedDocument $chunkedDocument)
  {
    $this->chunkedDocument = $chunkedDocument;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentChunkedDocument
   */
  public function getChunkedDocument()
  {
    return $this->chunkedDocument;
  }
  /**
   * Optional. Inline document content, represented as a stream of bytes. Note:
   * As with all `bytes` fields, protobuffers use a pure binary representation,
   * whereas JSON representations use base64.
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
   * Parsed layout of the document.
   *
   * @param GoogleCloudDocumentaiV1DocumentDocumentLayout $documentLayout
   */
  public function setDocumentLayout(GoogleCloudDocumentaiV1DocumentDocumentLayout $documentLayout)
  {
    $this->documentLayout = $documentLayout;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentDocumentLayout
   */
  public function getDocumentLayout()
  {
    return $this->documentLayout;
  }
  /**
   * A list of entities detected on Document.text. For document shards, entities
   * in this list may cross shard boundaries.
   *
   * @param GoogleCloudDocumentaiV1DocumentEntity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentEntity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * Placeholder. Relationship among Document.entities.
   *
   * @param GoogleCloudDocumentaiV1DocumentEntityRelation[] $entityRelations
   */
  public function setEntityRelations($entityRelations)
  {
    $this->entityRelations = $entityRelations;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentEntityRelation[]
   */
  public function getEntityRelations()
  {
    return $this->entityRelations;
  }
  /**
   * Any error that occurred while processing this document.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * An IANA published [media type (MIME
   * type)](https://www.iana.org/assignments/media-types/media-types.xhtml).
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Visual page layout for the Document.
   *
   * @param GoogleCloudDocumentaiV1DocumentPage[] $pages
   */
  public function setPages($pages)
  {
    $this->pages = $pages;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPage[]
   */
  public function getPages()
  {
    return $this->pages;
  }
  /**
   * Placeholder. Revision history of this document.
   *
   * @param GoogleCloudDocumentaiV1DocumentRevision[] $revisions
   */
  public function setRevisions($revisions)
  {
    $this->revisions = $revisions;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentRevision[]
   */
  public function getRevisions()
  {
    return $this->revisions;
  }
  /**
   * Information about the sharding if this document is sharded part of a larger
   * document. If the document is not sharded, this message is not specified.
   *
   * @param GoogleCloudDocumentaiV1DocumentShardInfo $shardInfo
   */
  public function setShardInfo(GoogleCloudDocumentaiV1DocumentShardInfo $shardInfo)
  {
    $this->shardInfo = $shardInfo;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentShardInfo
   */
  public function getShardInfo()
  {
    return $this->shardInfo;
  }
  /**
   * Optional. UTF-8 encoded text in reading order from the document.
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
   * Placeholder. A list of text corrections made to Document.text. This is
   * usually used for annotating corrections to OCR mistakes. Text changes for a
   * given revision may not overlap with each other.
   *
   * @param GoogleCloudDocumentaiV1DocumentTextChange[] $textChanges
   */
  public function setTextChanges($textChanges)
  {
    $this->textChanges = $textChanges;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentTextChange[]
   */
  public function getTextChanges()
  {
    return $this->textChanges;
  }
  /**
   * Styles for the Document.text.
   *
   * @deprecated
   * @param GoogleCloudDocumentaiV1DocumentStyle[] $textStyles
   */
  public function setTextStyles($textStyles)
  {
    $this->textStyles = $textStyles;
  }
  /**
   * @deprecated
   * @return GoogleCloudDocumentaiV1DocumentStyle[]
   */
  public function getTextStyles()
  {
    return $this->textStyles;
  }
  /**
   * Optional. Currently supports Google Cloud Storage URI of the form
   * `gs://bucket_name/object_name`. Object versioning is not supported. For
   * more information, refer to [Google Cloud Storage Request
   * URIs](https://cloud.google.com/storage/docs/reference-uris).
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
class_alias(GoogleCloudDocumentaiV1Document::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1Document');
