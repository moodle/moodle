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

namespace Google\Service\Slides;

class TextElement extends \Google\Model
{
  protected $autoTextType = AutoText::class;
  protected $autoTextDataType = '';
  /**
   * The zero-based end index of this text element, exclusive, in Unicode code
   * units.
   *
   * @var int
   */
  public $endIndex;
  protected $paragraphMarkerType = ParagraphMarker::class;
  protected $paragraphMarkerDataType = '';
  /**
   * The zero-based start index of this text element, in Unicode code units.
   *
   * @var int
   */
  public $startIndex;
  protected $textRunType = TextRun::class;
  protected $textRunDataType = '';

  /**
   * A TextElement representing a spot in the text that is dynamically replaced
   * with content that can change over time.
   *
   * @param AutoText $autoText
   */
  public function setAutoText(AutoText $autoText)
  {
    $this->autoText = $autoText;
  }
  /**
   * @return AutoText
   */
  public function getAutoText()
  {
    return $this->autoText;
  }
  /**
   * The zero-based end index of this text element, exclusive, in Unicode code
   * units.
   *
   * @param int $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return int
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * A marker representing the beginning of a new paragraph. The `start_index`
   * and `end_index` of this TextElement represent the range of the paragraph.
   * Other TextElements with an index range contained inside this paragraph's
   * range are considered to be part of this paragraph. The range of indices of
   * two separate paragraphs will never overlap.
   *
   * @param ParagraphMarker $paragraphMarker
   */
  public function setParagraphMarker(ParagraphMarker $paragraphMarker)
  {
    $this->paragraphMarker = $paragraphMarker;
  }
  /**
   * @return ParagraphMarker
   */
  public function getParagraphMarker()
  {
    return $this->paragraphMarker;
  }
  /**
   * The zero-based start index of this text element, in Unicode code units.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * A TextElement representing a run of text where all of the characters in the
   * run have the same TextStyle. The `start_index` and `end_index` of TextRuns
   * will always be fully contained in the index range of a single
   * `paragraph_marker` TextElement. In other words, a TextRun will never span
   * multiple paragraphs.
   *
   * @param TextRun $textRun
   */
  public function setTextRun(TextRun $textRun)
  {
    $this->textRun = $textRun;
  }
  /**
   * @return TextRun
   */
  public function getTextRun()
  {
    return $this->textRun;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextElement::class, 'Google_Service_Slides_TextElement');
