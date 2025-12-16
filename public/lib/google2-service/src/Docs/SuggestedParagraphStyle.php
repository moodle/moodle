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

class SuggestedParagraphStyle extends \Google\Model
{
  protected $paragraphStyleType = ParagraphStyle::class;
  protected $paragraphStyleDataType = '';
  protected $paragraphStyleSuggestionStateType = ParagraphStyleSuggestionState::class;
  protected $paragraphStyleSuggestionStateDataType = '';

  /**
   * A ParagraphStyle that only includes the changes made in this suggestion.
   * This can be used along with the paragraph_style_suggestion_state to see
   * which fields have changed and their new values.
   *
   * @param ParagraphStyle $paragraphStyle
   */
  public function setParagraphStyle(ParagraphStyle $paragraphStyle)
  {
    $this->paragraphStyle = $paragraphStyle;
  }
  /**
   * @return ParagraphStyle
   */
  public function getParagraphStyle()
  {
    return $this->paragraphStyle;
  }
  /**
   * A mask that indicates which of the fields on the base ParagraphStyle have
   * been changed in this suggestion.
   *
   * @param ParagraphStyleSuggestionState $paragraphStyleSuggestionState
   */
  public function setParagraphStyleSuggestionState(ParagraphStyleSuggestionState $paragraphStyleSuggestionState)
  {
    $this->paragraphStyleSuggestionState = $paragraphStyleSuggestionState;
  }
  /**
   * @return ParagraphStyleSuggestionState
   */
  public function getParagraphStyleSuggestionState()
  {
    return $this->paragraphStyleSuggestionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SuggestedParagraphStyle::class, 'Google_Service_Docs_SuggestedParagraphStyle');
