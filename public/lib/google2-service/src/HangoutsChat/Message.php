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

class Message extends \Google\Collection
{
  protected $collection_key = 'emojiReactionSummaries';
  protected $accessoryWidgetsType = AccessoryWidget::class;
  protected $accessoryWidgetsDataType = 'array';
  protected $actionResponseType = ActionResponse::class;
  protected $actionResponseDataType = '';
  protected $annotationsType = Annotation::class;
  protected $annotationsDataType = 'array';
  /**
   * Output only. Plain-text body of the message with all Chat app mentions
   * stripped out.
   *
   * @var string
   */
  public $argumentText;
  protected $attachedGifsType = AttachedGif::class;
  protected $attachedGifsDataType = 'array';
  protected $attachmentType = Attachment::class;
  protected $attachmentDataType = 'array';
  protected $cardsType = Card::class;
  protected $cardsDataType = 'array';
  protected $cardsV2Type = CardWithId::class;
  protected $cardsV2DataType = 'array';
  /**
   * Optional. A custom ID for the message. You can use field to identify a
   * message, or to get, delete, or update a message. To set a custom ID,
   * specify the [`messageId`](https://developers.google.com/workspace/chat/api/
   * reference/rest/v1/spaces.messages/create#body.QUERY_PARAMETERS.message_id)
   * field when you create the message. For details, see [Name a
   * message](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
   *
   * @var string
   */
  public $clientAssignedMessageId;
  /**
   * Optional. Immutable. For spaces created in Chat, the time at which the
   * message was created. This field is output only, except when used in import
   * mode spaces. For import mode spaces, set this field to the historical
   * timestamp at which the message was created in the source in order to
   * preserve the original creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time at which the message was deleted in Google Chat. If
   * the message is never deleted, this field is empty.
   *
   * @var string
   */
  public $deleteTime;
  protected $deletionMetadataType = DeletionMetadata::class;
  protected $deletionMetadataDataType = '';
  protected $emojiReactionSummariesType = EmojiReactionSummary::class;
  protected $emojiReactionSummariesDataType = 'array';
  /**
   * Optional. A plain-text description of the message's cards, used when the
   * actual cards can't be displayed—for example, mobile notifications.
   *
   * @var string
   */
  public $fallbackText;
  /**
   * Output only. Contains the message `text` with markups added to communicate
   * formatting. This field might not capture all formatting visible in the UI,
   * but includes the following: * [Markup
   * syntax](https://developers.google.com/workspace/chat/format-messages) for
   * bold, italic, strikethrough, monospace, monospace block, and bulleted list.
   * * [User mentions](https://developers.google.com/workspace/chat/format-
   * messages#messages-@mention) using the format ``. * Custom hyperlinks using
   * the format `<{url}|{rendered_text}>` where the first string is the URL and
   * the second is the rendered text—for example, ``. * Custom emoji using the
   * format `:{emoji_name}:`—for example, `:smile:`. This doesn't apply to
   * Unicode emoji, such as `U+1F600` for a grinning face emoji. * Bullet list
   * items using asterisks (`*`)—for example, `* item`. For more information,
   * see [View text formatting sent in a
   * message](https://developers.google.com/workspace/chat/format-
   * messages#view_text_formatting_sent_in_a_message)
   *
   * @var string
   */
  public $formattedText;
  /**
   * Output only. The time at which the message was last edited by a user. If
   * the message has never been edited, this field is empty.
   *
   * @var string
   */
  public $lastUpdateTime;
  protected $matchedUrlType = MatchedUrl::class;
  protected $matchedUrlDataType = '';
  /**
   * Identifier. Resource name of the message. Format:
   * `spaces/{space}/messages/{message}` Where `{space}` is the ID of the space
   * where the message is posted and `{message}` is a system-assigned ID for the
   * message. For example,
   * `spaces/AAAAAAAAAAA/messages/BBBBBBBBBBB.BBBBBBBBBBB`. If you set a custom
   * ID when you create a message, you can use this ID to specify the message in
   * a request by replacing `{message}` with the value from the
   * `clientAssignedMessageId` field. For example,
   * `spaces/AAAAAAAAAAA/messages/client-custom-name`. For details, see [Name a
   * message](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
   *
   * @var string
   */
  public $name;
  protected $privateMessageViewerType = User::class;
  protected $privateMessageViewerDataType = '';
  protected $quotedMessageMetadataType = QuotedMessageMetadata::class;
  protected $quotedMessageMetadataDataType = '';
  protected $senderType = User::class;
  protected $senderDataType = '';
  protected $slashCommandType = SlashCommand::class;
  protected $slashCommandDataType = '';
  protected $spaceType = Space::class;
  protected $spaceDataType = '';
  /**
   * Optional. Plain-text body of the message. The first link to an image,
   * video, or web page generates a [preview
   * chip](https://developers.google.com/workspace/chat/preview-links). You can
   * also [@mention a Google Chat
   * user](https://developers.google.com/workspace/chat/format-
   * messages#messages-@mention), or everyone in the space. To learn about
   * creating text messages, see [Send a
   * message](https://developers.google.com/workspace/chat/create-messages).
   *
   * @var string
   */
  public $text;
  protected $threadType = Thread::class;
  protected $threadDataType = '';
  /**
   * Output only. When `true`, the message is a response in a reply thread. When
   * `false`, the message is visible in the space's top-level conversation as
   * either the first message of a thread or a message with no threaded replies.
   * If the space doesn't support reply in threads, this field is always
   * `false`.
   *
   * @var bool
   */
  public $threadReply;

