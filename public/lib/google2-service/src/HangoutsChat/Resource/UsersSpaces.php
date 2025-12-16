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

namespace Google\Service\HangoutsChat\Resource;

use Google\Service\HangoutsChat\SpaceReadState;

/**
 * The "spaces" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $spaces = $chatService->users_spaces;
 *  </code>
 */
class UsersSpaces extends \Google\Service\Resource
{
  /**
   * Returns details about a user's read state within a space, used to identify
   * read and unread messages. For an example, see [Get details about a user's
   * space read state](https://developers.google.com/workspace/chat/get-space-
   * read-state). Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.users.readstate.readonly` -
   * `https://www.googleapis.com/auth/chat.users.readstate`
   * (spaces.getSpaceReadState)
   *
   * @param string $name Required. Resource name of the space read state to
   * retrieve. Only supports getting read state for the calling user. To refer to
   * the calling user, set one of the following: - The `me` alias. For example,
   * `users/me/spaces/{space}/spaceReadState`. - Their Workspace email address.
   * For example, `users/user@example.com/spaces/{space}/spaceReadState`. - Their
   * user id. For example, `users/123456789/spaces/{space}/spaceReadState`.
   * Format: users/{user}/spaces/{space}/spaceReadState
   * @param array $optParams Optional parameters.
   * @return SpaceReadState
   * @throws \Google\Service\Exception
   */
  public function getSpaceReadState($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getSpaceReadState', [$params], SpaceReadState::class);
  }
  /**
   * Updates a user's read state within a space, used to identify read and unread
   * messages. For an example, see [Update a user's space read
   * state](https://developers.google.com/workspace/chat/update-space-read-state).
   * Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with the [authorization
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.users.readstate`
   * (spaces.updateSpaceReadState)
   *
   * @param string $name Resource name of the space read state. Format:
   * `users/{user}/spaces/{space}/spaceReadState`
   * @param SpaceReadState $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The field paths to update. Currently
   * supported field paths: - `last_read_time` When the `last_read_time` is before
   * the latest message create time, the space appears as unread in the UI. To
   * mark the space as read, set `last_read_time` to any value later (larger) than
   * the latest message create time. The `last_read_time` is coerced to match the
   * latest message create time. Note that the space read state only affects the
   * read state of messages that are visible in the space's top-level
   * conversation. Replies in threads are unaffected by this timestamp, and
   * instead rely on the thread read state.
   * @return SpaceReadState
   * @throws \Google\Service\Exception
   */
  public function updateSpaceReadState($name, SpaceReadState $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateSpaceReadState', [$params], SpaceReadState::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsersSpaces::class, 'Google_Service_HangoutsChat_Resource_UsersSpaces');
