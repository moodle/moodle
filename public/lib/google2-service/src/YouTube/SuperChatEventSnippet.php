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

namespace Google\Service\YouTube;

class SuperChatEventSnippet extends \Google\Model
{
  /**
   * The purchase amount, in micros of the purchase currency. e.g., 1 is
   * represented as 1000000.
   *
   * @var string
   */
  public $amountMicros;
  /**
   * Channel id where the event occurred.
   *
   * @var string
   */
  public $channelId;
  /**
   * The text contents of the comment left by the user.
   *
   * @var string
   */
  public $commentText;
  /**
   * The date and time when the event occurred.
   *
   * @var string
   */
  public $createdAt;
  /**
   * The currency in which the purchase was made. ISO 4217.
   *
   * @var string
   */
  public $currency;
  /**
   * A rendered string that displays the purchase amount and currency (e.g.,
   * "$1.00"). The string is rendered for the given language.
   *
   * @var string
   */
  public $displayString;
  /**
   * True if this event is a Super Sticker event.
   *
   * @var bool
   */
  public $isSuperStickerEvent;
  /**
   * The tier for the paid message, which is based on the amount of money spent
   * to purchase the message.
   *
   * @var string
   */
  public $messageType;
  protected $superStickerMetadataType = SuperStickerMetadata::class;
  protected $superStickerMetadataDataType = '';
  protected $supporterDetailsType = ChannelProfileDetails::class;
  protected $supporterDetailsDataType = '';

  /**
   * The purchase amount, in micros of the purchase currency. e.g., 1 is
   * represented as 1000000.
   *
   * @param string $amountMicros
   */
  public function setAmountMicros($amountMicros)
  {
    $this->amountMicros = $amountMicros;
  }
  /**
   * @return string
   */
  public function getAmountMicros()
  {
    return $this->amountMicros;
  }
  /**
   * Channel id where the event occurred.
   *
   * @param string $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }
  /**
   * @return string
   */
  public function getChannelId()
  {
    return $this->channelId;
  }
  /**
   * The text contents of the comment left by the user.
   *
   * @param string $commentText
   */
  public function setCommentText($commentText)
  {
    $this->commentText = $commentText;
  }
  /**
   * @return string
   */
  public function getCommentText()
  {
    return $this->commentText;
  }
  /**
   * The date and time when the event occurred.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * The currency in which the purchase was made. ISO 4217.
   *
   * @param string $currency
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  /**
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }
  /**
   * A rendered string that displays the purchase amount and currency (e.g.,
   * "$1.00"). The string is rendered for the given language.
   *
   * @param string $displayString
   */
  public function setDisplayString($displayString)
  {
    $this->displayString = $displayString;
  }
  /**
   * @return string
   */
  public function getDisplayString()
  {
    return $this->displayString;
  }
  /**
   * True if this event is a Super Sticker event.
   *
   * @param bool $isSuperStickerEvent
   */
  public function setIsSuperStickerEvent($isSuperStickerEvent)
  {
    $this->isSuperStickerEvent = $isSuperStickerEvent;
  }
  /**
   * @return bool
   */
  public function getIsSuperStickerEvent()
  {
    return $this->isSuperStickerEvent;
  }
  /**
   * The tier for the paid message, which is based on the amount of money spent
   * to purchase the message.
   *
   * @param string $messageType
   */
  public function setMessageType($messageType)
  {
    $this->messageType = $messageType;
  }
  /**
   * @return string
   */
  public function getMessageType()
  {
    return $this->messageType;
  }
  /**
   * If this event is a Super Sticker event, this field will contain metadata
   * about the Super Sticker.
   *
   * @param SuperStickerMetadata $superStickerMetadata
   */
  public function setSuperStickerMetadata(SuperStickerMetadata $superStickerMetadata)
  {
    $this->superStickerMetadata = $superStickerMetadata;
  }
  /**
   * @return SuperStickerMetadata
   */
  public function getSuperStickerMetadata()
  {
    return $this->superStickerMetadata;
  }
  /**
   * Details about the supporter.
   *
   * @param ChannelProfileDetails $supporterDetails
   */
  public function setSupporterDetails(ChannelProfileDetails $supporterDetails)
  {
    $this->supporterDetails = $supporterDetails;
  }
  /**
   * @return ChannelProfileDetails
   */
  public function getSupporterDetails()
  {
    return $this->supporterDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SuperChatEventSnippet::class, 'Google_Service_YouTube_SuperChatEventSnippet');
