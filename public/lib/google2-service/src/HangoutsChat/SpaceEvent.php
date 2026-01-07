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

class SpaceEvent extends \Google\Model
{
  /**
   * Time when the event occurred.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Type of space event. Each event type has a batch version, which represents
   * multiple instances of the event type that occur in a short period of time.
   * For `spaceEvents.list()` requests, omit batch event types in your query
   * filter. By default, the server returns both event type and its batch
   * version. Supported event types for [messages](https://developers.google.com
   * /workspace/chat/api/reference/rest/v1/spaces.messages): * New message:
   * `google.workspace.chat.message.v1.created` * Updated message:
   * `google.workspace.chat.message.v1.updated` * Deleted message:
   * `google.workspace.chat.message.v1.deleted` * Multiple new messages:
   * `google.workspace.chat.message.v1.batchCreated` * Multiple updated
   * messages: `google.workspace.chat.message.v1.batchUpdated` * Multiple
   * deleted messages: `google.workspace.chat.message.v1.batchDeleted` Supported
   * event types for [memberships](https://developers.google.com/workspace/chat/
   * api/reference/rest/v1/spaces.members): * New membership:
   * `google.workspace.chat.membership.v1.created` * Updated membership:
   * `google.workspace.chat.membership.v1.updated` * Deleted membership:
   * `google.workspace.chat.membership.v1.deleted` * Multiple new memberships:
   * `google.workspace.chat.membership.v1.batchCreated` * Multiple updated
   * memberships: `google.workspace.chat.membership.v1.batchUpdated` * Multiple
   * deleted memberships: `google.workspace.chat.membership.v1.batchDeleted`
   * Supported event types for [reactions](https://developers.google.com/workspa
   * ce/chat/api/reference/rest/v1/spaces.messages.reactions): * New reaction:
   * `google.workspace.chat.reaction.v1.created` * Deleted reaction:
   * `google.workspace.chat.reaction.v1.deleted` * Multiple new reactions:
   * `google.workspace.chat.reaction.v1.batchCreated` * Multiple deleted
   * reactions: `google.workspace.chat.reaction.v1.batchDeleted` Supported event
   * types about the [space](https://developers.google.com/workspace/chat/api/re
   * ference/rest/v1/spaces): * Updated space:
   * `google.workspace.chat.space.v1.updated` * Multiple space updates:
   * `google.workspace.chat.space.v1.batchUpdated`
   *
   * @var string
   */
  public $eventType;
  protected $membershipBatchCreatedEventDataType = MembershipBatchCreatedEventData::class;
  protected $membershipBatchCreatedEventDataDataType = '';
  protected $membershipBatchDeletedEventDataType = MembershipBatchDeletedEventData::class;
  protected $membershipBatchDeletedEventDataDataType = '';
  protected $membershipBatchUpdatedEventDataType = MembershipBatchUpdatedEventData::class;
  protected $membershipBatchUpdatedEventDataDataType = '';
  protected $membershipCreatedEventDataType = MembershipCreatedEventData::class;
  protected $membershipCreatedEventDataDataType = '';
  protected $membershipDeletedEventDataType = MembershipDeletedEventData::class;
  protected $membershipDeletedEventDataDataType = '';
  protected $membershipUpdatedEventDataType = MembershipUpdatedEventData::class;
  protected $membershipUpdatedEventDataDataType = '';
  protected $messageBatchCreatedEventDataType = MessageBatchCreatedEventData::class;
  protected $messageBatchCreatedEventDataDataType = '';
  protected $messageBatchDeletedEventDataType = MessageBatchDeletedEventData::class;
  protected $messageBatchDeletedEventDataDataType = '';
  protected $messageBatchUpdatedEventDataType = MessageBatchUpdatedEventData::class;
  protected $messageBatchUpdatedEventDataDataType = '';
  protected $messageCreatedEventDataType = MessageCreatedEventData::class;
  protected $messageCreatedEventDataDataType = '';
  protected $messageDeletedEventDataType = MessageDeletedEventData::class;
  protected $messageDeletedEventDataDataType = '';
  protected $messageUpdatedEventDataType = MessageUpdatedEventData::class;
  protected $messageUpdatedEventDataDataType = '';
  /**
   * Resource name of the space event. Format:
   * `spaces/{space}/spaceEvents/{spaceEvent}`
   *
   * @var string
   */
  public $name;
  protected $reactionBatchCreatedEventDataType = ReactionBatchCreatedEventData::class;
  protected $reactionBatchCreatedEventDataDataType = '';
  protected $reactionBatchDeletedEventDataType = ReactionBatchDeletedEventData::class;
  protected $reactionBatchDeletedEventDataDataType = '';
  protected $reactionCreatedEventDataType = ReactionCreatedEventData::class;
  protected $reactionCreatedEventDataDataType = '';
  protected $reactionDeletedEventDataType = ReactionDeletedEventData::class;
  protected $reactionDeletedEventDataDataType = '';
  protected $spaceBatchUpdatedEventDataType = SpaceBatchUpdatedEventData::class;
  protected $spaceBatchUpdatedEventDataDataType = '';
  protected $spaceUpdatedEventDataType = SpaceUpdatedEventData::class;
  protected $spaceUpdatedEventDataDataType = '';