  /**
   * Optional. One or more interactive widgets that appear at the bottom of a
   * message. You can add accessory widgets to messages that contain text,
   * cards, or both text and cards. Not supported for messages that contain
   * dialogs. For details, see [Add interactive widgets at the bottom of a
   * message](https://developers.google.com/workspace/chat/create-messages#add-
   * accessory-widgets). Creating a message with accessory widgets requires [app
   * authentication] (https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app).
   *
   * @param AccessoryWidget[] $accessoryWidgets
   */
  public function setAccessoryWidgets($accessoryWidgets)
  {
    $this->accessoryWidgets = $accessoryWidgets;
  }
  /**
   * @return AccessoryWidget[]
   */
  public function getAccessoryWidgets()
  {
    return $this->accessoryWidgets;
  }
  /**
   * Input only. Parameters that a Chat app can use to configure how its
   * response is posted.
   *
   * @param ActionResponse $actionResponse
   */
  public function setActionResponse(ActionResponse $actionResponse)
  {
    $this->actionResponse = $actionResponse;
  }
  /**
   * @return ActionResponse
   */
  public function getActionResponse()
  {
    return $this->actionResponse;
  }
  /**
   * Output only. Annotations can be associated with the plain-text body of the
   * message or with chips that link to Google Workspace resources like Google
   * Docs or Sheets with `start_index` and `length` of 0.
   *
   * @param Annotation[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return Annotation[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. Plain-text body of the message with all Chat app mentions
   * stripped out.
   *
   * @param string $argumentText
   */
  public function setArgumentText($argumentText)
  {
    $this->argumentText = $argumentText;
  }
  /**
   * @return string
   */
  public function getArgumentText()
  {
    return $this->argumentText;
  }
  /**
   * Output only. GIF images that are attached to the message.
   *
   * @param AttachedGif[] $attachedGifs
   */
  public function setAttachedGifs($attachedGifs)
  {
    $this->attachedGifs = $attachedGifs;
  }
  /**
   * @return AttachedGif[]
   */
  public function getAttachedGifs()
  {
    return $this->attachedGifs;
  }
  /**
   * Optional. User-uploaded attachment.
   *
   * @param Attachment[] $attachment
   */
  public function setAttachment($attachment)
  {
    $this->attachment = $attachment;
  }
  /**
   * @return Attachment[]
   */
  public function getAttachment()
  {
    return $this->attachment;
  }
  /**
   * Deprecated: Use `cards_v2` instead. Rich, formatted, and interactive cards
   * that you can use to display UI elements such as: formatted texts, buttons,
   * and clickable images. Cards are normally displayed below the plain-text
   * body of the message. `cards` and `cards_v2` can have a maximum size of 32
   * KB.
   *
   * @deprecated
   * @param Card[] $cards
   */
  public function setCards($cards)
  {
    $this->cards = $cards;
  }
  /**
   * @deprecated
   * @return Card[]
   */
  public function getCards()
  {
    return $this->cards;
  }
  /**
   * Optional. An array of [cards](https://developers.google.com/workspace/chat/
   * api/reference/rest/v1/cards). Only Chat apps can create cards. If your Chat
   * app [authenticates as a
   * user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user), the messages can't contain cards. To learn how to create a
   * message that contains cards, see [Send a
   * message](https://developers.google.com/workspace/chat/create-messages).
   * [Card builder](https://addons.gsuite.google.com/uikit/builder)
   *
   * @param CardWithId[] $cardsV2
   */
  public function setCardsV2($cardsV2)
  {
    $this->cardsV2 = $cardsV2;
  }
  /**
   * @return CardWithId[]
   */
  public function getCardsV2()
  {
    return $this->cardsV2;
  }
  /**
   * Optional. A custom ID for the message. You can use field to identify a
   * message, or to get, delete, or update a message. To set a custom ID,
   * specify the [`messageId`](https://developers.google.com/workspace/chat/api/
   * reference/rest/v1/spaces.messages/create#body.QUERY_PARAMETERS.message_id)
   * field when you create the message. For details, see [Name a
   * message](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
   *
   * @param string $clientAssignedMessageId
   */
  public function setClientAssignedMessageId($clientAssignedMessageId)
  {
    $this->clientAssignedMessageId = $clientAssignedMessageId;
  }
  /**
   * @return string
   */
  public function getClientAssignedMessageId()
  {
    return $this->clientAssignedMessageId;
  }
  /**
   * Optional. Immutable. For spaces created in Chat, the time at which the
   * message was created. This field is output only, except when used in import
   * mode spaces. For import mode spaces, set this field to the historical
   * timestamp at which the message was created in the source in order to
   * preserve the original creation time.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The time at which the message was deleted in Google Chat. If
   * the message is never deleted, this field is empty.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Output only. Information about a deleted message. A message is deleted when
   * `delete_time` is set.
   *
   * @param DeletionMetadata $deletionMetadata
   */
  public function setDeletionMetadata(DeletionMetadata $deletionMetadata)
  {
    $this->deletionMetadata = $deletionMetadata;
  }
  /**
   * @return DeletionMetadata
   */
  public function getDeletionMetadata()
  {
    return $this->deletionMetadata;
  }
  /**
   * Output only. The list of emoji reaction summaries on the message.
   *
   * @param EmojiReactionSummary[] $emojiReactionSummaries
   */
  public function setEmojiReactionSummaries($emojiReactionSummaries)
  {
    $this->emojiReactionSummaries = $emojiReactionSummaries;
  }
  /**
   * @return EmojiReactionSummary[]
   */
  public function getEmojiReactionSummaries()
  {
    return $this->emojiReactionSummaries;
  }
  /**
   * Optional. A plain-text description of the message's cards, used when the
   * actual cards can't be displayed—for example, mobile notifications.
   *
   * @param string $fallbackText
   */
  public function setFallbackText($fallbackText)
  {
    $this->fallbackText = $fallbackText;
  }
  /**
   * @return string
   */
  public function getFallbackText()
  {
    return $this->fallbackText;
  }
  /**
   * Output only. Contains the message `text` with markups added to communicate
   * formatting. This field might not capture all formatting visible in the UI,
   * but includes the following: * [Markup
   * syntax](https://developers.google.com/workspace/chat/format-messages) for
   * bold, italic, strikethrough, monospace, monospace block, and bulleted list.
   * * [User mentions](https://developers.google.com/workspace/chat/format-
   * messages#messages-@mention) using the format ``. * Custom hyperlinks using
   * the format `<{url}|{rendered_text}>` where the first string is the URL and
   * the second is the rendered text—for example, ``. * Custom emoji using the
   * format `:{emoji_name}:`—for example, `:smile:`. This doesn't apply to
   * Unicode emoji, such as `U+1F600` for a grinning face emoji. * Bullet list
   * items using asterisks (`*`)—for example, `* item`. For more information,
   * see [View text formatting sent in a
   * message](https://developers.google.com/workspace/chat/format-
   * messages#view_text_formatting_sent_in_a_message)
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
   * Output only. The time at which the message was last edited by a user. If
   * the message has never been edited, this field is empty.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * Output only. A URL in `spaces.messages.text` that matches a link preview
   * pattern. For more information, see [Preview
   * links](https://developers.google.com/workspace/chat/preview-links).
   *
   * @param MatchedUrl $matchedUrl
   */
  public function setMatchedUrl(MatchedUrl $matchedUrl)
  {
    $this->matchedUrl = $matchedUrl;
  }
  /**
   * @return MatchedUrl
   */
  public function getMatchedUrl()
  {
    return $this->matchedUrl;
  }
  /**
   * Identifier. Resource name of the message. Format:
   * `spaces/{space}/messages/{message}` Where `{space}` is the ID of the space
   * where the message is posted and `{message}` is a system-assigned ID for the
   * message. For example,
   * `spaces/AAAAAAAAAAA/messages/BBBBBBBBBBB.BBBBBBBBBBB`. If you set a custom
   * ID when you create a message, you can use this ID to specify the message in
   * a request by replacing `{message}` with the value from the
   * `clientAssignedMessageId` field. For example,
   * `spaces/AAAAAAAAAAA/messages/client-custom-name`. For details, see [Name a
   * message](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
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
   * Optional. Immutable. Input for creating a message, otherwise output only.
   * The user that can view the message. When set, the message is private and
   * only visible to the specified user and the Chat app. To include this field
   * in your request, you must call the Chat API using [app
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) and omit the following: * [Attachments](https://develop
   * ers.google.com/workspace/chat/api/reference/rest/v1/spaces.messages.attachm
   * ents) * [Accessory widgets](https://developers.google.com/workspace/chat/ap
   * i/reference/rest/v1/spaces.messages#Message.AccessoryWidget) For details,
   * see [Send a message
   * privately](https://developers.google.com/workspace/chat/create-
   * messages#private).
   *
   * @param User $privateMessageViewer
   */
  public function setPrivateMessageViewer(User $privateMessageViewer)
  {
    $this->privateMessageViewer = $privateMessageViewer;
  }
  /**
   * @return User
   */
  public function getPrivateMessageViewer()
  {
    return $this->privateMessageViewer;
  }
  /**
   * Optional. Information about a message that another message quotes. When you
   * create a message, you can quote messages within the same thread, or quote a
   * root message to create a new root message. However, you can't quote a
   * message reply from a different thread. When you update a message, you can't
   * add or replace the `quotedMessageMetadata` field, but you can remove it.
   * For example usage, see [Quote another
   * message](https://developers.google.com/workspace/chat/create-
   * messages#quote-a-message).
   *
   * @param QuotedMessageMetadata $quotedMessageMetadata
   */
  public function setQuotedMessageMetadata(QuotedMessageMetadata $quotedMessageMetadata)
  {
    $this->quotedMessageMetadata = $quotedMessageMetadata;
  }
  /**
   * @return QuotedMessageMetadata
   */
  public function getQuotedMessageMetadata()
  {
    return $this->quotedMessageMetadata;
  }
  /**
   * Output only. The user who created the message. If your Chat app
   * [authenticates as a
   * user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user), the output populates the [user](https://developers.google.com/w
   * orkspace/chat/api/reference/rest/v1/User) `name` and `type`.
   *
   * @param User $sender
   */
  public function setSender(User $sender)
  {
    $this->sender = $sender;
  }
  /**
   * @return User
   */
  public function getSender()
  {
    return $this->sender;
  }
  /**
   * Output only. Slash command information, if applicable.
   *
   * @param SlashCommand $slashCommand
   */
  public function setSlashCommand(SlashCommand $slashCommand)
  {
    $this->slashCommand = $slashCommand;
  }
  /**
   * @return SlashCommand
   */
  public function getSlashCommand()
  {
    return $this->slashCommand;
  }
  /**
   * Output only. If your Chat app [authenticates as a
   * user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user), the output only populates the [space](https://developers.google
   * .com/workspace/chat/api/reference/rest/v1/spaces) `name`.
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
   * Optional. Plain-text body of the message. The first link to an image,
   * video, or web page generates a [preview
   * chip](https://developers.google.com/workspace/chat/preview-links). You can
   * also [@mention a Google Chat
   * user](https://developers.google.com/workspace/chat/format-
   * messages#messages-@mention), or everyone in the space. To learn about
   * creating text messages, see [Send a
   * message](https://developers.google.com/workspace/chat/create-messages).
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The thread the message belongs to. For example usage, see [Start or reply
   * to a message thread](https://developers.google.com/workspace/chat/create-
   * messages#create-message-thread).
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
   * Output only. When `true`, the message is a response in a reply thread. When
   * `false`, the message is visible in the space's top-level conversation as
   * either the first message of a thread or a message with no threaded replies.
   * If the space doesn't support reply in threads, this field is always
   * `false`.
   *
   * @param bool $threadReply
   */
  public function setThreadReply($threadReply)
  {
    $this->threadReply = $threadReply;
  }
  /**
   * @return bool
   */
  public function getThreadReply()
  {
    return $this->threadReply;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_HangoutsChat_Message');
