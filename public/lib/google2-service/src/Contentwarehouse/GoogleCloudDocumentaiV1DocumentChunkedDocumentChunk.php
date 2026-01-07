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

class GoogleCloudDocumentaiV1DocumentChunkedDocumentChunk extends \Google\Collection
{
  protected $collection_key = 'sourceBlockIds';
  /**
   * ID of the chunk.
   *
   * @var string
   */
  public $chunkId;
  /**
   * Text content of the chunk.
   *
   * @var string
   */
  public $content;
  protected $pageFootersType = GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageFooter::class;
  protected $pageFootersDataType = 'array';
  protected $pageHeadersType = GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageHeader::class;
  protected $pageHeadersDataType = 'array';
  protected $pageSpanType = GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageSpan::class;
  protected $pageSpanDataType = '';
  /**
   * Unused.
   *
   * @var string[]
   */
  public $sourceBlockIds;

  /**
   * ID of the chunk.
   *
   * @param string $chunkId
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
   * Text content of the chunk.
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
   * Page footers associated with the chunk.
   *
   * @param GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageFooter[] $pageFooters
   */
  public function setPageFooters($pageFooters)
  {
    $this->pageFooters = $pageFooters;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageFooter[]
   */
  public function getPageFooters()
  {
    return $this->pageFooters;
  }
  /**
   * Page headers associated with the chunk.
   *
   * @param GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageHeader[] $pageHeaders
   */
  public function setPageHeaders($pageHeaders)
  {
    $this->pageHeaders = $pageHeaders;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageHeader[]
   */
  public function getPageHeaders()
  {
    return $this->pageHeaders;
  }
  /**
   * Page span of the chunk.
   *
   * @param GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageSpan $pageSpan
   */
  public function setPageSpan(GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageSpan $pageSpan)
  {
    $this->pageSpan = $pageSpan;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentChunkedDocumentChunkChunkPageSpan
   */
  public function getPageSpan()
  {
    return $this->pageSpan;
  }
  /**
   * Unused.
   *
   * @param string[] $sourceBlockIds
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
class_alias(GoogleCloudDocumentaiV1DocumentChunkedDocumentChunk::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentChunkedDocumentChunk');
