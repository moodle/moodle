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

class DocumentStyleSuggestionState extends \Google\Model
{
  protected $backgroundSuggestionStateType = BackgroundSuggestionState::class;
  protected $backgroundSuggestionStateDataType = '';
  /**
   * Indicates if there was a suggested change to default_footer_id.
   *
   * @var bool
   */
  public $defaultFooterIdSuggested;
  /**
   * Indicates if there was a suggested change to default_header_id.
   *
   * @var bool
   */
  public $defaultHeaderIdSuggested;
  /**
   * Indicates if there was a suggested change to even_page_footer_id.
   *
   * @var bool
   */
  public $evenPageFooterIdSuggested;
  /**
   * Indicates if there was a suggested change to even_page_header_id.
   *
   * @var bool
   */
  public $evenPageHeaderIdSuggested;
  /**
   * Indicates if there was a suggested change to first_page_footer_id.
   *
   * @var bool
   */
  public $firstPageFooterIdSuggested;
  /**
   * Indicates if there was a suggested change to first_page_header_id.
   *
   * @var bool
   */
  public $firstPageHeaderIdSuggested;
  /**
   * Optional. Indicates if there was a suggested change to
   * flip_page_orientation.
   *
   * @var bool
   */
  public $flipPageOrientationSuggested;
  /**
   * Indicates if there was a suggested change to margin_bottom.
   *
   * @var bool
   */
  public $marginBottomSuggested;
  /**
   * Indicates if there was a suggested change to margin_footer.
   *
   * @var bool
   */
  public $marginFooterSuggested;
  /**
   * Indicates if there was a suggested change to margin_header.
   *
   * @var bool
   */
  public $marginHeaderSuggested;
  /**
   * Indicates if there was a suggested change to margin_left.
   *
   * @var bool
   */
  public $marginLeftSuggested;
  /**
   * Indicates if there was a suggested change to margin_right.
   *
   * @var bool
   */
  public $marginRightSuggested;
  /**
   * Indicates if there was a suggested change to margin_top.
   *
   * @var bool
   */
  public $marginTopSuggested;
  /**
   * Indicates if there was a suggested change to page_number_start.
   *
   * @var bool
   */
  public $pageNumberStartSuggested;
  protected $pageSizeSuggestionStateType = SizeSuggestionState::class;
  protected $pageSizeSuggestionStateDataType = '';
  /**
   * Indicates if there was a suggested change to
   * use_custom_header_footer_margins.
   *
   * @var bool
   */
  public $useCustomHeaderFooterMarginsSuggested;
  /**
   * Indicates if there was a suggested change to use_even_page_header_footer.
   *
   * @var bool
   */
  public $useEvenPageHeaderFooterSuggested;
  /**
   * Indicates if there was a suggested change to use_first_page_header_footer.
   *
   * @var bool
   */
  public $useFirstPageHeaderFooterSuggested;

