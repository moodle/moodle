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

class GoogleCloudDialogflowV2beta1ConversationEvent extends \Google\Model
{
  /**
   * Type not set.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * A new conversation has been opened. This is fired when a telephone call is
   * answered, or a conversation is created via the API.
   */
  public const TYPE_CONVERSATION_STARTED = 'CONVERSATION_STARTED';
  /**
   * An existing conversation has closed. This is fired when a telephone call is
   * terminated, or a conversation is closed via the API. The event is fired for
   * every CompleteConversation call, even if the conversation is already
   * closed.
   */
  public const TYPE_CONVERSATION_FINISHED = 'CONVERSATION_FINISHED';
  /**
   * An existing conversation has received notification from Dialogflow that
   * human intervention is required.
   */
  public const TYPE_HUMAN_INTERVENTION_NEEDED = 'HUMAN_INTERVENTION_NEEDED';
  /**
   * An existing conversation has received a new message, either from API or
   * telephony. It is configured in
   * ConversationProfile.new_message_event_notification_config
   */
  public const TYPE_NEW_MESSAGE = 'NEW_MESSAGE';
  /**
   * An existing conversation has received a new speech recognition result. This
   * is mainly for delivering intermediate transcripts. The notification is
   * configured in
   * ConversationProfile.new_recognition_event_notification_config.
   */
  public const TYPE_NEW_RECOGNITION_RESULT = 'NEW_RECOGNITION_RESULT';
  /**
   * Unrecoverable error during a telephone call. In general non-recoverable
   * errors only occur if something was misconfigured in the ConversationProfile
   * corresponding to the call. After a non-recoverable error, Dialogflow may
   * stop responding. We don't fire this event: * in an API call because we can
   * directly return the error, or, * when we can recover from an error.
   */
  public const TYPE_UNRECOVERABLE_ERROR = 'UNRECOVERABLE_ERROR';
  /**
   * Required. The unique identifier of the conversation this notification
   * refers to. Format: `projects//conversations/`.
   *
   * @var string
   */
  public $conversation;
  protected $errorStatusType = GoogleRpcStatus::class;
  protected $errorStatusDataType = '';
  protected $newMessagePayloadType = GoogleCloudDialogflowV2beta1Message::class;
  protected $newMessagePayloadDataType = '';
  protected $newRecognitionResultPayloadType = GoogleCloudDialogflowV2beta1StreamingRecognitionResult::class;
  protected $newRecognitionResultPayloadDataType = '';
  /**
   * Required. The type of the event that this notification refers to.
   *
   * @var string
   */
  public $type;

  /**
   * Required. The unique identifier of the conversation this notification
   * refers to. Format: `projects//conversations/`.
   *
   * @param string $conversation
   */
  public function setConversation($conversation)
  {
    $this->conversation = $conversation;
  }
  /**
   * @return string
   */
  public function getConversation()
  {
    return $this->conversation;
  }
  /**
   * Optional. More detailed information about an error. Only set for type
   * UNRECOVERABLE_ERROR_IN_PHONE_CALL.
   *
   * @param GoogleRpcStatus $errorStatus
   */
  public function setErrorStatus(GoogleRpcStatus $errorStatus)
  {
    $this->errorStatus = $errorStatus;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getErrorStatus()
  {
    return $this->errorStatus;
  }
  /**
   * Payload of NEW_MESSAGE event.
   *
   * @param GoogleCloudDialogflowV2beta1Message $newMessagePayload
   */
  public function setNewMessagePayload(GoogleCloudDialogflowV2beta1Message $newMessagePayload)
  {
    $this->newMessagePayload = $newMessagePayload;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1Message
   */
  public function getNewMessagePayload()
  {
    return $this->newMessagePayload;
  }
  /**
   * Payload of NEW_RECOGNITION_RESULT event.
   *
   * @param GoogleCloudDialogflowV2beta1StreamingRecognitionResult $newRecognitionResultPayload
   */
  public function setNewRecognitionResultPayload(GoogleCloudDialogflowV2beta1StreamingRecognitionResult $newRecognitionResultPayload)
  {
    $this->newRecognitionResultPayload = $newRecognitionResultPayload;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1StreamingRecognitionResult
   */
  public function getNewRecognitionResultPayload()
  {
    return $this->newRecognitionResultPayload;
  }
  /**
   * Required. The type of the event that this notification refers to.
   *
   * Accepted values: TYPE_UNSPECIFIED, CONVERSATION_STARTED,
   * CONVERSATION_FINISHED, HUMAN_INTERVENTION_NEEDED, NEW_MESSAGE,
   * NEW_RECOGNITION_RESULT, UNRECOVERABLE_ERROR
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1ConversationEvent::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1ConversationEvent');
