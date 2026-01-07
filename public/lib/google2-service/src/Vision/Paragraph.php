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

class Paragraph extends \Google\Collection
{
  protected $collection_key = 'words';
  protected $boundingBoxType = BoundingPoly::class;
  protected $boundingBoxDataType = '';
  /**
   * Confidence of the OCR results for the paragraph. Range [0, 1].
   *
   * @var float
   */
  public $confidence;
  protected $propertyType = TextProperty::class;
  protected $propertyDataType = '';
  protected $wordsType = Word::class;
  protected $wordsDataType = 'array';

  /**
   * The bounding box for the paragraph. The vertices are in the order of top-
   * left, top-right, bottom-right, bottom-left. When a rotation of the bounding
   * box is detected the rotation is represented as around the top-left corner
   * as defined when the text is read in the 'natural' orientation. For example:
   * * when the text is horizontal it might look like: 0----1 | | 3----2 * when
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
   * Confidence of the OCR results for the paragraph. Range [0, 1].
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
   * Additional information detected for the paragraph.
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
  /**
   * List of all words in this paragraph.
   *
   * @param Word[] $words
   */
  public function setWords($words)
  {
    $this->words = $words;
  }
  /**
   * @return Word[]
   */
  public function getWords()
  {
    return $this->words;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Paragraph::class, 'Google_Service_Vision_Paragraph');
