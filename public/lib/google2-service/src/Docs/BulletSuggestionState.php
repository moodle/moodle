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

class BulletSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to the list_id.
   *
   * @var bool
   */
  public $listIdSuggested;
  /**
   * Indicates if there was a suggested change to the nesting_level.
   *
   * @var bool
   */
  public $nestingLevelSuggested;
  protected $textStyleSuggestionStateType = TextStyleSuggestionState::class;
  protected $textStyleSuggestionStateDataType = '';

  /**
   * Indicates if there was a suggested change to the list_id.
   *
   * @param bool $listIdSuggested
   */
  public function setListIdSuggested($listIdSuggested)
  {
    $this->listIdSuggested = $listIdSuggested;
  }
  /**
   * @return bool
   */
  public function getListIdSuggested()
  {
    return $this->listIdSuggested;
  }
  /**
   * Indicates if there was a suggested change to the nesting_level.
   *
   * @param bool $nestingLevelSuggested
   */
  public function setNestingLevelSuggested($nestingLevelSuggested)
  {
    $this->nestingLevelSuggested = $nestingLevelSuggested;
  }
  /**
   * @return bool
   */
  public function getNestingLevelSuggested()
  {
    return $this->nestingLevelSuggested;
  }
  /**
   * A mask that indicates which of the fields in text style have been changed
   * in this suggestion.
   *
   * @param TextStyleSuggestionState $textStyleSuggestionState
   */
  public function setTextStyleSuggestionState(TextStyleSuggestionState $textStyleSuggestionState)
  {
    $this->textStyleSuggestionState = $textStyleSuggestionState;
  }
  /**
   * @return TextStyleSuggestionState
   */
  public function getTextStyleSuggestionState()
  {
    return $this->textStyleSuggestionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulletSuggestionState::class, 'Google_Service_Docs_BulletSuggestionState');
