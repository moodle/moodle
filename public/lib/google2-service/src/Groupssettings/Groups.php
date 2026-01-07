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

namespace Google\Service\Groupssettings;

class Groups extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "defaultSender" => "default_sender",
  ];
  /**
   * Identifies whether members external to your organization can join the
   * group. Possible values are: - true: G Suite users external to your
   * organization can become members of this group.  - false: Users not
   * belonging to the organization are not allowed to become members of this
   * group.
   *
   * @var string
   */
  public $allowExternalMembers;
  /**
   * Deprecated. Allows Google to contact administrator of the group. - true:
   * Allow Google to contact managers of this group. Occasionally Google may
   * send updates on the latest features, ask for input on new features, or ask
   * for permission to highlight your group.  - false: Google can not contact
   * managers of this group.
   *
   * @var string
   */
  public $allowGoogleCommunication;
  /**
   * Allows posting from web. Possible values are: - true: Allows any member to
   * post to the group forum.  - false: Members only use Gmail to communicate
   * with the group.
   *
   * @var string
   */
  public $allowWebPosting;
  /**
   * Allows the group to be archived only. Possible values are: - true: Group is
   * archived and the group is inactive. New messages to this group are
   * rejected. The older archived messages are browseable and searchable.   - If
   * true, the whoCanPostMessage property is set to NONE_CAN_POST.   - If
   * reverted from true to false, whoCanPostMessages is set to
   * ALL_MANAGERS_CAN_POST.   - false: The group is active and can receive
   * messages.   - When false, updating whoCanPostMessage to NONE_CAN_POST,
   * results in an error.
   *
   * @var string
   */
  public $archiveOnly;
  /**
   * Set the content of custom footer text. The maximum number of characters is
   * 1,000.
   *
   * @var string
   */
  public $customFooterText;
  /**
   * An email address used when replying to a message if the replyTo property is
   * set to REPLY_TO_CUSTOM. This address is defined by an account
   * administrator. - When the group's ReplyTo property is set to
   * REPLY_TO_CUSTOM, the customReplyTo property holds a custom email address
   * used when replying to a message.  - If the group's ReplyTo property is set
   * to REPLY_TO_CUSTOM, the customReplyTo property must have a text value or an
   * error is returned.
   *
   * @var string
   */
  public $customReplyTo;
  /**
   * Specifies whether the group has a custom role that's included in one of the
   * settings being merged. This field is read-only and update/patch requests to
   * it are ignored. Possible values are: - true  - false
   *
   * @var string
   */
  public $customRolesEnabledForSettingsToBeMerged;
  /**
   * When a message is rejected, this is text for the rejection notification
   * sent to the message's author. By default, this property is empty and has no
   * value in the API's response body. The maximum notification text size is
   * 10,000 characters. Note: Requires sendMessageDenyNotification property to
   * be true.
   *
   * @var string
   */
  public $defaultMessageDenyNotificationText;
  /**
   * Default sender for members who can post messages as the group. Possible
   * values are: - `DEFAULT_SELF`: By default messages will be sent from the
   * user - `GROUP`: By default messages will be sent from the group
   *
   * @var string
   */
  public $defaultSender;
  /**
   * Description of the group. This property value may be an empty string if no
   * group description has been entered. If entered, the maximum group
   * description is no more than 300 characters.
   *
   * @var string
   */
  public $description;
  /**
   * The group's email address. This property can be updated using the Directory
   * API. Note: Only a group owner can change a group's email address. A group
   * manager can't do this. When you change your group's address using the
   * Directory API or the control panel, you are changing the address your
   * subscribers use to send email and the web address people use to access your
   * group. People can't reach your group by visiting the old address.
   *
   * @var string
   */
  public $email;
  /**
   * Specifies whether a collaborative inbox will remain turned on for the
   * group. Possible values are: - true  - false
   *
   * @var string
   */
  public $enableCollaborativeInbox;
  /**
   * Indicates if favorite replies should be displayed above other replies. -
   * true: Favorite replies will be displayed above other replies.  - false:
   * Favorite replies will not be displayed above other replies.
   *
   * @var string
   */
  public $favoriteRepliesOnTop;
  /**
   * Whether to include custom footer. Possible values are: - true  - false
   *
   * @var string
   */
  public $includeCustomFooter;
  /**
   * Enables the group to be included in the Global Address List. For more
   * information, see the help center. Possible values are: - true: Group is
   * included in the Global Address List.  - false: Group is not included in the
   * Global Address List.
   *
   * @var string
   */
  public $includeInGlobalAddressList;
  /**
   * Allows the Group contents to be archived. Possible values are: - true:
   * Archive messages sent to the group.  - false: Do not keep an archive of
   * messages sent to this group. If false, previously archived messages remain
   * in the archive.
   *
   * @var string
   */
  public $isArchived;
  /**
   * The type of the resource. It is always groupsSettings#groups.
   *
   * @var string
   */
  public $kind;
  /**
   * Deprecated. The maximum size of a message is 25Mb.
   *
   * @var int
   */
  public $maxMessageBytes;
  /**
   * Enables members to post messages as the group. Possible values are: - true:
   * Group member can post messages using the group's email address instead of
   * their own email address. Message appear to originate from the group itself.
   * Note: When true, any message moderation settings on individual users or new
   * members do not apply to posts made on behalf of the group.  - false:
   * Members can not post in behalf of the group's email address.
   *
   * @var string
   */
  public $membersCanPostAsTheGroup;
  /**
   * Deprecated. The default message display font always has a value of
   * "DEFAULT_FONT".
   *
   * @var string
   */
  public $messageDisplayFont;
  /**
   * Moderation level of incoming messages. Possible values are: -
   * MODERATE_ALL_MESSAGES: All messages are sent to the group owner's email
   * address for approval. If approved, the message is sent to the group.  -
   * MODERATE_NON_MEMBERS: All messages from non group members are sent to the
   * group owner's email address for approval. If approved, the message is sent
   * to the group.  - MODERATE_NEW_MEMBERS: All messages from new members are
   * sent to the group owner's email address for approval. If approved, the
   * message is sent to the group.  - MODERATE_NONE: No moderator approval is
   * required. Messages are delivered directly to the group. Note: When the
   * whoCanPostMessage is set to ANYONE_CAN_POST, we recommend the
   * messageModerationLevel be set to MODERATE_NON_MEMBERS to protect the group
   * from possible spam. When memberCanPostAsTheGroup is true, any message
   * moderation settings on individual users or new members will not apply to
   * posts made on behalf of the group.
   *
   * @var string
   */
  public $messageModerationLevel;
  /**
   * Name of the group, which has a maximum size of 75 characters.
   *
   * @var string
   */
  public $name;
  /**
   * The primary language for group. For a group's primary language use the
   * language tags from the G Suite languages found at G Suite Email Settings
   * API Email Language Tags.
   *
   * @var string
   */
  public $primaryLanguage;
  /**
   * Specifies who receives the default reply. Possible values are: -
   * REPLY_TO_CUSTOM: For replies to messages, use the group's custom email
   * address. When the group's ReplyTo property is set to REPLY_TO_CUSTOM, the
   * customReplyTo property holds the custom email address used when replying to
   * a message. If the group's ReplyTo property is set to REPLY_TO_CUSTOM, the
   * customReplyTo property must have a value. Otherwise an error is returned.
   * - REPLY_TO_SENDER: The reply sent to author of message.  - REPLY_TO_LIST:
   * This reply message is sent to the group.  - REPLY_TO_OWNER: The reply is
   * sent to the owner(s) of the group. This does not include the group's
   * managers.  - REPLY_TO_IGNORE: Group users individually decide where the
   * message reply is sent.  - REPLY_TO_MANAGERS: This reply message is sent to
   * the group's managers, which includes all managers and the group owner.
   *
   * @var string
   */
  public $replyTo;
  /**
   * Allows a member to be notified if the member's message to the group is
   * denied by the group owner. Possible values are: - true: When a message is
   * rejected, send the deny message notification to the message author. The
   * defaultMessageDenyNotificationText property is dependent on the
   * sendMessageDenyNotification property being true.   - false: When a message
   * is rejected, no notification is sent.
   *
   * @var string
   */
  public $sendMessageDenyNotification;
  /**
   * Deprecated. This is merged into the new whoCanDiscoverGroup setting. Allows
   * the group to be visible in the Groups Directory. Possible values are: -
   * true: All groups in the account are listed in the Groups directory.  -
   * false: All groups in the account are not listed in the directory.
   *
   * @var string
   */
  public $showInGroupDirectory;
  /**
   * Specifies moderation levels for messages detected as spam. Possible values
   * are: - ALLOW: Post the message to the group.  - MODERATE: Send the message
   * to the moderation queue. This is the default.  - SILENTLY_MODERATE: Send
   * the message to the moderation queue, but do not send notification to
   * moderators.  - REJECT: Immediately reject the message.
   *
   * @var string
   */
  public $spamModerationLevel;
  /**
   * Deprecated. This is merged into the new whoCanModerateMembers setting.
   * Permissions to add members. Possible values are: - ALL_MEMBERS_CAN_ADD:
   * Managers and members can directly add new members.  - ALL_MANAGERS_CAN_ADD:
   * Only managers can directly add new members. this includes the group's
   * owner.  - ALL_OWNERS_CAN_ADD: Only owners can directly add new members.  -
   * NONE_CAN_ADD: No one can directly add new members.
   *
   * @var string
   */
  public $whoCanAdd;
  /**
   * Deprecated. This functionality is no longer supported in the Google Groups
   * UI. The value is always "NONE".
   *
   * @var string
   */
  public $whoCanAddReferences;
  /**
   * Specifies who can approve members who ask to join groups. This permission
   * will be deprecated once it is merged into the new whoCanModerateMembers
   * setting. Possible values are: - ALL_MEMBERS_CAN_APPROVE  -
   * ALL_MANAGERS_CAN_APPROVE  - ALL_OWNERS_CAN_APPROVE  - NONE_CAN_APPROVE
   *
   * @var string
   */
  public $whoCanApproveMembers;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can approve pending messages in the moderation queue.
   * Possible values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  -
   * NONE
   *
   * @var string
   */
  public $whoCanApproveMessages;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to assign topics in a forum to another user. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY
   * - NONE
   *
   * @var string
   */
  public $whoCanAssignTopics;
  /**
   * Specifies who can moderate metadata. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanAssistContent;
  /**
   * Specifies who can deny membership to users. This permission will be
   * deprecated once it is merged into the new whoCanModerateMembers setting.
   * Possible values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  -
   * NONE
   *
   * @var string
   */
  public $whoCanBanUsers;
  /**
   * Permission to contact owner of the group via web UI. Possible values are: -
   * ALL_IN_DOMAIN_CAN_CONTACT  - ALL_MANAGERS_CAN_CONTACT  -
   * ALL_MEMBERS_CAN_CONTACT  - ANYONE_CAN_CONTACT  - ALL_OWNERS_CAN_CONTACT
   *
   * @var string
   */
  public $whoCanContactOwner;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can delete replies to topics. (Authors can always delete
   * their own posts). Possible values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS
   * - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanDeleteAnyPost;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can delete topics. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanDeleteTopics;
  /**
   * Specifies the set of users for whom this group is discoverable. Possible
   * values are: - ANYONE_CAN_DISCOVER  - ALL_IN_DOMAIN_CAN_DISCOVER  -
   * ALL_MEMBERS_CAN_DISCOVER
   *
   * @var string
   */
  public $whoCanDiscoverGroup;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to enter free form tags for topics in a forum. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY
   * - NONE
   *
   * @var string
   */
  public $whoCanEnterFreeFormTags;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can hide posts by reporting them as abuse. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanHideAbuse;
  /**
   * Deprecated. This is merged into the new whoCanModerateMembers setting.
   * Permissions to invite new members. Possible values are: -
   * ALL_MEMBERS_CAN_INVITE: Managers and members can invite a new member
   * candidate.  - ALL_MANAGERS_CAN_INVITE: Only managers can invite a new
   * member. This includes the group's owner.  - ALL_OWNERS_CAN_INVITE: Only
   * owners can invite a new member.  - NONE_CAN_INVITE: No one can invite a new
   * member candidate.
   *
   * @var string
   */
  public $whoCanInvite;
  /**
   * Permission to join group. Possible values are: - ANYONE_CAN_JOIN: Any
   * Internet user who is outside your domain can access your Google Groups
   * service and view the list of groups in your Groups directory. Warning:
   * Group owners can add external addresses, outside of the domain to their
   * groups. They can also allow people outside your domain to join their
   * groups. If you later disable this option, any external addresses already
   * added to users' groups remain in those groups.  - ALL_IN_DOMAIN_CAN_JOIN:
   * Anyone in the account domain can join. This includes accounts with multiple
   * domains.  - INVITED_CAN_JOIN: Candidates for membership can be invited to
   * join.   - CAN_REQUEST_TO_JOIN: Non members can request an invitation to
   * join.
   *
   * @var string
   */
  public $whoCanJoin;
  /**
   * Permission to leave the group. Possible values are: -
   * ALL_MANAGERS_CAN_LEAVE  - ALL_MEMBERS_CAN_LEAVE  - NONE_CAN_LEAVE
   *
   * @var string
   */
  public $whoCanLeaveGroup;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can prevent users from posting replies to topics. Possible
   * values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanLockTopics;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can make topics appear at the top of the topic list. Possible
   * values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanMakeTopicsSticky;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to mark a topic as a duplicate of another topic. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY
   * - NONE
   *
   * @var string
   */
  public $whoCanMarkDuplicate;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to mark any other user's post as a favorite reply. Possible
   * values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  -
   * OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanMarkFavoriteReplyOnAnyTopic;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to mark a post for a topic they started as a favorite reply.
   * Possible values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY
   * - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanMarkFavoriteReplyOnOwnTopic;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to mark a topic as not needing a response. Possible values are:
   * - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  -
   * NONE
   *
   * @var string
   */
  public $whoCanMarkNoResponseNeeded;
  /**
   * Specifies who can moderate content. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanModerateContent;
  /**
   * Specifies who can manage members. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanModerateMembers;
  /**
   * Deprecated. This is merged into the new whoCanModerateMembers setting.
   * Specifies who can change group members' roles. Possible values are: -
   * ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanModifyMembers;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to change tags and categories. Possible values are: -
   * ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanModifyTagsAndCategories;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can move topics into the group or forum. Possible values are:
   * - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanMoveTopicsIn;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can move topics out of the group or forum. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanMoveTopicsOut;
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can post announcements, a special topic type. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanPostAnnouncements;
  /**
   * Permissions to post messages. Possible values are: - NONE_CAN_POST: The
   * group is disabled and archived. No one can post a message to this group.
   * - When archiveOnly is false, updating whoCanPostMessage to NONE_CAN_POST,
   * results in an error.  - If archiveOnly is reverted from true to false,
   * whoCanPostMessages is set to ALL_MANAGERS_CAN_POST.   -
   * ALL_MANAGERS_CAN_POST: Managers, including group owners, can post messages.
   * - ALL_MEMBERS_CAN_POST: Any group member can post a message.  -
   * ALL_OWNERS_CAN_POST: Only group owners can post a message.  -
   * ALL_IN_DOMAIN_CAN_POST: Anyone in the account can post a message.   -
   * ANYONE_CAN_POST: Any Internet user who outside your account can access your
   * Google Groups service and post a message. Note: When whoCanPostMessage is
   * set to ANYONE_CAN_POST, we recommend the messageModerationLevel be set to
   * MODERATE_NON_MEMBERS to protect the group from possible spam.
   *
   * @var string
   */
  public $whoCanPostMessage;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to take topics in a forum. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanTakeTopics;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to unassign any topic in a forum. Possible values are: -
   * ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanUnassignTopic;
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to unmark any post from a favorite reply. Possible values are: -
   * ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @var string
   */
  public $whoCanUnmarkFavoriteReplyOnAnyTopic;
  /**
   * Permissions to view group messages. Possible values are: - ANYONE_CAN_VIEW:
   * Any Internet user can view the group's messages.   -
   * ALL_IN_DOMAIN_CAN_VIEW: Anyone in your account can view this group's
   * messages.  - ALL_MEMBERS_CAN_VIEW: All group members can view the group's
   * messages.  - ALL_MANAGERS_CAN_VIEW: Any group manager can view this group's
   * messages.
   *
   * @var string
   */
  public $whoCanViewGroup;
  /**
   * Permissions to view membership. Possible values are: -
   * ALL_IN_DOMAIN_CAN_VIEW: Anyone in the account can view the group members
   * list. If a group already has external members, those members can still send
   * email to this group.   - ALL_MEMBERS_CAN_VIEW: The group members can view
   * the group members list.  - ALL_MANAGERS_CAN_VIEW: The group managers can
   * view group members list.
   *
   * @var string
   */
  public $whoCanViewMembership;

  /**
   * Identifies whether members external to your organization can join the
   * group. Possible values are: - true: G Suite users external to your
   * organization can become members of this group.  - false: Users not
   * belonging to the organization are not allowed to become members of this
   * group.
   *
   * @param string $allowExternalMembers
   */
  public function setAllowExternalMembers($allowExternalMembers)
  {
    $this->allowExternalMembers = $allowExternalMembers;
  }
  /**
   * @return string
   */
  public function getAllowExternalMembers()
  {
    return $this->allowExternalMembers;
  }
  /**
   * Deprecated. Allows Google to contact administrator of the group. - true:
   * Allow Google to contact managers of this group. Occasionally Google may
   * send updates on the latest features, ask for input on new features, or ask
   * for permission to highlight your group.  - false: Google can not contact
   * managers of this group.
   *
   * @param string $allowGoogleCommunication
   */
  public function setAllowGoogleCommunication($allowGoogleCommunication)
  {
    $this->allowGoogleCommunication = $allowGoogleCommunication;
  }
  /**
   * @return string
   */
  public function getAllowGoogleCommunication()
  {
    return $this->allowGoogleCommunication;
  }
  /**
   * Allows posting from web. Possible values are: - true: Allows any member to
   * post to the group forum.  - false: Members only use Gmail to communicate
   * with the group.
   *
   * @param string $allowWebPosting
   */
  public function setAllowWebPosting($allowWebPosting)
  {
    $this->allowWebPosting = $allowWebPosting;
  }
  /**
   * @return string
   */
  public function getAllowWebPosting()
  {
    return $this->allowWebPosting;
  }
  /**
   * Allows the group to be archived only. Possible values are: - true: Group is
   * archived and the group is inactive. New messages to this group are
   * rejected. The older archived messages are browseable and searchable.   - If
   * true, the whoCanPostMessage property is set to NONE_CAN_POST.   - If
   * reverted from true to false, whoCanPostMessages is set to
   * ALL_MANAGERS_CAN_POST.   - false: The group is active and can receive
   * messages.   - When false, updating whoCanPostMessage to NONE_CAN_POST,
   * results in an error.
   *
   * @param string $archiveOnly
   */
  public function setArchiveOnly($archiveOnly)
  {
    $this->archiveOnly = $archiveOnly;
  }
  /**
   * @return string
   */
  public function getArchiveOnly()
  {
    return $this->archiveOnly;
  }
  /**
   * Set the content of custom footer text. The maximum number of characters is
   * 1,000.
   *
   * @param string $customFooterText
   */
  public function setCustomFooterText($customFooterText)
  {
    $this->customFooterText = $customFooterText;
  }
  /**
   * @return string
   */
  public function getCustomFooterText()
  {
    return $this->customFooterText;
  }
  /**
   * An email address used when replying to a message if the replyTo property is
   * set to REPLY_TO_CUSTOM. This address is defined by an account
   * administrator. - When the group's ReplyTo property is set to
   * REPLY_TO_CUSTOM, the customReplyTo property holds a custom email address
   * used when replying to a message.  - If the group's ReplyTo property is set
   * to REPLY_TO_CUSTOM, the customReplyTo property must have a text value or an
   * error is returned.
   *
   * @param string $customReplyTo
   */
  public function setCustomReplyTo($customReplyTo)
  {
    $this->customReplyTo = $customReplyTo;
  }
  /**
   * @return string
   */
  public function getCustomReplyTo()
  {
    return $this->customReplyTo;
  }
  /**
   * Specifies whether the group has a custom role that's included in one of the
   * settings being merged. This field is read-only and update/patch requests to
   * it are ignored. Possible values are: - true  - false
   *
   * @param string $customRolesEnabledForSettingsToBeMerged
   */
  public function setCustomRolesEnabledForSettingsToBeMerged($customRolesEnabledForSettingsToBeMerged)
  {
    $this->customRolesEnabledForSettingsToBeMerged = $customRolesEnabledForSettingsToBeMerged;
  }
  /**
   * @return string
   */
  public function getCustomRolesEnabledForSettingsToBeMerged()
  {
    return $this->customRolesEnabledForSettingsToBeMerged;
  }
  /**
   * When a message is rejected, this is text for the rejection notification
   * sent to the message's author. By default, this property is empty and has no
   * value in the API's response body. The maximum notification text size is
   * 10,000 characters. Note: Requires sendMessageDenyNotification property to
   * be true.
   *
   * @param string $defaultMessageDenyNotificationText
   */
  public function setDefaultMessageDenyNotificationText($defaultMessageDenyNotificationText)
  {
    $this->defaultMessageDenyNotificationText = $defaultMessageDenyNotificationText;
  }
  /**
   * @return string
   */
  public function getDefaultMessageDenyNotificationText()
  {
    return $this->defaultMessageDenyNotificationText;
  }
  /**
   * Default sender for members who can post messages as the group. Possible
   * values are: - `DEFAULT_SELF`: By default messages will be sent from the
   * user - `GROUP`: By default messages will be sent from the group
   *
   * @param string $defaultSender
   */
  public function setDefaultSender($defaultSender)
  {
    $this->defaultSender = $defaultSender;
  }
  /**
   * @return string
   */
  public function getDefaultSender()
  {
    return $this->defaultSender;
  }
  /**
   * Description of the group. This property value may be an empty string if no
   * group description has been entered. If entered, the maximum group
   * description is no more than 300 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The group's email address. This property can be updated using the Directory
   * API. Note: Only a group owner can change a group's email address. A group
   * manager can't do this. When you change your group's address using the
   * Directory API or the control panel, you are changing the address your
   * subscribers use to send email and the web address people use to access your
   * group. People can't reach your group by visiting the old address.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Specifies whether a collaborative inbox will remain turned on for the
   * group. Possible values are: - true  - false
   *
   * @param string $enableCollaborativeInbox
   */
  public function setEnableCollaborativeInbox($enableCollaborativeInbox)
  {
    $this->enableCollaborativeInbox = $enableCollaborativeInbox;
  }
  /**
   * @return string
   */
  public function getEnableCollaborativeInbox()
  {
    return $this->enableCollaborativeInbox;
  }
  /**
   * Indicates if favorite replies should be displayed above other replies. -
   * true: Favorite replies will be displayed above other replies.  - false:
   * Favorite replies will not be displayed above other replies.
   *
   * @param string $favoriteRepliesOnTop
   */
  public function setFavoriteRepliesOnTop($favoriteRepliesOnTop)
  {
    $this->favoriteRepliesOnTop = $favoriteRepliesOnTop;
  }
  /**
   * @return string
   */
  public function getFavoriteRepliesOnTop()
  {
    return $this->favoriteRepliesOnTop;
  }
  /**
   * Whether to include custom footer. Possible values are: - true  - false
   *
   * @param string $includeCustomFooter
   */
  public function setIncludeCustomFooter($includeCustomFooter)
  {
    $this->includeCustomFooter = $includeCustomFooter;
  }
  /**
   * @return string
   */
  public function getIncludeCustomFooter()
  {
    return $this->includeCustomFooter;
  }
  /**
   * Enables the group to be included in the Global Address List. For more
   * information, see the help center. Possible values are: - true: Group is
   * included in the Global Address List.  - false: Group is not included in the
   * Global Address List.
   *
   * @param string $includeInGlobalAddressList
   */
  public function setIncludeInGlobalAddressList($includeInGlobalAddressList)
  {
    $this->includeInGlobalAddressList = $includeInGlobalAddressList;
  }
  /**
   * @return string
   */
  public function getIncludeInGlobalAddressList()
  {
    return $this->includeInGlobalAddressList;
  }
  /**
   * Allows the Group contents to be archived. Possible values are: - true:
   * Archive messages sent to the group.  - false: Do not keep an archive of
   * messages sent to this group. If false, previously archived messages remain
   * in the archive.
   *
   * @param string $isArchived
   */
  public function setIsArchived($isArchived)
  {
    $this->isArchived = $isArchived;
  }
  /**
   * @return string
   */
  public function getIsArchived()
  {
    return $this->isArchived;
  }
  /**
   * The type of the resource. It is always groupsSettings#groups.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Deprecated. The maximum size of a message is 25Mb.
   *
   * @param int $maxMessageBytes
   */
  public function setMaxMessageBytes($maxMessageBytes)
  {
    $this->maxMessageBytes = $maxMessageBytes;
  }
  /**
   * @return int
   */
  public function getMaxMessageBytes()
  {
    return $this->maxMessageBytes;
  }
  /**
   * Enables members to post messages as the group. Possible values are: - true:
   * Group member can post messages using the group's email address instead of
   * their own email address. Message appear to originate from the group itself.
   * Note: When true, any message moderation settings on individual users or new
   * members do not apply to posts made on behalf of the group.  - false:
   * Members can not post in behalf of the group's email address.
   *
   * @param string $membersCanPostAsTheGroup
   */
  public function setMembersCanPostAsTheGroup($membersCanPostAsTheGroup)
  {
    $this->membersCanPostAsTheGroup = $membersCanPostAsTheGroup;
  }
  /**
   * @return string
   */
  public function getMembersCanPostAsTheGroup()
  {
    return $this->membersCanPostAsTheGroup;
  }
  /**
   * Deprecated. The default message display font always has a value of
   * "DEFAULT_FONT".
   *
   * @param string $messageDisplayFont
   */
  public function setMessageDisplayFont($messageDisplayFont)
  {
    $this->messageDisplayFont = $messageDisplayFont;
  }
  /**
   * @return string
   */
  public function getMessageDisplayFont()
  {
    return $this->messageDisplayFont;
  }
  /**
   * Moderation level of incoming messages. Possible values are: -
   * MODERATE_ALL_MESSAGES: All messages are sent to the group owner's email
   * address for approval. If approved, the message is sent to the group.  -
   * MODERATE_NON_MEMBERS: All messages from non group members are sent to the
   * group owner's email address for approval. If approved, the message is sent
   * to the group.  - MODERATE_NEW_MEMBERS: All messages from new members are
   * sent to the group owner's email address for approval. If approved, the
   * message is sent to the group.  - MODERATE_NONE: No moderator approval is
   * required. Messages are delivered directly to the group. Note: When the
   * whoCanPostMessage is set to ANYONE_CAN_POST, we recommend the
   * messageModerationLevel be set to MODERATE_NON_MEMBERS to protect the group
   * from possible spam. When memberCanPostAsTheGroup is true, any message
   * moderation settings on individual users or new members will not apply to
   * posts made on behalf of the group.
   *
   * @param string $messageModerationLevel
   */
  public function setMessageModerationLevel($messageModerationLevel)
  {
    $this->messageModerationLevel = $messageModerationLevel;
  }
  /**
   * @return string
   */
  public function getMessageModerationLevel()
  {
    return $this->messageModerationLevel;
  }
  /**
   * Name of the group, which has a maximum size of 75 characters.
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
   * The primary language for group. For a group's primary language use the
   * language tags from the G Suite languages found at G Suite Email Settings
   * API Email Language Tags.
   *
   * @param string $primaryLanguage
   */
  public function setPrimaryLanguage($primaryLanguage)
  {
    $this->primaryLanguage = $primaryLanguage;
  }
  /**
   * @return string
   */
  public function getPrimaryLanguage()
  {
    return $this->primaryLanguage;
  }
  /**
   * Specifies who receives the default reply. Possible values are: -
   * REPLY_TO_CUSTOM: For replies to messages, use the group's custom email
   * address. When the group's ReplyTo property is set to REPLY_TO_CUSTOM, the
   * customReplyTo property holds the custom email address used when replying to
   * a message. If the group's ReplyTo property is set to REPLY_TO_CUSTOM, the
   * customReplyTo property must have a value. Otherwise an error is returned.
   * - REPLY_TO_SENDER: The reply sent to author of message.  - REPLY_TO_LIST:
   * This reply message is sent to the group.  - REPLY_TO_OWNER: The reply is
   * sent to the owner(s) of the group. This does not include the group's
   * managers.  - REPLY_TO_IGNORE: Group users individually decide where the
   * message reply is sent.  - REPLY_TO_MANAGERS: This reply message is sent to
   * the group's managers, which includes all managers and the group owner.
   *
   * @param string $replyTo
   */
  public function setReplyTo($replyTo)
  {
    $this->replyTo = $replyTo;
  }
  /**
   * @return string
   */
  public function getReplyTo()
  {
    return $this->replyTo;
  }
  /**
   * Allows a member to be notified if the member's message to the group is
   * denied by the group owner. Possible values are: - true: When a message is
   * rejected, send the deny message notification to the message author. The
   * defaultMessageDenyNotificationText property is dependent on the
   * sendMessageDenyNotification property being true.   - false: When a message
   * is rejected, no notification is sent.
   *
   * @param string $sendMessageDenyNotification
   */
  public function setSendMessageDenyNotification($sendMessageDenyNotification)
  {
    $this->sendMessageDenyNotification = $sendMessageDenyNotification;
  }
  /**
   * @return string
   */
  public function getSendMessageDenyNotification()
  {
    return $this->sendMessageDenyNotification;
  }
  /**
   * Deprecated. This is merged into the new whoCanDiscoverGroup setting. Allows
   * the group to be visible in the Groups Directory. Possible values are: -
   * true: All groups in the account are listed in the Groups directory.  -
   * false: All groups in the account are not listed in the directory.
   *
   * @param string $showInGroupDirectory
   */
  public function setShowInGroupDirectory($showInGroupDirectory)
  {
    $this->showInGroupDirectory = $showInGroupDirectory;
  }
  /**
   * @return string
   */
  public function getShowInGroupDirectory()
  {
    return $this->showInGroupDirectory;
  }
  /**
   * Specifies moderation levels for messages detected as spam. Possible values
   * are: - ALLOW: Post the message to the group.  - MODERATE: Send the message
   * to the moderation queue. This is the default.  - SILENTLY_MODERATE: Send
   * the message to the moderation queue, but do not send notification to
   * moderators.  - REJECT: Immediately reject the message.
   *
   * @param string $spamModerationLevel
   */
  public function setSpamModerationLevel($spamModerationLevel)
  {
    $this->spamModerationLevel = $spamModerationLevel;
  }
  /**
   * @return string
   */
  public function getSpamModerationLevel()
  {
    return $this->spamModerationLevel;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateMembers setting.
   * Permissions to add members. Possible values are: - ALL_MEMBERS_CAN_ADD:
   * Managers and members can directly add new members.  - ALL_MANAGERS_CAN_ADD:
   * Only managers can directly add new members. this includes the group's
   * owner.  - ALL_OWNERS_CAN_ADD: Only owners can directly add new members.  -
   * NONE_CAN_ADD: No one can directly add new members.
   *
   * @param string $whoCanAdd
   */
  public function setWhoCanAdd($whoCanAdd)
  {
    $this->whoCanAdd = $whoCanAdd;
  }
  /**
   * @return string
   */
  public function getWhoCanAdd()
  {
    return $this->whoCanAdd;
  }
  /**
   * Deprecated. This functionality is no longer supported in the Google Groups
   * UI. The value is always "NONE".
   *
   * @param string $whoCanAddReferences
   */
  public function setWhoCanAddReferences($whoCanAddReferences)
  {
    $this->whoCanAddReferences = $whoCanAddReferences;
  }
  /**
   * @return string
   */
  public function getWhoCanAddReferences()
  {
    return $this->whoCanAddReferences;
  }
  /**
   * Specifies who can approve members who ask to join groups. This permission
   * will be deprecated once it is merged into the new whoCanModerateMembers
   * setting. Possible values are: - ALL_MEMBERS_CAN_APPROVE  -
   * ALL_MANAGERS_CAN_APPROVE  - ALL_OWNERS_CAN_APPROVE  - NONE_CAN_APPROVE
   *
   * @param string $whoCanApproveMembers
   */
  public function setWhoCanApproveMembers($whoCanApproveMembers)
  {
    $this->whoCanApproveMembers = $whoCanApproveMembers;
  }
  /**
   * @return string
   */
  public function getWhoCanApproveMembers()
  {
    return $this->whoCanApproveMembers;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can approve pending messages in the moderation queue.
   * Possible values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  -
   * NONE
   *
   * @param string $whoCanApproveMessages
   */
  public function setWhoCanApproveMessages($whoCanApproveMessages)
  {
    $this->whoCanApproveMessages = $whoCanApproveMessages;
  }
  /**
   * @return string
   */
  public function getWhoCanApproveMessages()
  {
    return $this->whoCanApproveMessages;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to assign topics in a forum to another user. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY
   * - NONE
   *
   * @param string $whoCanAssignTopics
   */
  public function setWhoCanAssignTopics($whoCanAssignTopics)
  {
    $this->whoCanAssignTopics = $whoCanAssignTopics;
  }
  /**
   * @return string
   */
  public function getWhoCanAssignTopics()
  {
    return $this->whoCanAssignTopics;
  }
  /**
   * Specifies who can moderate metadata. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanAssistContent
   */
  public function setWhoCanAssistContent($whoCanAssistContent)
  {
    $this->whoCanAssistContent = $whoCanAssistContent;
  }
  /**
   * @return string
   */
  public function getWhoCanAssistContent()
  {
    return $this->whoCanAssistContent;
  }
  /**
   * Specifies who can deny membership to users. This permission will be
   * deprecated once it is merged into the new whoCanModerateMembers setting.
   * Possible values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  -
   * NONE
   *
   * @param string $whoCanBanUsers
   */
  public function setWhoCanBanUsers($whoCanBanUsers)
  {
    $this->whoCanBanUsers = $whoCanBanUsers;
  }
  /**
   * @return string
   */
  public function getWhoCanBanUsers()
  {
    return $this->whoCanBanUsers;
  }
  /**
   * Permission to contact owner of the group via web UI. Possible values are: -
   * ALL_IN_DOMAIN_CAN_CONTACT  - ALL_MANAGERS_CAN_CONTACT  -
   * ALL_MEMBERS_CAN_CONTACT  - ANYONE_CAN_CONTACT  - ALL_OWNERS_CAN_CONTACT
   *
   * @param string $whoCanContactOwner
   */
  public function setWhoCanContactOwner($whoCanContactOwner)
  {
    $this->whoCanContactOwner = $whoCanContactOwner;
  }
  /**
   * @return string
   */
  public function getWhoCanContactOwner()
  {
    return $this->whoCanContactOwner;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can delete replies to topics. (Authors can always delete
   * their own posts). Possible values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS
   * - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanDeleteAnyPost
   */
  public function setWhoCanDeleteAnyPost($whoCanDeleteAnyPost)
  {
    $this->whoCanDeleteAnyPost = $whoCanDeleteAnyPost;
  }
  /**
   * @return string
   */
  public function getWhoCanDeleteAnyPost()
  {
    return $this->whoCanDeleteAnyPost;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can delete topics. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanDeleteTopics
   */
  public function setWhoCanDeleteTopics($whoCanDeleteTopics)
  {
    $this->whoCanDeleteTopics = $whoCanDeleteTopics;
  }
  /**
   * @return string
   */
  public function getWhoCanDeleteTopics()
  {
    return $this->whoCanDeleteTopics;
  }
  /**
   * Specifies the set of users for whom this group is discoverable. Possible
   * values are: - ANYONE_CAN_DISCOVER  - ALL_IN_DOMAIN_CAN_DISCOVER  -
   * ALL_MEMBERS_CAN_DISCOVER
   *
   * @param string $whoCanDiscoverGroup
   */
  public function setWhoCanDiscoverGroup($whoCanDiscoverGroup)
  {
    $this->whoCanDiscoverGroup = $whoCanDiscoverGroup;
  }
  /**
   * @return string
   */
  public function getWhoCanDiscoverGroup()
  {
    return $this->whoCanDiscoverGroup;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to enter free form tags for topics in a forum. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY
   * - NONE
   *
   * @param string $whoCanEnterFreeFormTags
   */
  public function setWhoCanEnterFreeFormTags($whoCanEnterFreeFormTags)
  {
    $this->whoCanEnterFreeFormTags = $whoCanEnterFreeFormTags;
  }
  /**
   * @return string
   */
  public function getWhoCanEnterFreeFormTags()
  {
    return $this->whoCanEnterFreeFormTags;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can hide posts by reporting them as abuse. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanHideAbuse
   */
  public function setWhoCanHideAbuse($whoCanHideAbuse)
  {
    $this->whoCanHideAbuse = $whoCanHideAbuse;
  }
  /**
   * @return string
   */
  public function getWhoCanHideAbuse()
  {
    return $this->whoCanHideAbuse;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateMembers setting.
   * Permissions to invite new members. Possible values are: -
   * ALL_MEMBERS_CAN_INVITE: Managers and members can invite a new member
   * candidate.  - ALL_MANAGERS_CAN_INVITE: Only managers can invite a new
   * member. This includes the group's owner.  - ALL_OWNERS_CAN_INVITE: Only
   * owners can invite a new member.  - NONE_CAN_INVITE: No one can invite a new
   * member candidate.
   *
   * @param string $whoCanInvite
   */
  public function setWhoCanInvite($whoCanInvite)
  {
    $this->whoCanInvite = $whoCanInvite;
  }
  /**
   * @return string
   */
  public function getWhoCanInvite()
  {
    return $this->whoCanInvite;
  }
  /**
   * Permission to join group. Possible values are: - ANYONE_CAN_JOIN: Any
   * Internet user who is outside your domain can access your Google Groups
   * service and view the list of groups in your Groups directory. Warning:
   * Group owners can add external addresses, outside of the domain to their
   * groups. They can also allow people outside your domain to join their
   * groups. If you later disable this option, any external addresses already
   * added to users' groups remain in those groups.  - ALL_IN_DOMAIN_CAN_JOIN:
   * Anyone in the account domain can join. This includes accounts with multiple
   * domains.  - INVITED_CAN_JOIN: Candidates for membership can be invited to
   * join.   - CAN_REQUEST_TO_JOIN: Non members can request an invitation to
   * join.
   *
   * @param string $whoCanJoin
   */
  public function setWhoCanJoin($whoCanJoin)
  {
    $this->whoCanJoin = $whoCanJoin;
  }
  /**
   * @return string
   */
  public function getWhoCanJoin()
  {
    return $this->whoCanJoin;
  }
  /**
   * Permission to leave the group. Possible values are: -
   * ALL_MANAGERS_CAN_LEAVE  - ALL_MEMBERS_CAN_LEAVE  - NONE_CAN_LEAVE
   *
   * @param string $whoCanLeaveGroup
   */
  public function setWhoCanLeaveGroup($whoCanLeaveGroup)
  {
    $this->whoCanLeaveGroup = $whoCanLeaveGroup;
  }
  /**
   * @return string
   */
  public function getWhoCanLeaveGroup()
  {
    return $this->whoCanLeaveGroup;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can prevent users from posting replies to topics. Possible
   * values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanLockTopics
   */
  public function setWhoCanLockTopics($whoCanLockTopics)
  {
    $this->whoCanLockTopics = $whoCanLockTopics;
  }
  /**
   * @return string
   */
  public function getWhoCanLockTopics()
  {
    return $this->whoCanLockTopics;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can make topics appear at the top of the topic list. Possible
   * values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanMakeTopicsSticky
   */
  public function setWhoCanMakeTopicsSticky($whoCanMakeTopicsSticky)
  {
    $this->whoCanMakeTopicsSticky = $whoCanMakeTopicsSticky;
  }
  /**
   * @return string
   */
  public function getWhoCanMakeTopicsSticky()
  {
    return $this->whoCanMakeTopicsSticky;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to mark a topic as a duplicate of another topic. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY
   * - NONE
   *
   * @param string $whoCanMarkDuplicate
   */
  public function setWhoCanMarkDuplicate($whoCanMarkDuplicate)
  {
    $this->whoCanMarkDuplicate = $whoCanMarkDuplicate;
  }
  /**
   * @return string
   */
  public function getWhoCanMarkDuplicate()
  {
    return $this->whoCanMarkDuplicate;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to mark any other user's post as a favorite reply. Possible
   * values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  -
   * OWNERS_ONLY  - NONE
   *
   * @param string $whoCanMarkFavoriteReplyOnAnyTopic
   */
  public function setWhoCanMarkFavoriteReplyOnAnyTopic($whoCanMarkFavoriteReplyOnAnyTopic)
  {
    $this->whoCanMarkFavoriteReplyOnAnyTopic = $whoCanMarkFavoriteReplyOnAnyTopic;
  }
  /**
   * @return string
   */
  public function getWhoCanMarkFavoriteReplyOnAnyTopic()
  {
    return $this->whoCanMarkFavoriteReplyOnAnyTopic;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to mark a post for a topic they started as a favorite reply.
   * Possible values are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY
   * - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanMarkFavoriteReplyOnOwnTopic
   */
  public function setWhoCanMarkFavoriteReplyOnOwnTopic($whoCanMarkFavoriteReplyOnOwnTopic)
  {
    $this->whoCanMarkFavoriteReplyOnOwnTopic = $whoCanMarkFavoriteReplyOnOwnTopic;
  }
  /**
   * @return string
   */
  public function getWhoCanMarkFavoriteReplyOnOwnTopic()
  {
    return $this->whoCanMarkFavoriteReplyOnOwnTopic;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to mark a topic as not needing a response. Possible values are:
   * - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  -
   * NONE
   *
   * @param string $whoCanMarkNoResponseNeeded
   */
  public function setWhoCanMarkNoResponseNeeded($whoCanMarkNoResponseNeeded)
  {
    $this->whoCanMarkNoResponseNeeded = $whoCanMarkNoResponseNeeded;
  }
  /**
   * @return string
   */
  public function getWhoCanMarkNoResponseNeeded()
  {
    return $this->whoCanMarkNoResponseNeeded;
  }
  /**
   * Specifies who can moderate content. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanModerateContent
   */
  public function setWhoCanModerateContent($whoCanModerateContent)
  {
    $this->whoCanModerateContent = $whoCanModerateContent;
  }
  /**
   * @return string
   */
  public function getWhoCanModerateContent()
  {
    return $this->whoCanModerateContent;
  }
  /**
   * Specifies who can manage members. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanModerateMembers
   */
  public function setWhoCanModerateMembers($whoCanModerateMembers)
  {
    $this->whoCanModerateMembers = $whoCanModerateMembers;
  }
  /**
   * @return string
   */
  public function getWhoCanModerateMembers()
  {
    return $this->whoCanModerateMembers;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateMembers setting.
   * Specifies who can change group members' roles. Possible values are: -
   * ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanModifyMembers
   */
  public function setWhoCanModifyMembers($whoCanModifyMembers)
  {
    $this->whoCanModifyMembers = $whoCanModifyMembers;
  }
  /**
   * @return string
   */
  public function getWhoCanModifyMembers()
  {
    return $this->whoCanModifyMembers;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to change tags and categories. Possible values are: -
   * ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanModifyTagsAndCategories
   */
  public function setWhoCanModifyTagsAndCategories($whoCanModifyTagsAndCategories)
  {
    $this->whoCanModifyTagsAndCategories = $whoCanModifyTagsAndCategories;
  }
  /**
   * @return string
   */
  public function getWhoCanModifyTagsAndCategories()
  {
    return $this->whoCanModifyTagsAndCategories;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can move topics into the group or forum. Possible values are:
   * - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanMoveTopicsIn
   */
  public function setWhoCanMoveTopicsIn($whoCanMoveTopicsIn)
  {
    $this->whoCanMoveTopicsIn = $whoCanMoveTopicsIn;
  }
  /**
   * @return string
   */
  public function getWhoCanMoveTopicsIn()
  {
    return $this->whoCanMoveTopicsIn;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can move topics out of the group or forum. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanMoveTopicsOut
   */
  public function setWhoCanMoveTopicsOut($whoCanMoveTopicsOut)
  {
    $this->whoCanMoveTopicsOut = $whoCanMoveTopicsOut;
  }
  /**
   * @return string
   */
  public function getWhoCanMoveTopicsOut()
  {
    return $this->whoCanMoveTopicsOut;
  }
  /**
   * Deprecated. This is merged into the new whoCanModerateContent setting.
   * Specifies who can post announcements, a special topic type. Possible values
   * are: - ALL_MEMBERS  - OWNERS_AND_MANAGERS  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanPostAnnouncements
   */
  public function setWhoCanPostAnnouncements($whoCanPostAnnouncements)
  {
    $this->whoCanPostAnnouncements = $whoCanPostAnnouncements;
  }
  /**
   * @return string
   */
  public function getWhoCanPostAnnouncements()
  {
    return $this->whoCanPostAnnouncements;
  }
  /**
   * Permissions to post messages. Possible values are: - NONE_CAN_POST: The
   * group is disabled and archived. No one can post a message to this group.
   * - When archiveOnly is false, updating whoCanPostMessage to NONE_CAN_POST,
   * results in an error.  - If archiveOnly is reverted from true to false,
   * whoCanPostMessages is set to ALL_MANAGERS_CAN_POST.   -
   * ALL_MANAGERS_CAN_POST: Managers, including group owners, can post messages.
   * - ALL_MEMBERS_CAN_POST: Any group member can post a message.  -
   * ALL_OWNERS_CAN_POST: Only group owners can post a message.  -
   * ALL_IN_DOMAIN_CAN_POST: Anyone in the account can post a message.   -
   * ANYONE_CAN_POST: Any Internet user who outside your account can access your
   * Google Groups service and post a message. Note: When whoCanPostMessage is
   * set to ANYONE_CAN_POST, we recommend the messageModerationLevel be set to
   * MODERATE_NON_MEMBERS to protect the group from possible spam.
   *
   * @param string $whoCanPostMessage
   */
  public function setWhoCanPostMessage($whoCanPostMessage)
  {
    $this->whoCanPostMessage = $whoCanPostMessage;
  }
  /**
   * @return string
   */
  public function getWhoCanPostMessage()
  {
    return $this->whoCanPostMessage;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to take topics in a forum. Possible values are: - ALL_MEMBERS  -
   * OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanTakeTopics
   */
  public function setWhoCanTakeTopics($whoCanTakeTopics)
  {
    $this->whoCanTakeTopics = $whoCanTakeTopics;
  }
  /**
   * @return string
   */
  public function getWhoCanTakeTopics()
  {
    return $this->whoCanTakeTopics;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to unassign any topic in a forum. Possible values are: -
   * ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanUnassignTopic
   */
  public function setWhoCanUnassignTopic($whoCanUnassignTopic)
  {
    $this->whoCanUnassignTopic = $whoCanUnassignTopic;
  }
  /**
   * @return string
   */
  public function getWhoCanUnassignTopic()
  {
    return $this->whoCanUnassignTopic;
  }
  /**
   * Deprecated. This is merged into the new whoCanAssistContent setting.
   * Permission to unmark any post from a favorite reply. Possible values are: -
   * ALL_MEMBERS  - OWNERS_AND_MANAGERS  - MANAGERS_ONLY  - OWNERS_ONLY  - NONE
   *
   * @param string $whoCanUnmarkFavoriteReplyOnAnyTopic
   */
  public function setWhoCanUnmarkFavoriteReplyOnAnyTopic($whoCanUnmarkFavoriteReplyOnAnyTopic)
  {
    $this->whoCanUnmarkFavoriteReplyOnAnyTopic = $whoCanUnmarkFavoriteReplyOnAnyTopic;
  }
  /**
   * @return string
   */
  public function getWhoCanUnmarkFavoriteReplyOnAnyTopic()
  {
    return $this->whoCanUnmarkFavoriteReplyOnAnyTopic;
  }
  /**
   * Permissions to view group messages. Possible values are: - ANYONE_CAN_VIEW:
   * Any Internet user can view the group's messages.   -
   * ALL_IN_DOMAIN_CAN_VIEW: Anyone in your account can view this group's
   * messages.  - ALL_MEMBERS_CAN_VIEW: All group members can view the group's
   * messages.  - ALL_MANAGERS_CAN_VIEW: Any group manager can view this group's
   * messages.
   *
   * @param string $whoCanViewGroup
   */
  public function setWhoCanViewGroup($whoCanViewGroup)
  {
    $this->whoCanViewGroup = $whoCanViewGroup;
  }
  /**
   * @return string
   */
  public function getWhoCanViewGroup()
  {
    return $this->whoCanViewGroup;
  }
  /**
   * Permissions to view membership. Possible values are: -
   * ALL_IN_DOMAIN_CAN_VIEW: Anyone in the account can view the group members
   * list. If a group already has external members, those members can still send
   * email to this group.   - ALL_MEMBERS_CAN_VIEW: The group members can view
   * the group members list.  - ALL_MANAGERS_CAN_VIEW: The group managers can
   * view group members list.
   *
   * @param string $whoCanViewMembership
   */
  public function setWhoCanViewMembership($whoCanViewMembership)
  {
    $this->whoCanViewMembership = $whoCanViewMembership;
  }
  /**
   * @return string
   */
  public function getWhoCanViewMembership()
  {
    return $this->whoCanViewMembership;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Groups::class, 'Google_Service_Groupssettings_Groups');
