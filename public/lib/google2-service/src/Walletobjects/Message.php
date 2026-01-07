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

namespace Google\Service\Walletobjects;

class Message extends \Google\Model
{
  public const MESSAGE_TYPE_MESSAGE_TYPE_UNSPECIFIED = 'MESSAGE_TYPE_UNSPECIFIED';
  /**
   * Renders the message as text on the card details screen. This is the default
   * message type.
   */
  public const MESSAGE_TYPE_TEXT = 'TEXT';
  /**
   * Legacy alias for `TEXT`. Deprecated.
   *
   * @deprecated
   */
  public const MESSAGE_TYPE_text = 'text';
  /**
   * Note: This enum is currently not supported.
   */
  public const MESSAGE_TYPE_EXPIRATION_NOTIFICATION = 'EXPIRATION_NOTIFICATION';
  /**
   * Legacy alias for `EXPIRATION_NOTIFICATION`. Deprecated.
   *
   * @deprecated
   */
  public const MESSAGE_TYPE_expirationNotification = 'expirationNotification';
  /**
   * Renders the message as text on the card details screen and as an Android
   * notification.
   */
  public const MESSAGE_TYPE_TEXT_AND_NOTIFY = 'TEXT_AND_NOTIFY';
  /**
   * The message body.
   *
   * @var string
   */
  public $body;
  protected $displayIntervalType = TimeInterval::class;
  protected $displayIntervalDataType = '';
  /**
   * The message header.
   *
   * @var string
   */
  public $header;
  /**
   * The ID associated with a message. This field is here to enable ease of
   * management of messages. Notice ID values could possibly duplicate across
   * multiple messages in the same class/instance, and care must be taken to
   * select a reasonable ID for each message.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#walletObjectMessage"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $localizedBodyType = LocalizedString::class;
  protected $localizedBodyDataType = '';
  protected $localizedHeaderType = LocalizedString::class;
  protected $localizedHeaderDataType = '';
  /**
   * The message type.
   *
   * @var string
   */
  public $messageType;

  /**
   * The message body.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * The period of time that the message will be displayed to users. You can
   * define both a `startTime` and `endTime` for each message. A message is
   * displayed immediately after a Wallet Object is inserted unless a
   * `startTime` is set. The message will appear in a list of messages
   * indefinitely if `endTime` is not provided.
   *
   * @param TimeInterval $displayInterval
   */
  public function setDisplayInterval(TimeInterval $displayInterval)
  {
    $this->displayInterval = $displayInterval;
  }
  /**
   * @return TimeInterval
   */
  public function getDisplayInterval()
  {
    return $this->displayInterval;
  }
  /**
   * The message header.
   *
   * @param string $header
   */
  public function setHeader($header)
  {
    $this->header = $header;
  }
  /**
   * @return string
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * The ID associated with a message. This field is here to enable ease of
   * management of messages. Notice ID values could possibly duplicate across
   * multiple messages in the same class/instance, and care must be taken to
   * select a reasonable ID for each message.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#walletObjectMessage"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Translated strings for the message body.
   *
   * @param LocalizedString $localizedBody
   */
  public function setLocalizedBody(LocalizedString $localizedBody)
  {
    $this->localizedBody = $localizedBody;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedBody()
  {
    return $this->localizedBody;
  }
  /**
   * Translated strings for the message header.
   *
   * @param LocalizedString $localizedHeader
   */
  public function setLocalizedHeader(LocalizedString $localizedHeader)
  {
    $this->localizedHeader = $localizedHeader;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedHeader()
  {
    return $this->localizedHeader;
  }
  /**
   * The message type.
   *
   * Accepted values: MESSAGE_TYPE_UNSPECIFIED, TEXT, text,
   * EXPIRATION_NOTIFICATION, expirationNotification, TEXT_AND_NOTIFY
   *
   * @param self::MESSAGE_TYPE_* $messageType
   */
  public function setMessageType($messageType)
  {
    $this->messageType = $messageType;
  }
  /**
   * @return self::MESSAGE_TYPE_*
   */
  public function getMessageType()
  {
    return $this->messageType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_Walletobjects_Message');
