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

namespace Google\Service\HangoutsChat;

class UpdatedWidget extends \Google\Model
{
  protected $suggestionsType = SelectionItems::class;
  protected $suggestionsDataType = '';
  /**
   * The ID of the updated widget. The ID must match the one for the widget that
   * triggered the update request.
   *
   * @var string
   */
  public $widget;

  /**
   * List of widget autocomplete results
   *
   * @param SelectionItems $suggestions
   */
  public function setSuggestions(SelectionItems $suggestions)
  {
    $this->suggestions = $suggestions;
  }
  /**
   * @return SelectionItems
   */
  public function getSuggestions()
  {
    return $this->suggestions;
  }
  /**
   * The ID of the updated widget. The ID must match the one for the widget that
   * triggered the update request.
   *
   * @param string $widget
   */
  public function setWidget($widget)
  {
    $this->widget = $widget;
  }
  /**
   * @return string
   */
  public function getWidget()
  {
    return $this->widget;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdatedWidget::class, 'Google_Service_HangoutsChat_UpdatedWidget');
