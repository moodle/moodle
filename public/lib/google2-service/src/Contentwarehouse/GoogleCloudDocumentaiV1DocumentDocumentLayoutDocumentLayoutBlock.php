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

class GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlock extends \Google\Model
{
  /**
   * ID of the block.
   *
   * @var string
   */
  public $blockId;
  protected $listBlockType = GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutListBlock::class;
  protected $listBlockDataType = '';
  protected $pageSpanType = GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutPageSpan::class;
  protected $pageSpanDataType = '';
  protected $tableBlockType = GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTableBlock::class;
  protected $tableBlockDataType = '';
  protected $textBlockType = GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTextBlock::class;
  protected $textBlockDataType = '';

  /**
   * ID of the block.
   *
   * @param string $blockId
   */
  public function setBlockId($blockId)
  {
    $this->blockId = $blockId;
  }
  /**
   * @return string
   */
  public function getBlockId()
  {
    return $this->blockId;
  }
  /**
   * Block consisting of list content/structure.
   *
   * @param GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutListBlock $listBlock
   */
  public function setListBlock(GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutListBlock $listBlock)
  {
    $this->listBlock = $listBlock;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutListBlock
   */
  public function getListBlock()
  {
    return $this->listBlock;
  }
  /**
   * Page span of the block.
   *
   * @param GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutPageSpan $pageSpan
   */
  public function setPageSpan(GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutPageSpan $pageSpan)
  {
    $this->pageSpan = $pageSpan;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutPageSpan
   */
  public function getPageSpan()
  {
    return $this->pageSpan;
  }
  /**
   * Block consisting of table content/structure.
   *
   * @param GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTableBlock $tableBlock
   */
  public function setTableBlock(GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTableBlock $tableBlock)
  {
    $this->tableBlock = $tableBlock;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTableBlock
   */
  public function getTableBlock()
  {
    return $this->tableBlock;
  }
  /**
   * Block consisting of text content.
   *
   * @param GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTextBlock $textBlock
   */
  public function setTextBlock(GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTextBlock $textBlock)
  {
    $this->textBlock = $textBlock;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTextBlock
   */
  public function getTextBlock()
  {
    return $this->textBlock;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlock::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlock');