  /**
   * Time when the event occurred.
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
   * Type of space event. Each event type has a batch version, which represents
   * multiple instances of the event type that occur in a short period of time.
   * For `spaceEvents.list()` requests, omit batch event types in your query
   * filter. By default, the server returns both event type and its batch
   * version. Supported event types for [messages](https://developers.google.com
   * /workspace/chat/api/reference/rest/v1/spaces.messages): * New message:
   * `google.workspace.chat.message.v1.created` * Updated message:
   * `google.workspace.chat.message.v1.updated` * Deleted message:
   * `google.workspace.chat.message.v1.deleted` * Multiple new messages:
   * `google.workspace.chat.message.v1.batchCreated` * Multiple updated
   * messages: `google.workspace.chat.message.v1.batchUpdated` * Multiple
   * deleted messages: `google.workspace.chat.message.v1.batchDeleted` Supported
   * event types for [memberships](https://developers.google.com/workspace/chat/
   * api/reference/rest/v1/spaces.members): * New membership:
   * `google.workspace.chat.membership.v1.created` * Updated membership:
   * `google.workspace.chat.membership.v1.updated` * Deleted membership:
   * `google.workspace.chat.membership.v1.deleted` * Multiple new memberships:
   * `google.workspace.chat.membership.v1.batchCreated` * Multiple updated
   * memberships: `google.workspace.chat.membership.v1.batchUpdated` * Multiple
   * deleted memberships: `google.workspace.chat.membership.v1.batchDeleted`
   * Supported event types for [reactions](https://developers.google.com/workspa
   * ce/chat/api/reference/rest/v1/spaces.messages.reactions): * New reaction:
   * `google.workspace.chat.reaction.v1.created` * Deleted reaction:
   * `google.workspace.chat.reaction.v1.deleted` * Multiple new reactions:
   * `google.workspace.chat.reaction.v1.batchCreated` * Multiple deleted
   * reactions: `google.workspace.chat.reaction.v1.batchDeleted` Supported event
   * types about the [space](https://developers.google.com/workspace/chat/api/re
   * ference/rest/v1/spaces): * Updated space:
   * `google.workspace.chat.space.v1.updated` * Multiple space updates:
   * `google.workspace.chat.space.v1.batchUpdated`
   *
   * @param string $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return string
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * Event payload for multiple new memberships. Event type:
   * `google.workspace.chat.membership.v1.batchCreated`
   *
   * @param MembershipBatchCreatedEventData $membershipBatchCreatedEventData
   */
  public function setMembershipBatchCreatedEventData(MembershipBatchCreatedEventData $membershipBatchCreatedEventData)
  {
    $this->membershipBatchCreatedEventData = $membershipBatchCreatedEventData;
  }
  /**
   * @return MembershipBatchCreatedEventData
   */
  public function getMembershipBatchCreatedEventData()
  {
    return $this->membershipBatchCreatedEventData;
  }
  /**
   * Event payload for multiple deleted memberships. Event type:
   * `google.workspace.chat.membership.v1.batchDeleted`
   *
   * @param MembershipBatchDeletedEventData $membershipBatchDeletedEventData
   */
  public function setMembershipBatchDeletedEventData(MembershipBatchDeletedEventData $membershipBatchDeletedEventData)
  {
    $this->membershipBatchDeletedEventData = $membershipBatchDeletedEventData;
  }
  /**
   * @return MembershipBatchDeletedEventData
   */
  public function getMembershipBatchDeletedEventData()
  {
    return $this->membershipBatchDeletedEventData;
  }
  /**
   * Event payload for multiple updated memberships. Event type:
   * `google.workspace.chat.membership.v1.batchUpdated`
   *
   * @param MembershipBatchUpdatedEventData $membershipBatchUpdatedEventData
   */
  public function setMembershipBatchUpdatedEventData(MembershipBatchUpdatedEventData $membershipBatchUpdatedEventData)
  {
    $this->membershipBatchUpdatedEventData = $membershipBatchUpdatedEventData;
  }
  /**
   * @return MembershipBatchUpdatedEventData
   */
  public function getMembershipBatchUpdatedEventData()
  {
    return $this->membershipBatchUpdatedEventData;
  }
  /**
   * Event payload for a new membership. Event type:
   * `google.workspace.chat.membership.v1.created`
   *
   * @param MembershipCreatedEventData $membershipCreatedEventData
   */
  public function setMembershipCreatedEventData(MembershipCreatedEventData $membershipCreatedEventData)
  {
    $this->membershipCreatedEventData = $membershipCreatedEventData;
  }
  /**
   * @return MembershipCreatedEventData
   */
  public function getMembershipCreatedEventData()
  {
    return $this->membershipCreatedEventData;
  }
  /**
   * Event payload for a deleted membership. Event type:
   * `google.workspace.chat.membership.v1.deleted`
   *
   * @param MembershipDeletedEventData $membershipDeletedEventData
   */
  public function setMembershipDeletedEventData(MembershipDeletedEventData $membershipDeletedEventData)
  {
    $this->membershipDeletedEventData = $membershipDeletedEventData;
  }
  /**
   * @return MembershipDeletedEventData
   */
  public function getMembershipDeletedEventData()
  {
    return $this->membershipDeletedEventData;
  }
  /**
   * Event payload for an updated membership. Event type:
   * `google.workspace.chat.membership.v1.updated`
   *
   * @param MembershipUpdatedEventData $membershipUpdatedEventData
   */
  public function setMembershipUpdatedEventData(MembershipUpdatedEventData $membershipUpdatedEventData)
  {
    $this->membershipUpdatedEventData = $membershipUpdatedEventData;
  }
  /**
   * @return MembershipUpdatedEventData
   */
  public function getMembershipUpdatedEventData()
  {
    return $this->membershipUpdatedEventData;
  }
  /**
   * Event payload for multiple new messages. Event type:
   * `google.workspace.chat.message.v1.batchCreated`
   *
   * @param MessageBatchCreatedEventData $messageBatchCreatedEventData
   */
  public function setMessageBatchCreatedEventData(MessageBatchCreatedEventData $messageBatchCreatedEventData)
  {
    $this->messageBatchCreatedEventData = $messageBatchCreatedEventData;
  }
  /**
   * @return MessageBatchCreatedEventData
   */
  public function getMessageBatchCreatedEventData()
  {
    return $this->messageBatchCreatedEventData;
  }
  /**
   * Event payload for multiple deleted messages. Event type:
   * `google.workspace.chat.message.v1.batchDeleted`
   *
   * @param MessageBatchDeletedEventData $messageBatchDeletedEventData
   */
  public function setMessageBatchDeletedEventData(MessageBatchDeletedEventData $messageBatchDeletedEventData)
  {
    $this->messageBatchDeletedEventData = $messageBatchDeletedEventData;
  }
  /**
   * @return MessageBatchDeletedEventData
   */
  public function getMessageBatchDeletedEventData()
  {
    return $this->messageBatchDeletedEventData;
  }
  /**
   * Event payload for multiple updated messages. Event type:
   * `google.workspace.chat.message.v1.batchUpdated`
   *
   * @param MessageBatchUpdatedEventData $messageBatchUpdatedEventData
   */
  public function setMessageBatchUpdatedEventData(MessageBatchUpdatedEventData $messageBatchUpdatedEventData)
  {
    $this->messageBatchUpdatedEventData = $messageBatchUpdatedEventData;
  }
  /**
   * @return MessageBatchUpdatedEventData
   */
  public function getMessageBatchUpdatedEventData()
  {
    return $this->messageBatchUpdatedEventData;
  }
  /**
   * Event payload for a new message. Event type:
   * `google.workspace.chat.message.v1.created`
   *
   * @param MessageCreatedEventData $messageCreatedEventData
   */
  public function setMessageCreatedEventData(MessageCreatedEventData $messageCreatedEventData)
  {
    $this->messageCreatedEventData = $messageCreatedEventData;
  }
  /**
   * @return MessageCreatedEventData
   */
  public function getMessageCreatedEventData()
  {
    return $this->messageCreatedEventData;
  }
  /**
   * Event payload for a deleted message. Event type:
   * `google.workspace.chat.message.v1.deleted`
   *
   * @param MessageDeletedEventData $messageDeletedEventData
   */
  public function setMessageDeletedEventData(MessageDeletedEventData $messageDeletedEventData)
  {
    $this->messageDeletedEventData = $messageDeletedEventData;
  }
  /**
   * @return MessageDeletedEventData
   */
  public function getMessageDeletedEventData()
  {
    return $this->messageDeletedEventData;
  }
  /**
   * Event payload for an updated message. Event type:
   * `google.workspace.chat.message.v1.updated`
   *
   * @param MessageUpdatedEventData $messageUpdatedEventData
   */
  public function setMessageUpdatedEventData(MessageUpdatedEventData $messageUpdatedEventData)
  {
    $this->messageUpdatedEventData = $messageUpdatedEventData;
  }
  /**
   * @return MessageUpdatedEventData
   */
  public function getMessageUpdatedEventData()
  {
    return $this->messageUpdatedEventData;
  }
  /**
   * Resource name of the space event. Format:
   * `spaces/{space}/spaceEvents/{spaceEvent}`
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
   * Event payload for multiple new reactions. Event type:
   * `google.workspace.chat.reaction.v1.batchCreated`
   *
   * @param ReactionBatchCreatedEventData $reactionBatchCreatedEventData
   */
  public function setReactionBatchCreatedEventData(ReactionBatchCreatedEventData $reactionBatchCreatedEventData)
  {
    $this->reactionBatchCreatedEventData = $reactionBatchCreatedEventData;
  }
  /**
   * @return ReactionBatchCreatedEventData
   */
  public function getReactionBatchCreatedEventData()
  {
    return $this->reactionBatchCreatedEventData;
  }
  /**
   * Event payload for multiple deleted reactions. Event type:
   * `google.workspace.chat.reaction.v1.batchDeleted`
   *
   * @param ReactionBatchDeletedEventData $reactionBatchDeletedEventData
   */
  public function setReactionBatchDeletedEventData(ReactionBatchDeletedEventData $reactionBatchDeletedEventData)
  {
    $this->reactionBatchDeletedEventData = $reactionBatchDeletedEventData;
  }
  /**
   * @return ReactionBatchDeletedEventData
   */
  public function getReactionBatchDeletedEventData()
  {
    return $this->reactionBatchDeletedEventData;
  }
  /**
   * Event payload for a new reaction. Event type:
   * `google.workspace.chat.reaction.v1.created`
   *
   * @param ReactionCreatedEventData $reactionCreatedEventData
   */
  public function setReactionCreatedEventData(ReactionCreatedEventData $reactionCreatedEventData)
  {
    $this->reactionCreatedEventData = $reactionCreatedEventData;
  }
  /**
   * @return ReactionCreatedEventData
   */
  public function getReactionCreatedEventData()
  {
    return $this->reactionCreatedEventData;
  }
  /**
   * Event payload for a deleted reaction. Event type:
   * `google.workspace.chat.reaction.v1.deleted`
   *
   * @param ReactionDeletedEventData $reactionDeletedEventData
   */
  public function setReactionDeletedEventData(ReactionDeletedEventData $reactionDeletedEventData)
  {
    $this->reactionDeletedEventData = $reactionDeletedEventData;
  }
  /**
   * @return ReactionDeletedEventData
   */
  public function getReactionDeletedEventData()
  {
    return $this->reactionDeletedEventData;
  }
  /**
   * Event payload for multiple updates to a space. Event type:
   * `google.workspace.chat.space.v1.batchUpdated`
   *
   * @param SpaceBatchUpdatedEventData $spaceBatchUpdatedEventData
   */
  public function setSpaceBatchUpdatedEventData(SpaceBatchUpdatedEventData $spaceBatchUpdatedEventData)
  {
    $this->spaceBatchUpdatedEventData = $spaceBatchUpdatedEventData;
  }
  /**
   * @return SpaceBatchUpdatedEventData
   */
  public function getSpaceBatchUpdatedEventData()
  {
    return $this->spaceBatchUpdatedEventData;
  }
  /**
   * Event payload for a space update. Event type:
   * `google.workspace.chat.space.v1.updated`
   *
   * @param SpaceUpdatedEventData $spaceUpdatedEventData
   */
  public function setSpaceUpdatedEventData(SpaceUpdatedEventData $spaceUpdatedEventData)
  {
    $this->spaceUpdatedEventData = $spaceUpdatedEventData;
  }
  /**
   * @return SpaceUpdatedEventData
   */
  public function getSpaceUpdatedEventData()
  {
    return $this->spaceUpdatedEventData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpaceEvent::class, 'Google_Service_HangoutsChat_SpaceEvent');
