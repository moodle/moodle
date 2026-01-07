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

class GoogleCloudDocumentaiV1beta1DocumentChunkedDocumentChunkChunkPageHeader extends \Google\Model
{
  protected $pageSpanType = GoogleCloudDocumentaiV1beta1DocumentChunkedDocumentChunkChunkPageSpan::class;
  protected $pageSpanDataType = '';
  /**
   * @var string
   */
  public $text;

  /**
   * @param GoogleCloudDocumentaiV1beta1DocumentChunkedDocumentChunkChunkPageSpan
   */
  public function setPageSpan(GoogleCloudDocumentaiV1beta1DocumentChunkedDocumentChunkChunkPageSpan $pageSpan)
  {
    $this->pageSpan = $pageSpan;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta1DocumentChunkedDocumentChunkChunkPageSpan
   */
  public function getPageSpan()
  {
    return $this->pageSpan;
  }
  /**
   * @param string
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta1DocumentChunkedDocumentChunkChunkPageHeader::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta1DocumentChunkedDocumentChunkChunkPageHeader');
