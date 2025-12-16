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

class GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTextBlock extends \Google\Collection
{
  protected $collection_key = 'blocks';
  protected $blocksType = GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlock::class;
  protected $blocksDataType = 'array';
  /**
   * Text content stored in the block.
   *
   * @var string
   */
  public $text;
  /**
   * Type of the text in the block. Available options are: `paragraph`,
   * `subtitle`, `heading-1`, `heading-2`, `heading-3`, `heading-4`,
   * `heading-5`, `header`, `footer`.
   *
   * @var string
   */
  public $type;

  /**
   * A text block could further have child blocks. Repeated blocks support
   * further hierarchies and nested blocks.
   *
   * @param GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlock[] $blocks
   */
  public function setBlocks($blocks)
  {
    $this->blocks = $blocks;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlock[]
   */
  public function getBlocks()
  {
    return $this->blocks;
  }
  /**
   * Text content stored in the block.
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
   * Type of the text in the block. Available options are: `paragraph`,
   * `subtitle`, `heading-1`, `heading-2`, `heading-3`, `heading-4`,
   * `heading-5`, `header`, `footer`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTextBlock::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentDocumentLayoutDocumentLayoutBlockLayoutTextBlock');
