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

class LiveChatMessageSnippet extends \Google\Model
{
  public const TYPE_invalidType = 'invalidType';
  public const TYPE_textMessageEvent = 'textMessageEvent';
  public const TYPE_tombstone = 'tombstone';
  public const TYPE_fanFundingEvent = 'fanFundingEvent';
  public const TYPE_chatEndedEvent = 'chatEndedEvent';
  public const TYPE_sponsorOnlyModeStartedEvent = 'sponsorOnlyModeStartedEvent';
  public const TYPE_sponsorOnlyModeEndedEvent = 'sponsorOnlyModeEndedEvent';
  public const TYPE_newSponsorEvent = 'newSponsorEvent';
  public const TYPE_memberMilestoneChatEvent = 'memberMilestoneChatEvent';
  public const TYPE_membershipGiftingEvent = 'membershipGiftingEvent';
  public const TYPE_giftMembershipReceivedEvent = 'giftMembershipReceivedEvent';
  public const TYPE_messageDeletedEvent = 'messageDeletedEvent';
  public const TYPE_messageRetractedEvent = 'messageRetractedEvent';
  public const TYPE_userBannedEvent = 'userBannedEvent';
  public const TYPE_superChatEvent = 'superChatEvent';
  public const TYPE_superStickerEvent = 'superStickerEvent';
  public const TYPE_pollEvent = 'pollEvent';
  /**
   * The ID of the user that authored this message, this field is not always
   * filled. textMessageEvent - the user that wrote the message fanFundingEvent
   * - the user that funded the broadcast newSponsorEvent - the user that just
   * became a sponsor memberMilestoneChatEvent - the member that sent the
   * message membershipGiftingEvent - the user that made the purchase
   * giftMembershipReceivedEvent - the user that received the gift membership
   * messageDeletedEvent - the moderator that took the action
   * messageRetractedEvent - the author that retracted their message
   * userBannedEvent - the moderator that took the action superChatEvent - the
   * user that made the purchase superStickerEvent - the user that made the
   * purchase pollEvent - the user that created the poll
   *
   * @var string
   */
  public $authorChannelId;
  /**
   * Contains a string that can be displayed to the user. If this field is not
   * present the message is silent, at the moment only messages of type
   * TOMBSTONE and CHAT_ENDED_EVENT are silent.
   *
   * @var string
   */
  public $displayMessage;
  protected $fanFundingEventDetailsType = LiveChatFanFundingEventDetails::class;
  protected $fanFundingEventDetailsDataType = '';
  protected $giftMembershipReceivedDetailsType = LiveChatGiftMembershipReceivedDetails::class;
  protected $giftMembershipReceivedDetailsDataType = '';
  /**
   * Whether the message has display content that should be displayed to users.
   *
   * @var bool
   */
  public $hasDisplayContent;
  /**
   * @var string
   */
  public $liveChatId;
  protected $memberMilestoneChatDetailsType = LiveChatMemberMilestoneChatDetails::class;
  protected $memberMilestoneChatDetailsDataType = '';
  protected $membershipGiftingDetailsType = LiveChatMembershipGiftingDetails::class;
  protected $membershipGiftingDetailsDataType = '';
  protected $messageDeletedDetailsType = LiveChatMessageDeletedDetails::class;
  protected $messageDeletedDetailsDataType = '';
  protected $messageRetractedDetailsType = LiveChatMessageRetractedDetails::class;
  protected $messageRetractedDetailsDataType = '';
  protected $newSponsorDetailsType = LiveChatNewSponsorDetails::class;
  protected $newSponsorDetailsDataType = '';
  protected $pollDetailsType = LiveChatPollDetails::class;
  protected $pollDetailsDataType = '';
  /**
   * The date and time when the message was orignally published.
   *
   * @var string
   */
  public $publishedAt;
  protected $superChatDetailsType = LiveChatSuperChatDetails::class;
  protected $superChatDetailsDataType = '';
  protected $superStickerDetailsType = LiveChatSuperStickerDetails::class;
  protected $superStickerDetailsDataType = '';
  protected $textMessageDetailsType = LiveChatTextMessageDetails::class;
  protected $textMessageDetailsDataType = '';
  /**
   * The type of message, this will always be present, it determines the
   * contents of the message as well as which fields will be present.
   *
   * @var string
   */
  public $type;
  protected $userBannedDetailsType = LiveChatUserBannedMessageDetails::class;
  protected $userBannedDetailsDataType = '';

