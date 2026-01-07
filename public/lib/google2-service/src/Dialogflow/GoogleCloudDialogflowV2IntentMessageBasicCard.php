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

class GoogleCloudDialogflowV2IntentMessageBasicCard extends \Google\Collection
{
  protected $collection_key = 'buttons';
  protected $buttonsType = GoogleCloudDialogflowV2IntentMessageBasicCardButton::class;
  protected $buttonsDataType = 'array';
  /**
   * Required, unless image is present. The body text of the card.
   *
   * @var string
   */
  public $formattedText;
  protected $imageType = GoogleCloudDialogflowV2IntentMessageImage::class;
  protected $imageDataType = '';
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
   * @param GoogleCloudDialogflowV2IntentMessageBasicCardButton[] $buttons
   */
  public function setButtons($buttons)
  {
    $this->buttons = $buttons;
  }
  /**
   * @return GoogleCloudDialogflowV2IntentMessageBasicCardButton[]
   */
  public function getButtons()
  {
    return $this->buttons;
  }
  /**
   * Required, unless image is present. The body text of the card.
   *
   * @param string $formattedText
   */
  public function setFormattedText($formattedText)
  {
    $this->formattedText = $formattedText;
  }
  /**
   * @return string
   */
  public function getFormattedText()
  {
    return $this->formattedText;
  }
  /**
   * Optional. The image for the card.
   *
   * @param GoogleCloudDialogflowV2IntentMessageImage $image
   */
  public function setImage(GoogleCloudDialogflowV2IntentMessageImage $image)
  {
    $this->image = $image;
  }
  /**
   * @return GoogleCloudDialogflowV2IntentMessageImage
   */
  public function getImage()
  {
    return $this->image;
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
class_alias(GoogleCloudDialogflowV2IntentMessageBasicCard::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2IntentMessageBasicCard');
