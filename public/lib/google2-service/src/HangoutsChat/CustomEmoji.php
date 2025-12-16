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

class CustomEmoji extends \Google\Model
{
  /**
   * Optional. Immutable. User-provided name for the custom emoji, which is
   * unique within the organization. Required when the custom emoji is created,
   * output only otherwise. Emoji names must start and end with colons, must be
   * lowercase and can only contain alphanumeric characters, hyphens, and
   * underscores. Hyphens and underscores should be used to separate words and
   * cannot be used consecutively. Example: `:valid-emoji-name:`
   *
   * @var string
   */
  public $emojiName;
  /**
   * Identifier. The resource name of the custom emoji, assigned by the server.
   * Format: `customEmojis/{customEmoji}`
   *
   * @var string
   */
  public $name;
  protected $payloadType = CustomEmojiPayload::class;
  protected $payloadDataType = '';
  /**
   * Output only. A temporary image URL for the custom emoji, valid for at least
   * 10 minutes. Note that this is not populated in the response when the custom
   * emoji is created.
   *
   * @var string
   */
  public $temporaryImageUri;
  /**
   * Output only. Unique key for the custom emoji resource.
   *
   * @var string
   */
  public $uid;

  /**
   * Optional. Immutable. User-provided name for the custom emoji, which is
   * unique within the organization. Required when the custom emoji is created,
   * output only otherwise. Emoji names must start and end with colons, must be
   * lowercase and can only contain alphanumeric characters, hyphens, and
   * underscores. Hyphens and underscores should be used to separate words and
   * cannot be used consecutively. Example: `:valid-emoji-name:`
   *
   * @param string $emojiName
   */
  public function setEmojiName($emojiName)
  {
    $this->emojiName = $emojiName;
  }
  /**
   * @return string
   */
  public function getEmojiName()
  {
    return $this->emojiName;
  }
  /**
   * Identifier. The resource name of the custom emoji, assigned by the server.
   * Format: `customEmojis/{customEmoji}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. Input only. Payload data. Required when the custom emoji is
   * created.
   *
   * @param CustomEmojiPayload $payload
   */
  public function setPayload(CustomEmojiPayload $payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return CustomEmojiPayload
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Output only. A temporary image URL for the custom emoji, valid for at least
   * 10 minutes. Note that this is not populated in the response when the custom
   * emoji is created.
   *
   * @param string $temporaryImageUri
   */
  public function setTemporaryImageUri($temporaryImageUri)
  {
    $this->temporaryImageUri = $temporaryImageUri;
  }
  /**
   * @return string
   */
  public function getTemporaryImageUri()
  {
    return $this->temporaryImageUri;
  }
  /**
   * Output only. Unique key for the custom emoji resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomEmoji::class, 'Google_Service_HangoutsChat_CustomEmoji');
