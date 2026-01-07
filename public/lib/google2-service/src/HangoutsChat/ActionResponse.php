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

class ActionResponse extends \Google\Model
{
  /**
   * Default type that's handled as `NEW_MESSAGE`.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Post as a new message in the topic.
   */
  public const TYPE_NEW_MESSAGE = 'NEW_MESSAGE';
  /**
   * Update the Chat app's message. This is only permitted on a `CARD_CLICKED`
   * event where the message sender type is `BOT`.
   */
  public const TYPE_UPDATE_MESSAGE = 'UPDATE_MESSAGE';
  /**
   * Update the cards on a user's message. This is only permitted as a response
   * to a `MESSAGE` event with a matched url, or a `CARD_CLICKED` event where
   * the message sender type is `HUMAN`. Text is ignored.
   */
  public const TYPE_UPDATE_USER_MESSAGE_CARDS = 'UPDATE_USER_MESSAGE_CARDS';
  /**
   * Privately ask the user for additional authentication or configuration.
   */
  public const TYPE_REQUEST_CONFIG = 'REQUEST_CONFIG';
  /**
   * Presents a [dialog](https://developers.google.com/workspace/chat/dialogs).
   */
  public const TYPE_DIALOG = 'DIALOG';
  /**
   * Widget text autocomplete options query.
   */
  public const TYPE_UPDATE_WIDGET = 'UPDATE_WIDGET';
  protected $dialogActionType = DialogAction::class;
  protected $dialogActionDataType = '';
  /**
   * Input only. The type of Chat app response.
   *
   * @var string
   */
  public $type;
  protected $updatedWidgetType = UpdatedWidget::class;
  protected $updatedWidgetDataType = '';
  /**
   * Input only. URL for users to authenticate or configure. (Only for
   * `REQUEST_CONFIG` response types.)
   *
   * @var string
   */
  public $url;

  /**
   * Input only. A response to an interaction event related to a
   * [dialog](https://developers.google.com/workspace/chat/dialogs). Must be
   * accompanied by `ResponseType.Dialog`.
   *
   * @param DialogAction $dialogAction
   */
  public function setDialogAction(DialogAction $dialogAction)
  {
    $this->dialogAction = $dialogAction;
  }
  /**
   * @return DialogAction
   */
  public function getDialogAction()
  {
    return $this->dialogAction;
  }
  /**
   * Input only. The type of Chat app response.
   *
   * Accepted values: TYPE_UNSPECIFIED, NEW_MESSAGE, UPDATE_MESSAGE,
   * UPDATE_USER_MESSAGE_CARDS, REQUEST_CONFIG, DIALOG, UPDATE_WIDGET
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Input only. The response of the updated widget.
   *
   * @param UpdatedWidget $updatedWidget
   */
  public function setUpdatedWidget(UpdatedWidget $updatedWidget)
  {
    $this->updatedWidget = $updatedWidget;
  }
  /**
   * @return UpdatedWidget
   */
  public function getUpdatedWidget()
  {
    return $this->updatedWidget;
  }
  /**
   * Input only. URL for users to authenticate or configure. (Only for
   * `REQUEST_CONFIG` response types.)
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActionResponse::class, 'Google_Service_HangoutsChat_ActionResponse');
