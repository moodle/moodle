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

class EmbeddedObjectBorderSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to color.
   *
   * @var bool
   */
  public $colorSuggested;
  /**
   * Indicates if there was a suggested change to dash_style.
   *
   * @var bool
   */
  public $dashStyleSuggested;
  /**
   * Indicates if there was a suggested change to property_state.
   *
   * @var bool
   */
  public $propertyStateSuggested;
  /**
   * Indicates if there was a suggested change to width.
   *
   * @var bool
   */
  public $widthSuggested;

  /**
   * Indicates if there was a suggested change to color.
   *
   * @param bool $colorSuggested
   */
  public function setColorSuggested($colorSuggested)
  {
    $this->colorSuggested = $colorSuggested;
  }
  /**
   * @return bool
   */
  public function getColorSuggested()
  {
    return $this->colorSuggested;
  }
  /**
   * Indicates if there was a suggested change to dash_style.
   *
   * @param bool $dashStyleSuggested
   */
  public function setDashStyleSuggested($dashStyleSuggested)
  {
    $this->dashStyleSuggested = $dashStyleSuggested;
  }
  /**
   * @return bool
   */
  public function getDashStyleSuggested()
  {
    return $this->dashStyleSuggested;
  }
  /**
   * Indicates if there was a suggested change to property_state.
   *
   * @param bool $propertyStateSuggested
   */
  public function setPropertyStateSuggested($propertyStateSuggested)
  {
    $this->propertyStateSuggested = $propertyStateSuggested;
  }
  /**
   * @return bool
   */
  public function getPropertyStateSuggested()
  {
    return $this->propertyStateSuggested;
  }
  /**
   * Indicates if there was a suggested change to width.
   *
   * @param bool $widthSuggested
   */
  public function setWidthSuggested($widthSuggested)
  {
    $this->widthSuggested = $widthSuggested;
  }
  /**
   * @return bool
   */
  public function getWidthSuggested()
  {
    return $this->widthSuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EmbeddedObjectBorderSuggestionState::class, 'Google_Service_Docs_EmbeddedObjectBorderSuggestionState');
