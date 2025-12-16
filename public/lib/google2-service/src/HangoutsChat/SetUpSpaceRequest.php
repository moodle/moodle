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

class SetUpSpaceRequest extends \Google\Collection
{
  protected $collection_key = 'memberships';
  protected $membershipsType = Membership::class;
  protected $membershipsDataType = 'array';
  /**
   * Optional. A unique identifier for this request. A random UUID is
   * recommended. Specifying an existing request ID returns the space created
   * with that ID instead of creating a new space. Specifying an existing
   * request ID from the same Chat app with a different authenticated user
   * returns an error.
   *
   * @var string
   */
  public $requestId;
  protected $spaceType = Space::class;
  protected $spaceDataType = '';

  /**
   * Optional. The Google Chat users or groups to invite to join the space. Omit
   * the calling user, as they are added automatically. The set currently allows
   * up to 49 memberships (in addition to the caller). For human membership, the
   * `Membership.member` field must contain a `user` with `name` populated
   * (format: `users/{user}`) and `type` set to `User.Type.HUMAN`. You can only
   * add human users when setting up a space (adding Chat apps is only supported
   * for direct message setup with the calling app). You can also add members
   * using the user's email as an alias for {user}. For example, the `user.name`
   * can be `users/example@gmail.com`. To invite Gmail users or users from
   * external Google Workspace domains, user's email must be used for `{user}`.
   * For Google group membership, the `Membership.group_member` field must
   * contain a `group` with `name` populated (format `groups/{group}`). You can
   * only add Google groups when setting `Space.spaceType` to `SPACE`. Optional
   * when setting `Space.spaceType` to `SPACE`. Required when setting
   * `Space.spaceType` to `GROUP_CHAT`, along with at least two memberships.
   * Required when setting `Space.spaceType` to `DIRECT_MESSAGE` with a human
   * user, along with exactly one membership. Must be empty when creating a 1:1
   * conversation between a human and the calling Chat app (when setting
   * `Space.spaceType` to `DIRECT_MESSAGE` and `Space.singleUserBotDm` to
   * `true`).
   *
   * @param Membership[] $memberships
   */
  public function setMemberships($memberships)
  {
    $this->memberships = $memberships;
  }
  /**
   * @return Membership[]
   */
  public function getMemberships()
  {
    return $this->memberships;
  }
  /**
   * Optional. A unique identifier for this request. A random UUID is
   * recommended. Specifying an existing request ID returns the space created
   * with that ID instead of creating a new space. Specifying an existing
   * request ID from the same Chat app with a different authenticated user
   * returns an error.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Required. The `Space.spaceType` field is required. To create a space, set
   * `Space.spaceType` to `SPACE` and set `Space.displayName`. If you receive
   * the error message `ALREADY_EXISTS` when setting up a space, try a different
   * `displayName`. An existing space within the Google Workspace organization
   * might already use this display name. To create a group chat, set
   * `Space.spaceType` to `GROUP_CHAT`. Don't set `Space.displayName`. To create
   * a 1:1 conversation between humans, set `Space.spaceType` to
   * `DIRECT_MESSAGE` and set `Space.singleUserBotDm` to `false`. Don't set
   * `Space.displayName` or `Space.spaceDetails`. To create an 1:1 conversation
   * between a human and the calling Chat app, set `Space.spaceType` to
   * `DIRECT_MESSAGE` and `Space.singleUserBotDm` to `true`. Don't set
   * `Space.displayName` or `Space.spaceDetails`. If a `DIRECT_MESSAGE` space
   * already exists, that space is returned instead of creating a new space.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetUpSpaceRequest::class, 'Google_Service_HangoutsChat_SetUpSpaceRequest');
