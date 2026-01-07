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

namespace Google\Service\CloudSearch;

class Content extends \Google\Collection
{
  protected $collection_key = 'actions';
  protected $actionsType = Action::class;
  protected $actionsDataType = 'array';
  protected $descriptionType = SafeHtmlProto::class;
  protected $descriptionDataType = '';
  protected $subtitleType = BackgroundColoredText::class;
  protected $subtitleDataType = '';
  protected $titleType = BackgroundColoredText::class;
  protected $titleDataType = '';

  /**
   * [Optional] Actions for this card.
   *
   * @param Action[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return Action[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * [Optional] Description of the card.
   *
   * @param SafeHtmlProto $description
   */
  public function setDescription(SafeHtmlProto $description)
  {
    $this->description = $description;
  }
  /**
   * @return SafeHtmlProto
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * [Optional] Subtitle of the card.
   *
   * @param BackgroundColoredText $subtitle
   */
  public function setSubtitle(BackgroundColoredText $subtitle)
  {
    $this->subtitle = $subtitle;
  }
  /**
   * @return BackgroundColoredText
   */
  public function getSubtitle()
  {
    return $this->subtitle;
  }
  /**
   * [Optional] Title of the card.
   *
   * @param BackgroundColoredText $title
   */
  public function setTitle(BackgroundColoredText $title)
  {
    $this->title = $title;
  }
  /**
   * @return BackgroundColoredText
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Content::class, 'Google_Service_CloudSearch_Content');
