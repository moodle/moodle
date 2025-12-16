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

class DeprecatedEvent extends \Google\Model
{
  /**
   * Default value. Unspecified.
   */
  public const DIALOG_EVENT_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * A user opens a dialog.
   */
  public const DIALOG_EVENT_TYPE_REQUEST_DIALOG = 'REQUEST_DIALOG';
  /**
   * A user clicks an interactive element of a dialog. For example, a user fills
   * out information in a dialog and clicks a button to submit the information.
   */
  public const DIALOG_EVENT_TYPE_SUBMIT_DIALOG = 'SUBMIT_DIALOG';
  /**
   * A user closes a dialog without submitting information. The Chat app only
   * receives this interaction event when users click the close icon in the top
   * right corner of the dialog. When the user closes the dialog by other means
   * (such as refreshing the browser, clicking outside the dialog box, or
   * pressing the escape key), no event is sent.
   */
  public const DIALOG_EVENT_TYPE_CANCEL_DIALOG = 'CANCEL_DIALOG';
  /**
   * Default value for the enum. DO NOT USE.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * A user sends the Chat app a message, or invokes the Chat app in a space.
   * Examples of message events include: * Any message in a direct message (DM)
   * space with the Chat app. * A message in a multi-person space where a person
   * @mentions the Chat app, or uses one of its [slash
   * commands](https://developers.google.com/workspace/chat/commands#types). *
   * If you've configured link previews for your Chat app, a user posts a
   * message that contains a link that matches the configured URL pattern.
   */
  public const TYPE_MESSAGE = 'MESSAGE';
  /**
   * A user adds the Chat app to a space, or a Google Workspace administrator
   * installs the Chat app in direct message spaces for users in their
   * organization. Chat apps typically respond to this interaction event by
   * posting a welcome message in the space. When administrators install Chat
   * apps, the `space.adminInstalled` field is set to `true` and users can't
   * uninstall them. To learn about Chat apps installed by administrators, see
   * Google Workspace Admin Help's documentation, [Install Marketplace apps in
   * your domain](https://support.google.com/a/answer/172482).
   */
  public const TYPE_ADDED_TO_SPACE = 'ADDED_TO_SPACE';
  /**
   * A user removes the Chat app from a space, or a Google Workspace
   * administrator uninstalls the Chat app for a user in their organization.
   * Chat apps can't respond with messages to this event, because they have
   * already been removed. When administrators uninstall Chat apps, the
   * `space.adminInstalled` field is set to `false`. If a user installed the
   * Chat app before the administrator, the Chat app remains installed for the
   * user and the Chat app doesn't receive a `REMOVED_FROM_SPACE` interaction
   * event.
   */
  public const TYPE_REMOVED_FROM_SPACE = 'REMOVED_FROM_SPACE';
  /**
   * A user clicks an interactive element of a card or dialog from a Chat app,
   * such as a button. To receive an interaction event, the button must trigger
   * another interaction with the Chat app. For example, a Chat app doesn't
   * receive a `CARD_CLICKED` interaction event if a user clicks a button that
   * opens a link to a website, but receives interaction events in the following
   * examples: * The user clicks a `Send feedback` button on a card, which opens
   * a dialog for the user to input information. * The user clicks a `Submit`
   * button after inputting information into a card or dialog. If a user clicks
   * a button to open, submit, or cancel a dialog, the `CARD_CLICKED`
   * interaction event's `isDialogEvent` field is set to `true` and includes a [
   * `DialogEventType`](https://developers.google.com/workspace/chat/api/referen
   * ce/rest/v1/DialogEventType).
   */
  public const TYPE_CARD_CLICKED = 'CARD_CLICKED';
  /**
   * A user updates a widget in a card message or dialog. This event is
   * triggered when a user interacts with a widget that has an associated
   * action.
   */
  public const TYPE_WIDGET_UPDATED = 'WIDGET_UPDATED';
  /**
   * A user uses a Chat app
   * [command](https://developers.google.com/workspace/chat/commands#types),
   * including slash commands and quick commands.
   */
  public const TYPE_APP_COMMAND = 'APP_COMMAND';
  protected $actionType = FormAction::class;
  protected $actionDataType = '';
  protected $appCommandMetadataType = AppCommandMetadata::class;
  protected $appCommandMetadataDataType = '';
  protected $commonType = CommonEventObject::class;
  protected $commonDataType = '';
  /**
   * This URL is populated for `MESSAGE`, `ADDED_TO_SPACE`, and `APP_COMMAND`
   * interaction events. After completing an authorization or configuration flow
   * outside of Google Chat, users must be redirected to this URL to signal to
   * Google Chat that the authorization or configuration flow was successful.
   * For more information, see [Connect a Chat app with other services and
   * tools](https://developers.google.com/workspace/chat/connect-web-services-
   * tools).
   *
   * @var string
   */
  public $configCompleteRedirectUrl;
  /**
   * The type of [dialog](https://developers.google.com/workspace/chat/dialogs)
   * interaction event received.
   *
   * @var string
   */
  public $dialogEventType;
  /**
   * The timestamp indicating when the interaction event occurred.
   *
   * @var string
   */
  public $eventTime;
  /**
   * For `CARD_CLICKED` and `MESSAGE` interaction events, whether the user is
   * interacting with or about to interact with a
   * [dialog](https://developers.google.com/workspace/chat/dialogs).
   *
   * @var bool
   */
  public $isDialogEvent;
  protected $messageType = Message::class;
  protected $messageDataType = '';
  protected $spaceType = Space::class;
  protected $spaceDataType = '';
  protected $threadType = Thread::class;
  protected $threadDataType = '';
  /**
   * The Chat app-defined key for the thread related to the interaction event.
   * See [`spaces.messages.thread.threadKey`](/chat/api/reference/rest/v1/spaces
   * .messages#Thread.FIELDS.thread_key) for more information.
   *
   * @var string
   */
  public $threadKey;
  /**
   * A secret value that legacy Chat apps can use to verify if a request is from
   * Google. Google randomly generates the token, and its value remains static.
   * You can obtain, revoke, or regenerate the token from the [Chat API
   * configuration page](https://console.cloud.google.com/apis/api/chat.googleap
   * is.com/hangouts-chat) in the Google Cloud Console. Modern Chat apps don't
   * use this field. It is absent from API responses and the [Chat API
   * configuration page](https://console.cloud.google.com/apis/api/chat.googleap
   * is.com/hangouts-chat).
   *
   * @var string
   */
  public $token;
  /**
   * The [type](/workspace/chat/api/reference/rest/v1/EventType) of user
   * interaction with the Chat app, such as `MESSAGE` or `ADDED_TO_SPACE`.
   *
   * @var string
   */
  public $type;
  protected $userType = User::class;
  protected $userDataType = '';

