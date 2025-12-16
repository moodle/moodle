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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedText extends \Google\Collection
{
  protected $collection_key = 'highlightedTextRanges';
  protected $highlightedTextRangesType = GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedTextHighlightedTextRange::class;
  protected $highlightedTextRangesDataType = 'array';
  /**
   * @var string
   */
  public $text;

  /**
   * The list of the ranges of the highlighted text.
   *
   * @param GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedTextHighlightedTextRange[] $highlightedTextRanges
   */
  public function setHighlightedTextRanges($highlightedTextRanges)
  {
    $this->highlightedTextRanges = $highlightedTextRanges;
  }
  /**
   * @return GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedTextHighlightedTextRange[]
   */
  public function getHighlightedTextRanges()
  {
    return $this->highlightedTextRanges;
  }
  /**
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedText::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedText');
