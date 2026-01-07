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

class SuggestedNamedStyles extends \Google\Model
{
  protected $namedStylesType = NamedStyles::class;
  protected $namedStylesDataType = '';
  protected $namedStylesSuggestionStateType = NamedStylesSuggestionState::class;
  protected $namedStylesSuggestionStateDataType = '';

  /**
   * A NamedStyles that only includes the changes made in this suggestion. This
   * can be used along with the named_styles_suggestion_state to see which
   * fields have changed and their new values.
   *
   * @param NamedStyles $namedStyles
   */
  public function setNamedStyles(NamedStyles $namedStyles)
  {
    $this->namedStyles = $namedStyles;
  }
  /**
   * @return NamedStyles
   */
  public function getNamedStyles()
  {
    return $this->namedStyles;
  }
  /**
   * A mask that indicates which of the fields on the base NamedStyles have been
   * changed in this suggestion.
   *
   * @param NamedStylesSuggestionState $namedStylesSuggestionState
   */
  public function setNamedStylesSuggestionState(NamedStylesSuggestionState $namedStylesSuggestionState)
  {
    $this->namedStylesSuggestionState = $namedStylesSuggestionState;
  }
  /**
   * @return NamedStylesSuggestionState
   */
  public function getNamedStylesSuggestionState()
  {
    return $this->namedStylesSuggestionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SuggestedNamedStyles::class, 'Google_Service_Docs_SuggestedNamedStyles');
