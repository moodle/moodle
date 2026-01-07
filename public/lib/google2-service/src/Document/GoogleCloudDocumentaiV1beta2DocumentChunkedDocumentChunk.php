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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunk extends \Google\Collection
{
  protected $collection_key = 'sourceBlockIds';
  /**
   * @var string
   */
  public $chunkId;
  /**
   * @var string
   */
  public $content;
  protected $pageFootersType = GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageFooter::class;
  protected $pageFootersDataType = 'array';
  protected $pageHeadersType = GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageHeader::class;
  protected $pageHeadersDataType = 'array';
  protected $pageSpanType = GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageSpan::class;
  protected $pageSpanDataType = '';
  /**
   * @var string[]
   */
  public $sourceBlockIds;

  /**
   * @param string
   */
  public function setChunkId($chunkId)
  {
    $this->chunkId = $chunkId;
  }
  /**
   * @return string
   */
  public function getChunkId()
  {
    return $this->chunkId;
  }
  /**
   * @param string
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
   * @param GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageFooter[]
   */
  public function setPageFooters($pageFooters)
  {
    $this->pageFooters = $pageFooters;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageFooter[]
   */
  public function getPageFooters()
  {
    return $this->pageFooters;
  }
  /**
   * @param GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageHeader[]
   */
  public function setPageHeaders($pageHeaders)
  {
    $this->pageHeaders = $pageHeaders;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageHeader[]
   */
  public function getPageHeaders()
  {
    return $this->pageHeaders;
  }
  /**
   * @param GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageSpan
   */
  public function setPageSpan(GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageSpan $pageSpan)
  {
    $this->pageSpan = $pageSpan;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunkChunkPageSpan
   */
  public function getPageSpan()
  {
    return $this->pageSpan;
  }
  /**
   * @param string[]
   */
  public function setSourceBlockIds($sourceBlockIds)
  {
    $this->sourceBlockIds = $sourceBlockIds;
  }
  /**
   * @return string[]
   */
  public function getSourceBlockIds()
  {
    return $this->sourceBlockIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunk::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta2DocumentChunkedDocumentChunk');
