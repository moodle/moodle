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

use Google\Service\HangoutsChat\ChatEmpty;
use Google\Service\HangoutsChat\ListReactionsResponse;
use Google\Service\HangoutsChat\Reaction;

/**
 * The "reactions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $reactions = $chatService->spaces_messages_reactions;
 *  </code>
 */
class SpacesMessagesReactions extends \Google\Service\Resource
{
  /**
   * Creates a reaction and adds it to a message. For an example, see [Add a
   * reaction to a message](https://developers.google.com/workspace/chat/create-
   * reactions). Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.messages.reactions.create` -
   * `https://www.googleapis.com/auth/chat.messages.reactions` -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only)
   * (reactions.create)
   *
   * @param string $parent Required. The message where the reaction is created.
   * Format: `spaces/{space}/messages/{message}`
   * @param Reaction $postBody
   * @param array $optParams Optional parameters.
   * @return Reaction
   * @throws \Google\Service\Exception
   */
  public function create($parent, Reaction $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Reaction::class);
  }
  /**
   * Deletes a reaction to a message. For an example, see [Delete a
   * reaction](https://developers.google.com/workspace/chat/delete-reactions).
   * Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.messages.reactions` -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only)
   * (reactions.delete)
   *
   * @param string $name Required. Name of the reaction to delete. Format:
   * `spaces/{space}/messages/{message}/reactions/{reaction}`
   * @param array $optParams Optional parameters.
   * @return ChatEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ChatEmpty::class);
  }
  /**
   * Lists reactions to a message. For an example, see [List reactions for a
   * message](https://developers.google.com/workspace/chat/list-reactions).
   * Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.messages.reactions.readonly` -
   * `https://www.googleapis.com/auth/chat.messages.reactions` -
   * `https://www.googleapis.com/auth/chat.messages.readonly` -
   * `https://www.googleapis.com/auth/chat.messages`
   * (reactions.listSpacesMessagesReactions)
   *
   * @param string $parent Required. The message users reacted to. Format:
   * `spaces/{space}/messages/{message}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A query filter. You can filter reactions
   * by [emoji](https://developers.google.com/workspace/chat/api/reference/rest/v1
   * /Emoji) (either `emoji.unicode` or `emoji.custom_emoji.uid`) and [user](https
   * ://developers.google.com/workspace/chat/api/reference/rest/v1/User)
   * (`user.name`). To filter reactions for multiple emojis or users, join similar
   * fields with the `OR` operator, such as `emoji.unicode = "🙂" OR emoji.unicode
   * = "👍"` and `user.name = "users/AAAAAA" OR user.name = "users/BBBBBB"`. To
   * filter reactions by emoji and user, use the `AND` operator, such as
   * `emoji.unicode = "🙂" AND user.name = "users/AAAAAA"`. If your query uses both
   * `AND` and `OR`, group them with parentheses. For example, the following
   * queries are valid: ``` user.name = "users/{user}" emoji.unicode = "🙂"
   * emoji.custom_emoji.uid = "{uid}" emoji.unicode = "🙂" OR emoji.unicode = "👍"
   * emoji.unicode = "🙂" OR emoji.custom_emoji.uid = "{uid}" emoji.unicode = "🙂"
   * AND user.name = "users/{user}" (emoji.unicode = "🙂" OR emoji.custom_emoji.uid
   * = "{uid}") AND user.name = "users/{user}" ``` The following queries are
   * invalid: ``` emoji.unicode = "🙂" AND emoji.unicode = "👍" emoji.unicode = "🙂"
   * AND emoji.custom_emoji.uid = "{uid}" emoji.unicode = "🙂" OR user.name =
   * "users/{user}" emoji.unicode = "🙂" OR emoji.custom_emoji.uid = "{uid}" OR
   * user.name = "users/{user}" emoji.unicode = "🙂" OR emoji.custom_emoji.uid =
   * "{uid}" AND user.name = "users/{user}" ``` Invalid queries are rejected with
   * an `INVALID_ARGUMENT` error.
   * @opt_param int pageSize Optional. The maximum number of reactions returned.
   * The service can return fewer reactions than this value. If unspecified, the
   * default value is 25. The maximum value is 200; values above 200 are changed
   * to 200.
   * @opt_param string pageToken Optional. (If resuming from a previous query.) A
   * page token received from a previous list reactions call. Provide this to
   * retrieve the subsequent page. When paginating, the filter value should match
   * the call that provided the page token. Passing a different value might lead
   * to unexpected results.
   * @return ListReactionsResponse
   * @throws \Google\Service\Exception
   */
  public function listSpacesMessagesReactions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListReactionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpacesMessagesReactions::class, 'Google_Service_HangoutsChat_Resource_SpacesMessagesReactions');
