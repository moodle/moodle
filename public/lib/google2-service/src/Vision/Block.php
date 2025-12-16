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

namespace Google\Service\Vision;

class Block extends \Google\Collection
{
  /**
   * Unknown block type.
   */
  public const BLOCK_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Regular text block.
   */
  public const BLOCK_TYPE_TEXT = 'TEXT';
  /**
   * Table block.
   */
  public const BLOCK_TYPE_TABLE = 'TABLE';
  /**
   * Image block.
   */
  public const BLOCK_TYPE_PICTURE = 'PICTURE';
  /**
   * Horizontal/vertical line box.
   */
  public const BLOCK_TYPE_RULER = 'RULER';
  /**
   * Barcode block.
   */
  public const BLOCK_TYPE_BARCODE = 'BARCODE';
  protected $collection_key = 'paragraphs';
  /**
   * Detected block type (text, image etc) for this block.
   *
   * @var string
   */
  public $blockType;
  protected $boundingBoxType = BoundingPoly::class;
  protected $boundingBoxDataType = '';
  /**
   * Confidence of the OCR results on the block. Range [0, 1].
   *
   * @var float
   */
  public $confidence;
  protected $paragraphsType = Paragraph::class;
  protected $paragraphsDataType = 'array';
  protected $propertyType = TextProperty::class;
  protected $propertyDataType = '';

  /**
   * Detected block type (text, image etc) for this block.
   *
   * Accepted values: UNKNOWN, TEXT, TABLE, PICTURE, RULER, BARCODE
   *
   * @param self::BLOCK_TYPE_* $blockType
   */
  public function setBlockType($blockType)
  {
    $this->blockType = $blockType;
  }
  /**
   * @return self::BLOCK_TYPE_*
   */
  public function getBlockType()
  {
    return $this->blockType;
  }
  /**
   * The bounding box for the block. The vertices are in the order of top-left,
   * top-right, bottom-right, bottom-left. When a rotation of the bounding box
   * is detected the rotation is represented as around the top-left corner as
   * defined when the text is read in the 'natural' orientation. For example: *
   * when the text is horizontal it might look like: 0----1 | | 3----2 * when
   * it's rotated 180 degrees around the top-left corner it becomes: 2----3 | |
   * 1----0 and the vertex order will still be (0, 1, 2, 3).
   *
   * @param BoundingPoly $boundingBox
   */
  public function setBoundingBox(BoundingPoly $boundingBox)
  {
    $this->boundingBox = $boundingBox;
  }
  /**
   * @return BoundingPoly
   */
  public function getBoundingBox()
  {
    return $this->boundingBox;
  }
  /**
   * Confidence of the OCR results on the block. Range [0, 1].
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * List of paragraphs in this block (if this blocks is of type text).
   *
   * @param Paragraph[] $paragraphs
   */
  public function setParagraphs($paragraphs)
  {
    $this->paragraphs = $paragraphs;
  }
  /**
   * @return Paragraph[]
   */
  public function getParagraphs()
  {
    return $this->paragraphs;
  }
  /**
   * Additional information detected for the block.
   *
   * @param TextProperty $property
   */
  public function setProperty(TextProperty $property)
  {
    $this->property = $property;
  }
  /**
   * @return TextProperty
   */
  public function getProperty()
  {
    return $this->property;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Block::class, 'Google_Service_Vision_Block');