  /**
   * A mask that indicates which of the fields in background have been changed
   * in this suggestion.
   *
   * @param BackgroundSuggestionState $backgroundSuggestionState
   */
  public function setBackgroundSuggestionState(BackgroundSuggestionState $backgroundSuggestionState)
  {
    $this->backgroundSuggestionState = $backgroundSuggestionState;
  }
  /**
   * @return BackgroundSuggestionState
   */
  public function getBackgroundSuggestionState()
  {
    return $this->backgroundSuggestionState;
  }
  /**
   * Indicates if there was a suggested change to default_footer_id.
   *
   * @param bool $defaultFooterIdSuggested
   */
  public function setDefaultFooterIdSuggested($defaultFooterIdSuggested)
  {
    $this->defaultFooterIdSuggested = $defaultFooterIdSuggested;
  }
  /**
   * @return bool
   */
  public function getDefaultFooterIdSuggested()
  {
    return $this->defaultFooterIdSuggested;
  }
  /**
   * Indicates if there was a suggested change to default_header_id.
   *
   * @param bool $defaultHeaderIdSuggested
   */
  public function setDefaultHeaderIdSuggested($defaultHeaderIdSuggested)
  {
    $this->defaultHeaderIdSuggested = $defaultHeaderIdSuggested;
  }
  /**
   * @return bool
   */
  public function getDefaultHeaderIdSuggested()
  {
    return $this->defaultHeaderIdSuggested;
  }
  /**
   * Indicates if there was a suggested change to even_page_footer_id.
   *
   * @param bool $evenPageFooterIdSuggested
   */
  public function setEvenPageFooterIdSuggested($evenPageFooterIdSuggested)
  {
    $this->evenPageFooterIdSuggested = $evenPageFooterIdSuggested;
  }
  /**
   * @return bool
   */
  public function getEvenPageFooterIdSuggested()
  {
    return $this->evenPageFooterIdSuggested;
  }
  /**
   * Indicates if there was a suggested change to even_page_header_id.
   *
   * @param bool $evenPageHeaderIdSuggested
   */
  public function setEvenPageHeaderIdSuggested($evenPageHeaderIdSuggested)
  {
    $this->evenPageHeaderIdSuggested = $evenPageHeaderIdSuggested;
  }
  /**
   * @return bool
   */
  public function getEvenPageHeaderIdSuggested()
  {
    return $this->evenPageHeaderIdSuggested;
  }
  /**
   * Indicates if there was a suggested change to first_page_footer_id.
   *
   * @param bool $firstPageFooterIdSuggested
   */
  public function setFirstPageFooterIdSuggested($firstPageFooterIdSuggested)
  {
    $this->firstPageFooterIdSuggested = $firstPageFooterIdSuggested;
  }
  /**
   * @return bool
   */
  public function getFirstPageFooterIdSuggested()
  {
    return $this->firstPageFooterIdSuggested;
  }
  /**
   * Indicates if there was a suggested change to first_page_header_id.
   *
   * @param bool $firstPageHeaderIdSuggested
   */
  public function setFirstPageHeaderIdSuggested($firstPageHeaderIdSuggested)
  {
    $this->firstPageHeaderIdSuggested = $firstPageHeaderIdSuggested;
  }
  /**
   * @return bool
   */
  public function getFirstPageHeaderIdSuggested()
  {
    return $this->firstPageHeaderIdSuggested;
  }
  /**
   * Optional. Indicates if there was a suggested change to
   * flip_page_orientation.
   *
   * @param bool $flipPageOrientationSuggested
   */
  public function setFlipPageOrientationSuggested($flipPageOrientationSuggested)
  {
    $this->flipPageOrientationSuggested = $flipPageOrientationSuggested;
  }
  /**
   * @return bool
   */
  public function getFlipPageOrientationSuggested()
  {
    return $this->flipPageOrientationSuggested;
  }
  /**
   * Indicates if there was a suggested change to margin_bottom.
   *
   * @param bool $marginBottomSuggested
   */
  public function setMarginBottomSuggested($marginBottomSuggested)
  {
    $this->marginBottomSuggested = $marginBottomSuggested;
  }
  /**
   * @return bool
   */
  public function getMarginBottomSuggested()
  {
    return $this->marginBottomSuggested;
  }
  /**
   * Indicates if there was a suggested change to margin_footer.
   *
   * @param bool $marginFooterSuggested
   */
  public function setMarginFooterSuggested($marginFooterSuggested)
  {
    $this->marginFooterSuggested = $marginFooterSuggested;
  }
  /**
   * @return bool
   */
  public function getMarginFooterSuggested()
  {
    return $this->marginFooterSuggested;
  }
  /**
   * Indicates if there was a suggested change to margin_header.
   *
   * @param bool $marginHeaderSuggested
   */
  public function setMarginHeaderSuggested($marginHeaderSuggested)
  {
    $this->marginHeaderSuggested = $marginHeaderSuggested;
  }
  /**
   * @return bool
   */
  public function getMarginHeaderSuggested()
  {
    return $this->marginHeaderSuggested;
  }
  /**
   * Indicates if there was a suggested change to margin_left.
   *
   * @param bool $marginLeftSuggested
   */
  public function setMarginLeftSuggested($marginLeftSuggested)
  {
    $this->marginLeftSuggested = $marginLeftSuggested;
  }
  /**
   * @return bool
   */
  public function getMarginLeftSuggested()
  {
    return $this->marginLeftSuggested;
  }
  /**
   * Indicates if there was a suggested change to margin_right.
   *
   * @param bool $marginRightSuggested
   */
  public function setMarginRightSuggested($marginRightSuggested)
  {
    $this->marginRightSuggested = $marginRightSuggested;
  }
  /**
   * @return bool
   */
  public function getMarginRightSuggested()
  {
    return $this->marginRightSuggested;
  }
  /**
   * Indicates if there was a suggested change to margin_top.
   *
   * @param bool $marginTopSuggested
   */
  public function setMarginTopSuggested($marginTopSuggested)
  {
    $this->marginTopSuggested = $marginTopSuggested;
  }
  /**
   * @return bool
   */
  public function getMarginTopSuggested()
  {
    return $this->marginTopSuggested;
  }
  /**
   * Indicates if there was a suggested change to page_number_start.
   *
   * @param bool $pageNumberStartSuggested
   */
  public function setPageNumberStartSuggested($pageNumberStartSuggested)
  {
    $this->pageNumberStartSuggested = $pageNumberStartSuggested;
  }
  /**
   * @return bool
   */
  public function getPageNumberStartSuggested()
  {
    return $this->pageNumberStartSuggested;
  }
  /**
   * A mask that indicates which of the fields in size have been changed in this
   * suggestion.
   *
   * @param SizeSuggestionState $pageSizeSuggestionState
   */
  public function setPageSizeSuggestionState(SizeSuggestionState $pageSizeSuggestionState)
  {
    $this->pageSizeSuggestionState = $pageSizeSuggestionState;
  }
  /**
   * @return SizeSuggestionState
   */
  public function getPageSizeSuggestionState()
  {
    return $this->pageSizeSuggestionState;
  }
  /**
   * Indicates if there was a suggested change to
   * use_custom_header_footer_margins.
   *
   * @param bool $useCustomHeaderFooterMarginsSuggested
   */
  public function setUseCustomHeaderFooterMarginsSuggested($useCustomHeaderFooterMarginsSuggested)
  {
    $this->useCustomHeaderFooterMarginsSuggested = $useCustomHeaderFooterMarginsSuggested;
  }
  /**
   * @return bool
   */
  public function getUseCustomHeaderFooterMarginsSuggested()
  {
    return $this->useCustomHeaderFooterMarginsSuggested;
  }
  /**
   * Indicates if there was a suggested change to use_even_page_header_footer.
   *
   * @param bool $useEvenPageHeaderFooterSuggested
   */
  public function setUseEvenPageHeaderFooterSuggested($useEvenPageHeaderFooterSuggested)
  {
    $this->useEvenPageHeaderFooterSuggested = $useEvenPageHeaderFooterSuggested;
  }
  /**
   * @return bool
   */
  public function getUseEvenPageHeaderFooterSuggested()
  {
    return $this->useEvenPageHeaderFooterSuggested;
  }
  /**
   * Indicates if there was a suggested change to use_first_page_header_footer.
   *
   * @param bool $useFirstPageHeaderFooterSuggested
   */
  public function setUseFirstPageHeaderFooterSuggested($useFirstPageHeaderFooterSuggested)
  {
    $this->useFirstPageHeaderFooterSuggested = $useFirstPageHeaderFooterSuggested;
  }
  /**
   * @return bool
   */
  public function getUseFirstPageHeaderFooterSuggested()
  {
    return $this->useFirstPageHeaderFooterSuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DocumentStyleSuggestionState::class, 'Google_Service_Docs_DocumentStyleSuggestionState');