  /**
   * For `CARD_CLICKED` interaction events, the form action data associated when
   * a user clicks a card or dialog. To learn more, see [Read form data input by
   * users on cards](https://developers.google.com/workspace/chat/read-form-
   * data).
   *
   * @param FormAction $action
   */
  public function setAction(FormAction $action)
  {
    $this->action = $action;
  }
  /**
   * @return FormAction
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Metadata about a Chat app command.
   *
   * @param AppCommandMetadata $appCommandMetadata
   */
  public function setAppCommandMetadata(AppCommandMetadata $appCommandMetadata)
  {
    $this->appCommandMetadata = $appCommandMetadata;
  }
  /**
   * @return AppCommandMetadata
   */
  public function getAppCommandMetadata()
  {
    return $this->appCommandMetadata;
  }
  /**
   * Represents information about the user's client, such as locale, host app,
   * and platform. For Chat apps, `CommonEventObject` includes information
   * submitted by users interacting with
   * [dialogs](https://developers.google.com/workspace/chat/dialogs), like data
   * entered on a card.
   *
   * @param CommonEventObject $common
   */
  public function setCommon(CommonEventObject $common)
  {
    $this->common = $common;
  }
  /**
   * @return CommonEventObject
   */
  public function getCommon()
  {
    return $this->common;
  }
  /**
   * This URL is populated for `MESSAGE`, `ADDED_TO_SPACE`, and `APP_COMMAND`
   * interaction events. After completing an authorization or configuration flow
   * outside of Google Chat, users must be redirected to this URL to signal to
   * Google Chat that the authorization or configuration flow was successful.
   * For more information, see [Connect a Chat app with other services and
   * tools](https://developers.google.com/workspace/chat/connect-web-services-
   * tools).
   *
   * @param string $configCompleteRedirectUrl
   */
  public function setConfigCompleteRedirectUrl($configCompleteRedirectUrl)
  {
    $this->configCompleteRedirectUrl = $configCompleteRedirectUrl;
  }
  /**
   * @return string
   */
  public function getConfigCompleteRedirectUrl()
  {
    return $this->configCompleteRedirectUrl;
  }
  /**
   * The type of [dialog](https://developers.google.com/workspace/chat/dialogs)
   * interaction event received.
   *
   * Accepted values: TYPE_UNSPECIFIED, REQUEST_DIALOG, SUBMIT_DIALOG,
   * CANCEL_DIALOG
   *
   * @param self::DIALOG_EVENT_TYPE_* $dialogEventType
   */
  public function setDialogEventType($dialogEventType)
  {
    $this->dialogEventType = $dialogEventType;
  }
  /**
   * @return self::DIALOG_EVENT_TYPE_*
   */
  public function getDialogEventType()
  {
    return $this->dialogEventType;
  }
  /**
   * The timestamp indicating when the interaction event occurred.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * For `CARD_CLICKED` and `MESSAGE` interaction events, whether the user is
   * interacting with or about to interact with a
   * [dialog](https://developers.google.com/workspace/chat/dialogs).
   *
   * @param bool $isDialogEvent
   */
  public function setIsDialogEvent($isDialogEvent)
  {
    $this->isDialogEvent = $isDialogEvent;
  }
  /**
   * @return bool
   */
  public function getIsDialogEvent()
  {
    return $this->isDialogEvent;
  }
  /**
   * For `ADDED_TO_SPACE`, `CARD_CLICKED`, and `MESSAGE` interaction events, the
   * message that triggered the interaction event, if applicable.
   *
   * @param Message $message
   */
  public function setMessage(Message $message)
  {
    $this->message = $message;
  }
  /**
   * @return Message
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The space in which the user interacted with the Chat app.
   *
   * @param Space $space
   */
  public function setSpace(Space $space)
  {
    $this->space = $space;
  }
  /**
   * @return Space
   */
  public function getSpace()
  {
    return $this->space;
  }
  /**
   * The thread in which the user interacted with the Chat app. This could be in
   * a new thread created by a newly sent message. This field is populated if
   * the interaction event is associated with a specific message or thread.
   *
   * @param Thread $thread
   */
  public function setThread(Thread $thread)
  {
    $this->thread = $thread;
  }
  /**
   * @return Thread
   */
  public function getThread()
  {
    return $this->thread;
  }
  /**
   * The Chat app-defined key for the thread related to the interaction event.
   * See [`spaces.messages.thread.threadKey`](/chat/api/reference/rest/v1/spaces
   * .messages#Thread.FIELDS.thread_key) for more information.
   *
   * @param string $threadKey
   */
  public function setThreadKey($threadKey)
  {
    $this->threadKey = $threadKey;
  }
  /**
   * @return string
   */
  public function getThreadKey()
  {
    return $this->threadKey;
  }
  /**
   * A secret value that legacy Chat apps can use to verify if a request is from
   * Google. Google randomly generates the token, and its value remains static.
   * You can obtain, revoke, or regenerate the token from the [Chat API
   * configuration page](https://console.cloud.google.com/apis/api/chat.googleap
   * is.com/hangouts-chat) in the Google Cloud Console. Modern Chat apps don't
   * use this field. It is absent from API responses and the [Chat API
   * configuration page](https://console.cloud.google.com/apis/api/chat.googleap
   * is.com/hangouts-chat).
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * The [type](/workspace/chat/api/reference/rest/v1/EventType) of user
   * interaction with the Chat app, such as `MESSAGE` or `ADDED_TO_SPACE`.
   *
   * Accepted values: UNSPECIFIED, MESSAGE, ADDED_TO_SPACE, REMOVED_FROM_SPACE,
   * CARD_CLICKED, WIDGET_UPDATED, APP_COMMAND
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
   * The user that interacted with the Chat app.
   *
   * @param User $user
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }
  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeprecatedEvent::class, 'Google_Service_HangoutsChat_DeprecatedEvent');
