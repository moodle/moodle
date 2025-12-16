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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent extends \Google\Collection
{
  protected $collection_key = 'suggestions';
  /**
   * Optional. Description of the card (at most 2000 bytes). At least one of the
   * title, description or media must be set.
   *
   * @var string
   */
  public $description;
  protected $mediaType = GoogleCloudDialogflowV2beta1IntentMessageRbmCardContentRbmMedia::class;
  protected $mediaDataType = '';
  protected $suggestionsType = GoogleCloudDialogflowV2beta1IntentMessageRbmSuggestion::class;
  protected $suggestionsDataType = 'array';
  /**
   * Optional. Title of the card (at most 200 bytes). At least one of the title,
   * description or media must be set.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. Description of the card (at most 2000 bytes). At least one of the
   * title, description or media must be set.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. However at least one of the title, description or media must be
   * set. Media (image, GIF or a video) to include in the card.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageRbmCardContentRbmMedia $media
   */
  public function setMedia(GoogleCloudDialogflowV2beta1IntentMessageRbmCardContentRbmMedia $media)
  {
    $this->media = $media;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageRbmCardContentRbmMedia
   */
  public function getMedia()
  {
    return $this->media;
  }
  /**
   * Optional. List of suggestions to include in the card.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageRbmSuggestion[] $suggestions
   */
  public function setSuggestions($suggestions)
  {
    $this->suggestions = $suggestions;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageRbmSuggestion[]
   */
  public function getSuggestions()
  {
    return $this->suggestions;
  }
  /**
   * Optional. Title of the card (at most 200 bytes). At least one of the title,
   * description or media must be set.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent');
