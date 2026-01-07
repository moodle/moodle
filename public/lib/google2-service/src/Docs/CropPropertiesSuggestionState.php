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

class CropPropertiesSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to angle.
   *
   * @var bool
   */
  public $angleSuggested;
  /**
   * Indicates if there was a suggested change to offset_bottom.
   *
   * @var bool
   */
  public $offsetBottomSuggested;
  /**
   * Indicates if there was a suggested change to offset_left.
   *
   * @var bool
   */
  public $offsetLeftSuggested;
  /**
   * Indicates if there was a suggested change to offset_right.
   *
   * @var bool
   */
  public $offsetRightSuggested;
  /**
   * Indicates if there was a suggested change to offset_top.
   *
   * @var bool
   */
  public $offsetTopSuggested;

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
   * Indicates if there was a suggested change to offset_bottom.
   *
   * @param bool $offsetBottomSuggested
   */
  public function setOffsetBottomSuggested($offsetBottomSuggested)
  {
    $this->offsetBottomSuggested = $offsetBottomSuggested;
  }
  /**
   * @return bool
   */
  public function getOffsetBottomSuggested()
  {
    return $this->offsetBottomSuggested;
  }
  /**
   * Indicates if there was a suggested change to offset_left.
   *
   * @param bool $offsetLeftSuggested
   */
  public function setOffsetLeftSuggested($offsetLeftSuggested)
  {
    $this->offsetLeftSuggested = $offsetLeftSuggested;
  }
  /**
   * @return bool
   */
  public function getOffsetLeftSuggested()
  {
    return $this->offsetLeftSuggested;
  }
  /**
   * Indicates if there was a suggested change to offset_right.
   *
   * @param bool $offsetRightSuggested
   */
  public function setOffsetRightSuggested($offsetRightSuggested)
  {
    $this->offsetRightSuggested = $offsetRightSuggested;
  }
  /**
   * @return bool
   */
  public function getOffsetRightSuggested()
  {
    return $this->offsetRightSuggested;
  }
  /**
   * Indicates if there was a suggested change to offset_top.
   *
   * @param bool $offsetTopSuggested
   */
  public function setOffsetTopSuggested($offsetTopSuggested)
  {
    $this->offsetTopSuggested = $offsetTopSuggested;
  }
  /**
   * @return bool
   */
  public function getOffsetTopSuggested()
  {
    return $this->offsetTopSuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CropPropertiesSuggestionState::class, 'Google_Service_Docs_CropPropertiesSuggestionState');
