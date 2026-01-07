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

class GoogleCloudDialogflowV2beta1IntentMessageCard extends \Google\Collection
{
  protected $collection_key = 'buttons';
  protected $buttonsType = GoogleCloudDialogflowV2beta1IntentMessageCardButton::class;
  protected $buttonsDataType = 'array';
  /**
   * Optional. The public URI to an image file for the card.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Optional. The subtitle of the card.
   *
   * @var string
   */
  public $subtitle;
  /**
   * Optional. The title of the card.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. The collection of card buttons.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageCardButton[] $buttons
   */
  public function setButtons($buttons)
  {
    $this->buttons = $buttons;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageCardButton[]
   */
  public function getButtons()
  {
    return $this->buttons;
  }
  /**
   * Optional. The public URI to an image file for the card.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * Optional. The subtitle of the card.
   *
   * @param string $subtitle
   */
  public function setSubtitle($subtitle)
  {
    $this->subtitle = $subtitle;
  }
  /**
   * @return string
   */
  public function getSubtitle()
  {
    return $this->subtitle;
  }
  /**
   * Optional. The title of the card.
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
class_alias(GoogleCloudDialogflowV2beta1IntentMessageCard::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageCard');
