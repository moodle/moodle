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

class Membership extends \Google\Model
{
  /**
   * Default value. For users: they aren't a member of the space, but can be
   * invited. For Google Groups: they're always assigned this role (other enum
   * values might be used in the future).
   */
  public const ROLE_MEMBERSHIP_ROLE_UNSPECIFIED = 'MEMBERSHIP_ROLE_UNSPECIFIED';
  /**
   * A member of the space. In the Chat UI, this role is called Member. The user
   * has basic permissions, like sending messages to the space. Managers and
   * owners can grant members additional permissions in a space, including: -
   * Add or remove members. - Modify space details. - Turn history on or off. -
   * Mention everyone in the space with `@all`. - Manage Chat apps and webhooks
   * installed in the space. In direct messages and unnamed group conversations,
   * everyone has this role.
   */
  public const ROLE_ROLE_MEMBER = 'ROLE_MEMBER';
  /**
   * A space owner. In the Chat UI, this role is called Owner. The user has the
   * complete set of space permissions to manage the space, including: - Change
   * the role of other members in the space to member, manager, or owner. -
   * Delete the space. Only supported in SpaceType.SPACE (named spaces). To
   * learn more, see [Learn more about your role as a space owner or
   * manager](https://support.google.com/chat/answer/11833441).
   */
  public const ROLE_ROLE_MANAGER = 'ROLE_MANAGER';
  /**
   * A space manager. In the Chat UI, this role is called Manager. The user has
   * all basic permissions of `ROLE_MEMBER`, and can be granted a subset of
   * administrative permissions by an owner. By default, managers have all the
   * permissions of an owner except for the ability to: - Delete the space. -
   * Make another space member an owner. - Change an owner's role. By default,
   * managers permissions include but aren't limited to: - Make another member a
   * manager. - Delete messages in the space. - Manage space permissions. -
   * Receive notifications for requests to join the space if the manager has the
   * "manage members" permission in the space settings. - Make a space
   * discoverable. Only supported in SpaceType.SPACE (named spaces). To learn
   * more, see [Manage space
   * settings](https://support.google.com/chat/answer/13340792).
   */
  public const ROLE_ROLE_ASSISTANT_MANAGER = 'ROLE_ASSISTANT_MANAGER';
  /**
   * Default value. Don't use.
   */
  public const STATE_MEMBERSHIP_STATE_UNSPECIFIED = 'MEMBERSHIP_STATE_UNSPECIFIED';
  /**
   * The user is added to the space, and can participate in the space.
   */
  public const STATE_JOINED = 'JOINED';
  /**
   * The user is invited to join the space, but hasn't joined it.
   */
  public const STATE_INVITED = 'INVITED';
  /**
   * The user doesn't belong to the space and doesn't have a pending invitation
   * to join the space.
   */
  public const STATE_NOT_A_MEMBER = 'NOT_A_MEMBER';
  /**
   * Optional. Immutable. The creation time of the membership, such as when a
   * member joined or was invited to join a space. This field is output only,
   * except when used to import historical memberships in import mode spaces.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Immutable. The deletion time of the membership, such as when a
   * member left or was removed from a space. This field is output only, except
   * when used to import historical memberships in import mode spaces.
   *
   * @var string
   */
  public $deleteTime;
  protected $groupMemberType = Group::class;
  protected $groupMemberDataType = '';
  protected $memberType = User::class;
  protected $memberDataType = '';
  /**
   * Identifier. Resource name of the membership, assigned by the server.
   * Format: `spaces/{space}/members/{member}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. User's role within a Chat space, which determines their permitted
   * actions in the space. This field can only be used as input in
   * `UpdateMembership`.
   *
   * @var string
   */
  public $role;
  /**
   * Output only. State of the membership.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. Immutable. The creation time of the membership, such as when a
   * member joined or was invited to join a space. This field is output only,
   * except when used to import historical memberships in import mode spaces.
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
   * Optional. Immutable. The deletion time of the membership, such as when a
   * member left or was removed from a space. This field is output only, except
   * when used to import historical memberships in import mode spaces.
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
   * Optional. The Google Group the membership corresponds to. Reading or
   * mutating memberships for Google Groups requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user).
   *
   * @param Group $groupMember
   */
  public function setGroupMember(Group $groupMember)
  {
    $this->groupMember = $groupMember;
  }
  /**
   * @return Group
   */
  public function getGroupMember()
  {
    return $this->groupMember;
  }
  /**
   * Optional. The Google Chat user or app the membership corresponds to. If
   * your Chat app [authenticates as a
   * user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user), the output populates the [user](https://developers.google.com/w
   * orkspace/chat/api/reference/rest/v1/User) `name` and `type`.
   *
   * @param User $member
   */
  public function setMember(User $member)
  {
    $this->member = $member;
  }
  /**
   * @return User
   */
  public function getMember()
  {
    return $this->member;
  }
  /**
   * Identifier. Resource name of the membership, assigned by the server.
   * Format: `spaces/{space}/members/{member}`
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
   * Optional. User's role within a Chat space, which determines their permitted
   * actions in the space. This field can only be used as input in
   * `UpdateMembership`.
   *
   * Accepted values: MEMBERSHIP_ROLE_UNSPECIFIED, ROLE_MEMBER, ROLE_MANAGER,
   * ROLE_ASSISTANT_MANAGER
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Output only. State of the membership.
   *
   * Accepted values: MEMBERSHIP_STATE_UNSPECIFIED, JOINED, INVITED,
   * NOT_A_MEMBER
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Membership::class, 'Google_Service_HangoutsChat_Membership');
