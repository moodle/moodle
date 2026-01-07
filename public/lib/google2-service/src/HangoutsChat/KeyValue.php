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

class KeyValue extends \Google\Model
{
  public const ICON_ICON_UNSPECIFIED = 'ICON_UNSPECIFIED';
  public const ICON_AIRPLANE = 'AIRPLANE';
  public const ICON_BOOKMARK = 'BOOKMARK';
  public const ICON_BUS = 'BUS';
  public const ICON_CAR = 'CAR';
  public const ICON_CLOCK = 'CLOCK';
  public const ICON_CONFIRMATION_NUMBER_ICON = 'CONFIRMATION_NUMBER_ICON';
  public const ICON_DOLLAR = 'DOLLAR';
  public const ICON_DESCRIPTION = 'DESCRIPTION';
  public const ICON_EMAIL = 'EMAIL';
  public const ICON_EVENT_PERFORMER = 'EVENT_PERFORMER';
  public const ICON_EVENT_SEAT = 'EVENT_SEAT';
  public const ICON_FLIGHT_ARRIVAL = 'FLIGHT_ARRIVAL';
  public const ICON_FLIGHT_DEPARTURE = 'FLIGHT_DEPARTURE';
  public const ICON_HOTEL = 'HOTEL';
  public const ICON_HOTEL_ROOM_TYPE = 'HOTEL_ROOM_TYPE';
  public const ICON_INVITE = 'INVITE';
  public const ICON_MAP_PIN = 'MAP_PIN';
  public const ICON_MEMBERSHIP = 'MEMBERSHIP';
  public const ICON_MULTIPLE_PEOPLE = 'MULTIPLE_PEOPLE';
  public const ICON_OFFER = 'OFFER';
  public const ICON_PERSON = 'PERSON';
  public const ICON_PHONE = 'PHONE';
  public const ICON_RESTAURANT_ICON = 'RESTAURANT_ICON';
  public const ICON_SHOPPING_CART = 'SHOPPING_CART';
  public const ICON_STAR = 'STAR';
  public const ICON_STORE = 'STORE';
  public const ICON_TICKET = 'TICKET';
  public const ICON_TRAIN = 'TRAIN';
  public const ICON_VIDEO_CAMERA = 'VIDEO_CAMERA';
  public const ICON_VIDEO_PLAY = 'VIDEO_PLAY';
  /**
   * The text of the bottom label. Formatted text supported. For more
   * information about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace Add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @var string
   */
  public $bottomLabel;
  protected $buttonType = Button::class;
  protected $buttonDataType = '';
  /**
   * The text of the content. Formatted text supported and always required. For
   * more information about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace Add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @var string
   */
  public $content;
  /**
   * If the content should be multiline.
   *
   * @var bool
   */
  public $contentMultiline;
  /**
   * An enum value that's replaced by the Chat API with the corresponding icon
   * image.
   *
   * @var string
   */
  public $icon;
  /**
   * The icon specified by a URL.
   *
   * @var string
   */
  public $iconUrl;
  protected $onClickType = OnClick::class;
  protected $onClickDataType = '';
  /**
   * The text of the top label. Formatted text supported. For more information
   * about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace Add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @var string
   */
  public $topLabel;

  /**
   * The text of the bottom label. Formatted text supported. For more
   * information about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace Add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @param string $bottomLabel
   */
  public function setBottomLabel($bottomLabel)
  {
    $this->bottomLabel = $bottomLabel;
  }
  /**
   * @return string
   */
  public function getBottomLabel()
  {
    return $this->bottomLabel;
  }
  /**
   * A button that can be clicked to trigger an action.
   *
   * @param Button $button
   */
  public function setButton(Button $button)
  {
    $this->button = $button;
  }
  /**
   * @return Button
   */
  public function getButton()
  {
    return $this->button;
  }
  /**
   * The text of the content. Formatted text supported and always required. For
   * more information about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace Add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * If the content should be multiline.
   *
   * @param bool $contentMultiline
   */
  public function setContentMultiline($contentMultiline)
  {
    $this->contentMultiline = $contentMultiline;
  }
  /**
   * @return bool
   */
  public function getContentMultiline()
  {
    return $this->contentMultiline;
  }
  /**
   * An enum value that's replaced by the Chat API with the corresponding icon
   * image.
   *
   * Accepted values: ICON_UNSPECIFIED, AIRPLANE, BOOKMARK, BUS, CAR, CLOCK,
   * CONFIRMATION_NUMBER_ICON, DOLLAR, DESCRIPTION, EMAIL, EVENT_PERFORMER,
   * EVENT_SEAT, FLIGHT_ARRIVAL, FLIGHT_DEPARTURE, HOTEL, HOTEL_ROOM_TYPE,
   * INVITE, MAP_PIN, MEMBERSHIP, MULTIPLE_PEOPLE, OFFER, PERSON, PHONE,
   * RESTAURANT_ICON, SHOPPING_CART, STAR, STORE, TICKET, TRAIN, VIDEO_CAMERA,
   * VIDEO_PLAY
   *
   * @param self::ICON_* $icon
   */
  public function setIcon($icon)
  {
    $this->icon = $icon;
  }
  /**
   * @return self::ICON_*
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * The icon specified by a URL.
   *
   * @param string $iconUrl
   */
  public function setIconUrl($iconUrl)
  {
    $this->iconUrl = $iconUrl;
  }
  /**
   * @return string
   */
  public function getIconUrl()
  {
    return $this->iconUrl;
  }
  /**
   * The `onclick` action. Only the top label, bottom label, and content region
   * are clickable.
   *
   * @param OnClick $onClick
   */
  public function setOnClick(OnClick $onClick)
  {
    $this->onClick = $onClick;
  }
  /**
   * @return OnClick
   */
  public function getOnClick()
  {
    return $this->onClick;
  }
  /**
   * The text of the top label. Formatted text supported. For more information
   * about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace Add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @param string $topLabel
   */
  public function setTopLabel($topLabel)
  {
    $this->topLabel = $topLabel;
  }
  /**
   * @return string
   */
  public function getTopLabel()
  {
    return $this->topLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyValue::class, 'Google_Service_HangoutsChat_KeyValue');
