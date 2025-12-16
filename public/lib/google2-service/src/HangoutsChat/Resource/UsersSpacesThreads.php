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

use Google\Service\HangoutsChat\ThreadReadState;

/**
 * The "threads" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $threads = $chatService->users_spaces_threads;
 *  </code>
 */
class UsersSpacesThreads extends \Google\Service\Resource
{
  /**
   * Returns details about a user's read state within a thread, used to identify
   * read and unread messages. For an example, see [Get details about a user's
   * thread read state](https://developers.google.com/workspace/chat/get-thread-
   * read-state). Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.users.readstate.readonly` -
   * `https://www.googleapis.com/auth/chat.users.readstate`
   * (threads.getThreadReadState)
   *
   * @param string $name Required. Resource name of the thread read state to
   * retrieve. Only supports getting read state for the calling user. To refer to
   * the calling user, set one of the following: - The `me` alias. For example,
   * `users/me/spaces/{space}/threads/{thread}/threadReadState`. - Their Workspace
   * email address. For example,
   * `users/user@example.com/spaces/{space}/threads/{thread}/threadReadState`. -
   * Their user id. For example,
   * `users/123456789/spaces/{space}/threads/{thread}/threadReadState`. Format:
   * users/{user}/spaces/{space}/threads/{thread}/threadReadState
   * @param array $optParams Optional parameters.
   * @return ThreadReadState
   * @throws \Google\Service\Exception
   */
  public function getThreadReadState($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getThreadReadState', [$params], ThreadReadState::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsersSpacesThreads::class, 'Google_Service_HangoutsChat_Resource_UsersSpacesThreads');