  /**
   * The ID of the user that authored this message, this field is not always
   * filled. textMessageEvent - the user that wrote the message fanFundingEvent
   * - the user that funded the broadcast newSponsorEvent - the user that just
   * became a sponsor memberMilestoneChatEvent - the member that sent the
   * message membershipGiftingEvent - the user that made the purchase
   * giftMembershipReceivedEvent - the user that received the gift membership
   * messageDeletedEvent - the moderator that took the action
   * messageRetractedEvent - the author that retracted their message
   * userBannedEvent - the moderator that took the action superChatEvent - the
   * user that made the purchase superStickerEvent - the user that made the
   * purchase pollEvent - the user that created the poll
   *
   * @param string $authorChannelId
   */
  public function setAuthorChannelId($authorChannelId)
  {
    $this->authorChannelId = $authorChannelId;
  }
  /**
   * @return string
   */
  public function getAuthorChannelId()
  {
    return $this->authorChannelId;
  }
  /**
   * Contains a string that can be displayed to the user. If this field is not
   * present the message is silent, at the moment only messages of type
   * TOMBSTONE and CHAT_ENDED_EVENT are silent.
   *
   * @param string $displayMessage
   */
  public function setDisplayMessage($displayMessage)
  {
    $this->displayMessage = $displayMessage;
  }
  /**
   * @return string
   */
  public function getDisplayMessage()
  {
    return $this->displayMessage;
  }
  /**
   * Details about the funding event, this is only set if the type is
   * 'fanFundingEvent'.
   *
   * @deprecated
   * @param LiveChatFanFundingEventDetails $fanFundingEventDetails
   */
  public function setFanFundingEventDetails(LiveChatFanFundingEventDetails $fanFundingEventDetails)
  {
    $this->fanFundingEventDetails = $fanFundingEventDetails;
  }
  /**
   * @deprecated
   * @return LiveChatFanFundingEventDetails
   */
  public function getFanFundingEventDetails()
  {
    return $this->fanFundingEventDetails;
  }
  /**
   * Details about the Gift Membership Received event, this is only set if the
   * type is 'giftMembershipReceivedEvent'.
   *
   * @param LiveChatGiftMembershipReceivedDetails $giftMembershipReceivedDetails
   */
  public function setGiftMembershipReceivedDetails(LiveChatGiftMembershipReceivedDetails $giftMembershipReceivedDetails)
  {
    $this->giftMembershipReceivedDetails = $giftMembershipReceivedDetails;
  }
  /**
   * @return LiveChatGiftMembershipReceivedDetails
   */
  public function getGiftMembershipReceivedDetails()
  {
    return $this->giftMembershipReceivedDetails;
  }
  /**
   * Whether the message has display content that should be displayed to users.
   *
   * @param bool $hasDisplayContent
   */
  public function setHasDisplayContent($hasDisplayContent)
  {
    $this->hasDisplayContent = $hasDisplayContent;
  }
  /**
   * @return bool
   */
  public function getHasDisplayContent()
  {
    return $this->hasDisplayContent;
  }
  /**
   * @param string $liveChatId
   */
  public function setLiveChatId($liveChatId)
  {
    $this->liveChatId = $liveChatId;
  }
  /**
   * @return string
   */
  public function getLiveChatId()
  {
    return $this->liveChatId;
  }
  /**
   * Details about the Member Milestone Chat event, this is only set if the type
   * is 'memberMilestoneChatEvent'.
   *
   * @param LiveChatMemberMilestoneChatDetails $memberMilestoneChatDetails
   */
  public function setMemberMilestoneChatDetails(LiveChatMemberMilestoneChatDetails $memberMilestoneChatDetails)
  {
    $this->memberMilestoneChatDetails = $memberMilestoneChatDetails;
  }
  /**
   * @return LiveChatMemberMilestoneChatDetails
   */
  public function getMemberMilestoneChatDetails()
  {
    return $this->memberMilestoneChatDetails;
  }
  /**
   * Details about the Membership Gifting event, this is only set if the type is
   * 'membershipGiftingEvent'.
   *
   * @param LiveChatMembershipGiftingDetails $membershipGiftingDetails
   */
  public function setMembershipGiftingDetails(LiveChatMembershipGiftingDetails $membershipGiftingDetails)
  {
    $this->membershipGiftingDetails = $membershipGiftingDetails;
  }
  /**
   * @return LiveChatMembershipGiftingDetails
   */
  public function getMembershipGiftingDetails()
  {
    return $this->membershipGiftingDetails;
  }
  /**
   * @param LiveChatMessageDeletedDetails $messageDeletedDetails
   */
  public function setMessageDeletedDetails(LiveChatMessageDeletedDetails $messageDeletedDetails)
  {
    $this->messageDeletedDetails = $messageDeletedDetails;
  }
  /**
   * @return LiveChatMessageDeletedDetails
   */
  public function getMessageDeletedDetails()
  {
    return $this->messageDeletedDetails;
  }
  /**
   * @param LiveChatMessageRetractedDetails $messageRetractedDetails
   */
  public function setMessageRetractedDetails(LiveChatMessageRetractedDetails $messageRetractedDetails)
  {
    $this->messageRetractedDetails = $messageRetractedDetails;
  }
  /**
   * @return LiveChatMessageRetractedDetails
   */
  public function getMessageRetractedDetails()
  {
    return $this->messageRetractedDetails;
  }
  /**
   * Details about the New Member Announcement event, this is only set if the
   * type is 'newSponsorEvent'. Please note that "member" is the new term for
   * "sponsor".
   *
   * @param LiveChatNewSponsorDetails $newSponsorDetails
   */
  public function setNewSponsorDetails(LiveChatNewSponsorDetails $newSponsorDetails)
  {
    $this->newSponsorDetails = $newSponsorDetails;
  }
  /**
   * @return LiveChatNewSponsorDetails
   */
  public function getNewSponsorDetails()
  {
    return $this->newSponsorDetails;
  }
  /**
   * Details about the poll event, this is only set if the type is 'pollEvent'.
   *
   * @param LiveChatPollDetails $pollDetails
   */
  public function setPollDetails(LiveChatPollDetails $pollDetails)
  {
    $this->pollDetails = $pollDetails;
  }
  /**
   * @return LiveChatPollDetails
   */
  public function getPollDetails()
  {
    return $this->pollDetails;
  }
  /**
   * The date and time when the message was orignally published.
   *
   * @param string $publishedAt
   */
  public function setPublishedAt($publishedAt)
  {
    $this->publishedAt = $publishedAt;
  }
  /**
   * @return string
   */
  public function getPublishedAt()
  {
    return $this->publishedAt;
  }
  /**
   * Details about the Super Chat event, this is only set if the type is
   * 'superChatEvent'.
   *
   * @param LiveChatSuperChatDetails $superChatDetails
   */
  public function setSuperChatDetails(LiveChatSuperChatDetails $superChatDetails)
  {
    $this->superChatDetails = $superChatDetails;
  }
  /**
   * @return LiveChatSuperChatDetails
   */
  public function getSuperChatDetails()
  {
    return $this->superChatDetails;
  }
  /**
   * Details about the Super Sticker event, this is only set if the type is
   * 'superStickerEvent'.
   *
   * @param LiveChatSuperStickerDetails $superStickerDetails
   */
  public function setSuperStickerDetails(LiveChatSuperStickerDetails $superStickerDetails)
  {
    $this->superStickerDetails = $superStickerDetails;
  }
  /**
   * @return LiveChatSuperStickerDetails
   */
  public function getSuperStickerDetails()
  {
    return $this->superStickerDetails;
  }
  /**
   * Details about the text message, this is only set if the type is
   * 'textMessageEvent'.
   *
   * @param LiveChatTextMessageDetails $textMessageDetails
   */
  public function setTextMessageDetails(LiveChatTextMessageDetails $textMessageDetails)
  {
    $this->textMessageDetails = $textMessageDetails;
  }
  /**
   * @return LiveChatTextMessageDetails
   */
  public function getTextMessageDetails()
  {
    return $this->textMessageDetails;
  }
  /**
   * The type of message, this will always be present, it determines the
   * contents of the message as well as which fields will be present.
   *
   * Accepted values: invalidType, textMessageEvent, tombstone, fanFundingEvent,
   * chatEndedEvent, sponsorOnlyModeStartedEvent, sponsorOnlyModeEndedEvent,
   * newSponsorEvent, memberMilestoneChatEvent, membershipGiftingEvent,
   * giftMembershipReceivedEvent, messageDeletedEvent, messageRetractedEvent,
   * userBannedEvent, superChatEvent, superStickerEvent, pollEvent
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
   * @param LiveChatUserBannedMessageDetails $userBannedDetails
   */
  public function setUserBannedDetails(LiveChatUserBannedMessageDetails $userBannedDetails)
  {
    $this->userBannedDetails = $userBannedDetails;
  }
  /**
   * @return LiveChatUserBannedMessageDetails
   */
  public function getUserBannedDetails()
  {
    return $this->userBannedDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatMessageSnippet::class, 'Google_Service_YouTube_LiveChatMessageSnippet');
