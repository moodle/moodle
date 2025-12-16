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

class Annotation extends \Google\Model
{
  /**
   * Default value for the enum. Don't use.
   */
  public const TYPE_ANNOTATION_TYPE_UNSPECIFIED = 'ANNOTATION_TYPE_UNSPECIFIED';
  /**
   * A user is mentioned.
   */
  public const TYPE_USER_MENTION = 'USER_MENTION';
  /**
   * A slash command is invoked.
   */
  public const TYPE_SLASH_COMMAND = 'SLASH_COMMAND';
  /**
   * A rich link annotation.
   */
  public const TYPE_RICH_LINK = 'RICH_LINK';
  /**
   * A custom emoji annotation.
   */
  public const TYPE_CUSTOM_EMOJI = 'CUSTOM_EMOJI';
  protected $customEmojiMetadataType = CustomEmojiMetadata::class;
  protected $customEmojiMetadataDataType = '';
  /**
   * Length of the substring in the plain-text message body this annotation
   * corresponds to. If not present, indicates a length of 0.
   *
   * @var int
   */
  public $length;
  protected $richLinkMetadataType = RichLinkMetadata::class;
  protected $richLinkMetadataDataType = '';
  protected $slashCommandType = SlashCommandMetadata::class;
  protected $slashCommandDataType = '';
  /**
   * Start index (0-based, inclusive) in the plain-text message body this
   * annotation corresponds to.
   *
   * @var int
   */
  public $startIndex;
  /**
   * The type of this annotation.
   *
   * @var string
   */
  public $type;
  protected $userMentionType = UserMentionMetadata::class;
  protected $userMentionDataType = '';

  /**
   * The metadata for a custom emoji.
   *
   * @param CustomEmojiMetadata $customEmojiMetadata
   */
  public function setCustomEmojiMetadata(CustomEmojiMetadata $customEmojiMetadata)
  {
    $this->customEmojiMetadata = $customEmojiMetadata;
  }
  /**
   * @return CustomEmojiMetadata
   */
  public function getCustomEmojiMetadata()
  {
    return $this->customEmojiMetadata;
  }
  /**
   * Length of the substring in the plain-text message body this annotation
   * corresponds to. If not present, indicates a length of 0.
   *
   * @param int $length
   */
  public function setLength($length)
  {
    $this->length = $length;
  }
  /**
   * @return int
   */
  public function getLength()
  {
    return $this->length;
  }
  /**
   * The metadata for a rich link.
   *
   * @param RichLinkMetadata $richLinkMetadata
   */
  public function setRichLinkMetadata(RichLinkMetadata $richLinkMetadata)
  {
    $this->richLinkMetadata = $richLinkMetadata;
  }
  /**
   * @return RichLinkMetadata
   */
  public function getRichLinkMetadata()
  {
    return $this->richLinkMetadata;
  }
  /**
   * The metadata for a slash command.
   *
   * @param SlashCommandMetadata $slashCommand
   */
  public function setSlashCommand(SlashCommandMetadata $slashCommand)
  {
    $this->slashCommand = $slashCommand;
  }
  /**
   * @return SlashCommandMetadata
   */
  public function getSlashCommand()
  {
    return $this->slashCommand;
  }
  /**
   * Start index (0-based, inclusive) in the plain-text message body this
   * annotation corresponds to.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * The type of this annotation.
   *
   * Accepted values: ANNOTATION_TYPE_UNSPECIFIED, USER_MENTION, SLASH_COMMAND,
   * RICH_LINK, CUSTOM_EMOJI
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
   * The metadata of user mention.
   *
   * @param UserMentionMetadata $userMention
   */
  public function setUserMention(UserMentionMetadata $userMention)
  {
    $this->userMention = $userMention;
  }
  /**
   * @return UserMentionMetadata
   */
  public function getUserMention()
  {
    return $this->userMention;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Annotation::class, 'Google_Service_HangoutsChat_Annotation');
