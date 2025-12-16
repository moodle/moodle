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

class ImagePropertiesSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to angle.
   *
   * @var bool
   */
  public $angleSuggested;
  /**
   * Indicates if there was a suggested change to brightness.
   *
   * @var bool
   */
  public $brightnessSuggested;
  /**
   * Indicates if there was a suggested change to content_uri.
   *
   * @var bool
   */
  public $contentUriSuggested;
  /**
   * Indicates if there was a suggested change to contrast.
   *
   * @var bool
   */
  public $contrastSuggested;
  protected $cropPropertiesSuggestionStateType = CropPropertiesSuggestionState::class;
  protected $cropPropertiesSuggestionStateDataType = '';
  /**
   * Indicates if there was a suggested change to source_uri.
   *
   * @var bool
   */
  public $sourceUriSuggested;
  /**
   * Indicates if there was a suggested change to transparency.
   *
   * @var bool
   */
  public $transparencySuggested;

  /**
   * Indicates if there was a suggested change to angle.
   *
   * @param bool $angleSuggested
   */
  public function setAngleSuggested($angleSuggested)
  {
    $this->angleSuggested = $angleSuggested;
  }
  /**
   * @return bool
   */
  public function getAngleSuggested()
  {
    return $this->angleSuggested;
  }
  /**
   * Indicates if there was a suggested change to brightness.
   *
   * @param bool $brightnessSuggested
   */
  public function setBrightnessSuggested($brightnessSuggested)
  {
    $this->brightnessSuggested = $brightnessSuggested;
  }
  /**
   * @return bool
   */
  public function getBrightnessSuggested()
  {
    return $this->brightnessSuggested;
  }
  /**
   * Indicates if there was a suggested change to content_uri.
   *
   * @param bool $contentUriSuggested
   */
  public function setContentUriSuggested($contentUriSuggested)
  {
    $this->contentUriSuggested = $contentUriSuggested;
  }
  /**
   * @return bool
   */
  public function getContentUriSuggested()
  {
    return $this->contentUriSuggested;
  }
  /**
   * Indicates if there was a suggested change to contrast.
   *
   * @param bool $contrastSuggested
   */
  public function setContrastSuggested($contrastSuggested)
  {
    $this->contrastSuggested = $contrastSuggested;
  }
  /**
   * @return bool
   */
  public function getContrastSuggested()
  {
    return $this->contrastSuggested;
  }
  /**
   * A mask that indicates which of the fields in crop_properties have been
   * changed in this suggestion.
   *
   * @param CropPropertiesSuggestionState $cropPropertiesSuggestionState
   */
  public function setCropPropertiesSuggestionState(CropPropertiesSuggestionState $cropPropertiesSuggestionState)
  {
    $this->cropPropertiesSuggestionState = $cropPropertiesSuggestionState;
  }
  /**
   * @return CropPropertiesSuggestionState
   */
  public function getCropPropertiesSuggestionState()
  {
    return $this->cropPropertiesSuggestionState;
  }
  /**
   * Indicates if there was a suggested change to source_uri.
   *
   * @param bool $sourceUriSuggested
   */
  public function setSourceUriSuggested($sourceUriSuggested)
  {
    $this->sourceUriSuggested = $sourceUriSuggested;
  }
  /**
   * @return bool
   */
  public function getSourceUriSuggested()
  {
    return $this->sourceUriSuggested;
  }
  /**
   * Indicates if there was a suggested change to transparency.
   *
   * @param bool $transparencySuggested
   */
  public function setTransparencySuggested($transparencySuggested)
  {
    $this->transparencySuggested = $transparencySuggested;
  }
  /**
   * @return bool
   */
  public function getTransparencySuggested()
  {
    return $this->transparencySuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImagePropertiesSuggestionState::class, 'Google_Service_Docs_ImagePropertiesSuggestionState');
