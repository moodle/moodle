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

class EmbeddedObjectSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to description.
   *
   * @var bool
   */
  public $descriptionSuggested;
  protected $embeddedDrawingPropertiesSuggestionStateType = EmbeddedDrawingPropertiesSuggestionState::class;
  protected $embeddedDrawingPropertiesSuggestionStateDataType = '';
  protected $embeddedObjectBorderSuggestionStateType = EmbeddedObjectBorderSuggestionState::class;
  protected $embeddedObjectBorderSuggestionStateDataType = '';
  protected $imagePropertiesSuggestionStateType = ImagePropertiesSuggestionState::class;
  protected $imagePropertiesSuggestionStateDataType = '';
  protected $linkedContentReferenceSuggestionStateType = LinkedContentReferenceSuggestionState::class;
  protected $linkedContentReferenceSuggestionStateDataType = '';
  /**
   * Indicates if there was a suggested change to margin_bottom.
   *
   * @var bool
   */
  public $marginBottomSuggested;
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
  protected $sizeSuggestionStateType = SizeSuggestionState::class;
  protected $sizeSuggestionStateDataType = '';
  /**
   * Indicates if there was a suggested change to title.
   *
   * @var bool
   */
  public $titleSuggested;

  /**
   * Indicates if there was a suggested change to description.
   *
   * @param bool $descriptionSuggested
   */
  public function setDescriptionSuggested($descriptionSuggested)
  {
    $this->descriptionSuggested = $descriptionSuggested;
  }
  /**
   * @return bool
   */
  public function getDescriptionSuggested()
  {
    return $this->descriptionSuggested;
  }
  /**
   * A mask that indicates which of the fields in embedded_drawing_properties
   * have been changed in this suggestion.
   *
   * @param EmbeddedDrawingPropertiesSuggestionState $embeddedDrawingPropertiesSuggestionState
   */
  public function setEmbeddedDrawingPropertiesSuggestionState(EmbeddedDrawingPropertiesSuggestionState $embeddedDrawingPropertiesSuggestionState)
  {
    $this->embeddedDrawingPropertiesSuggestionState = $embeddedDrawingPropertiesSuggestionState;
  }
  /**
   * @return EmbeddedDrawingPropertiesSuggestionState
   */
  public function getEmbeddedDrawingPropertiesSuggestionState()
  {
    return $this->embeddedDrawingPropertiesSuggestionState;
  }
  /**
   * A mask that indicates which of the fields in embedded_object_border have
   * been changed in this suggestion.
   *
   * @param EmbeddedObjectBorderSuggestionState $embeddedObjectBorderSuggestionState
   */
  public function setEmbeddedObjectBorderSuggestionState(EmbeddedObjectBorderSuggestionState $embeddedObjectBorderSuggestionState)
  {
    $this->embeddedObjectBorderSuggestionState = $embeddedObjectBorderSuggestionState;
  }
  /**
   * @return EmbeddedObjectBorderSuggestionState
   */
  public function getEmbeddedObjectBorderSuggestionState()
  {
    return $this->embeddedObjectBorderSuggestionState;
  }
  /**
   * A mask that indicates which of the fields in image_properties have been
   * changed in this suggestion.
   *
   * @param ImagePropertiesSuggestionState $imagePropertiesSuggestionState
   */
  public function setImagePropertiesSuggestionState(ImagePropertiesSuggestionState $imagePropertiesSuggestionState)
  {
    $this->imagePropertiesSuggestionState = $imagePropertiesSuggestionState;
  }
  /**
   * @return ImagePropertiesSuggestionState
   */
  public function getImagePropertiesSuggestionState()
  {
    return $this->imagePropertiesSuggestionState;
  }
  /**
   * A mask that indicates which of the fields in linked_content_reference have
   * been changed in this suggestion.
   *
   * @param LinkedContentReferenceSuggestionState $linkedContentReferenceSuggestionState
   */
  public function setLinkedContentReferenceSuggestionState(LinkedContentReferenceSuggestionState $linkedContentReferenceSuggestionState)
  {
    $this->linkedContentReferenceSuggestionState = $linkedContentReferenceSuggestionState;
  }
  /**
   * @return LinkedContentReferenceSuggestionState
   */
  public function getLinkedContentReferenceSuggestionState()
  {
    return $this->linkedContentReferenceSuggestionState;
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
   * A mask that indicates which of the fields in size have been changed in this
   * suggestion.
   *
   * @param SizeSuggestionState $sizeSuggestionState
   */
  public function setSizeSuggestionState(SizeSuggestionState $sizeSuggestionState)
  {
    $this->sizeSuggestionState = $sizeSuggestionState;
  }
  /**
   * @return SizeSuggestionState
   */
  public function getSizeSuggestionState()
  {
    return $this->sizeSuggestionState;
  }
  /**
   * Indicates if there was a suggested change to title.
   *
   * @param bool $titleSuggested
   */
  public function setTitleSuggested($titleSuggested)
  {
    $this->titleSuggested = $titleSuggested;
  }
  /**
   * @return bool
   */
  public function getTitleSuggested()
  {
    return $this->titleSuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EmbeddedObjectSuggestionState::class, 'Google_Service_Docs_EmbeddedObjectSuggestionState');
