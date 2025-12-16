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

namespace Google\Service\Docs;

class TextStyleSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to background_color.
   *
   * @var bool
   */
  public $backgroundColorSuggested;
  /**
   * Indicates if there was a suggested change to baseline_offset.
   *
   * @var bool
   */
  public $baselineOffsetSuggested;
  /**
   * Indicates if there was a suggested change to bold.
   *
   * @var bool
   */
  public $boldSuggested;
  /**
   * Indicates if there was a suggested change to font_size.
   *
   * @var bool
   */
  public $fontSizeSuggested;
  /**
   * Indicates if there was a suggested change to foreground_color.
   *
   * @var bool
   */
  public $foregroundColorSuggested;
  /**
   * Indicates if there was a suggested change to italic.
   *
   * @var bool
   */
  public $italicSuggested;
  /**
   * Indicates if there was a suggested change to link.
   *
   * @var bool
   */
  public $linkSuggested;
  /**
   * Indicates if there was a suggested change to small_caps.
   *
   * @var bool
   */
  public $smallCapsSuggested;
  /**
   * Indicates if there was a suggested change to strikethrough.
   *
   * @var bool
   */
  public $strikethroughSuggested;
  /**
   * Indicates if there was a suggested change to underline.
   *
   * @var bool
   */
  public $underlineSuggested;
  /**
   * Indicates if there was a suggested change to weighted_font_family.
   *
   * @var bool
   */
  public $weightedFontFamilySuggested;

  /**
   * Indicates if there was a suggested change to background_color.
   *
   * @param bool $backgroundColorSuggested
   */
  public function setBackgroundColorSuggested($backgroundColorSuggested)
  {
    $this->backgroundColorSuggested = $backgroundColorSuggested;
  }
  /**
   * @return bool
   */
  public function getBackgroundColorSuggested()
  {
    return $this->backgroundColorSuggested;
  }
  /**
   * Indicates if there was a suggested change to baseline_offset.
   *
   * @param bool $baselineOffsetSuggested
   */
  public function setBaselineOffsetSuggested($baselineOffsetSuggested)
  {
    $this->baselineOffsetSuggested = $baselineOffsetSuggested;
  }
  /**
   * @return bool
   */
  public function getBaselineOffsetSuggested()
  {
    return $this->baselineOffsetSuggested;
  }
  /**
   * Indicates if there was a suggested change to bold.
   *
   * @param bool $boldSuggested
   */
  public function setBoldSuggested($boldSuggested)
  {
    $this->boldSuggested = $boldSuggested;
  }
  /**
   * @return bool
   */
  public function getBoldSuggested()
  {
    return $this->boldSuggested;
  }
  /**
   * Indicates if there was a suggested change to font_size.
   *
   * @param bool $fontSizeSuggested
   */
  public function setFontSizeSuggested($fontSizeSuggested)
  {
    $this->fontSizeSuggested = $fontSizeSuggested;
  }
  /**
   * @return bool
   */
  public function getFontSizeSuggested()
  {
    return $this->fontSizeSuggested;
  }
  /**
   * Indicates if there was a suggested change to foreground_color.
   *
   * @param bool $foregroundColorSuggested
   */
  public function setForegroundColorSuggested($foregroundColorSuggested)
  {
    $this->foregroundColorSuggested = $foregroundColorSuggested;
  }
  /**
   * @return bool
   */
  public function getForegroundColorSuggested()
  {
    return $this->foregroundColorSuggested;
  }
  /**
   * Indicates if there was a suggested change to italic.
   *
   * @param bool $italicSuggested
   */
  public function setItalicSuggested($italicSuggested)
  {
    $this->italicSuggested = $italicSuggested;
  }
  /**
   * @return bool
   */
  public function getItalicSuggested()
  {
    return $this->italicSuggested;
  }
  /**
   * Indicates if there was a suggested change to link.
   *
   * @param bool $linkSuggested
   */
  public function setLinkSuggested($linkSuggested)
  {
    $this->linkSuggested = $linkSuggested;
  }
  /**
   * @return bool
   */
  public function getLinkSuggested()
  {
    return $this->linkSuggested;
  }
  /**
   * Indicates if there was a suggested change to small_caps.
   *
   * @param bool $smallCapsSuggested
   */
  public function setSmallCapsSuggested($smallCapsSuggested)
  {
    $this->smallCapsSuggested = $smallCapsSuggested;
  }
  /**
   * @return bool
   */
  public function getSmallCapsSuggested()
  {
    return $this->smallCapsSuggested;
  }
  /**
   * Indicates if there was a suggested change to strikethrough.
   *
   * @param bool $strikethroughSuggested
   */
  public function setStrikethroughSuggested($strikethroughSuggested)
  {
    $this->strikethroughSuggested = $strikethroughSuggested;
  }
  /**
   * @return bool
   */
  public function getStrikethroughSuggested()
  {
    return $this->strikethroughSuggested;
  }
  /**
   * Indicates if there was a suggested change to underline.
   *
   * @param bool $underlineSuggested
   */
  public function setUnderlineSuggested($underlineSuggested)
  {
    $this->underlineSuggested = $underlineSuggested;
  }
  /**
   * @return bool
   */
  public function getUnderlineSuggested()
  {
    return $this->underlineSuggested;
  }
  /**
   * Indicates if there was a suggested change to weighted_font_family.
   *
   * @param bool $weightedFontFamilySuggested
   */
  public function setWeightedFontFamilySuggested($weightedFontFamilySuggested)
  {
    $this->weightedFontFamilySuggested = $weightedFontFamilySuggested;
  }
  /**
   * @return bool
   */
  public function getWeightedFontFamilySuggested()
  {
    return $this->weightedFontFamilySuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextStyleSuggestionState::class, 'Google_Service_Docs_TextStyleSuggestionState');
