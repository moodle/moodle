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
use Google\Service\HangoutsChat\CustomEmoji;
use Google\Service\HangoutsChat\ListCustomEmojisResponse;

/**
 * The "customEmojis" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $customEmojis = $chatService->customEmojis;
 *  </code>
 */
class CustomEmojis extends \Google\Service\Resource
{
  /**
   * Creates a custom emoji. Custom emojis are only available for Google Workspace
   * accounts, and the administrator must turn custom emojis on for the
   * organization. For more information, see [Learn about custom emojis in Google
   * Chat](https://support.google.com/chat/answer/12800149) and [Manage custom
   * emoji permissions](https://support.google.com/a/answer/12850085). Requires
   * [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with the [authorization
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.customemojis` (customEmojis.create)
   *
   * @param CustomEmoji $postBody
   * @param array $optParams Optional parameters.
   * @return CustomEmoji
   * @throws \Google\Service\Exception
   */
  public function create(CustomEmoji $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CustomEmoji::class);
  }
  /**
   * Deletes a custom emoji. By default, users can only delete custom emoji they
   * created. [Emoji managers](https://support.google.com/a/answer/12850085)
   * assigned by the administrator can delete any custom emoji in the
   * organization. See [Learn about custom emojis in Google
   * Chat](https://support.google.com/chat/answer/12800149). Custom emojis are
   * only available for Google Workspace accounts, and the administrator must turn
   * custom emojis on for the organization. For more information, see [Learn about
   * custom emojis in Google
   * Chat](https://support.google.com/chat/answer/12800149) and [Manage custom
   * emoji permissions](https://support.google.com/a/answer/12850085). Requires
   * [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with the [authorization
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.customemojis` (customEmojis.delete)
   *
   * @param string $name Required. Resource name of the custom emoji to delete.
   * Format: `customEmojis/{customEmoji}` You can use the emoji name as an alias
   * for `{customEmoji}`. For example, `customEmojis/:example-emoji:` where
   * `:example-emoji:` is the emoji name for a custom emoji.
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
   * Returns details about a custom emoji. Custom emojis are only available for
   * Google Workspace accounts, and the administrator must turn custom emojis on
   * for the organization. For more information, see [Learn about custom emojis in
   * Google Chat](https://support.google.com/chat/answer/12800149) and [Manage
   * custom emoji permissions](https://support.google.com/a/answer/12850085).
   * Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.customemojis.readonly` -
   * `https://www.googleapis.com/auth/chat.customemojis` (customEmojis.get)
   *
   * @param string $name Required. Resource name of the custom emoji. Format:
   * `customEmojis/{customEmoji}` You can use the emoji name as an alias for
   * `{customEmoji}`. For example, `customEmojis/:example-emoji:` where `:example-
   * emoji:` is the emoji name for a custom emoji.
   * @param array $optParams Optional parameters.
   * @return CustomEmoji
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CustomEmoji::class);
  }
  /**
   * Lists custom emojis visible to the authenticated user. Custom emojis are only
   * available for Google Workspace accounts, and the administrator must turn
   * custom emojis on for the organization. For more information, see [Learn about
   * custom emojis in Google
   * Chat](https://support.google.com/chat/answer/12800149) and [Manage custom
   * emoji permissions](https://support.google.com/a/answer/12850085). Requires
   * [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.customemojis.readonly` -
   * `https://www.googleapis.com/auth/chat.customemojis`
   * (customEmojis.listCustomEmojis)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A query filter. Supports filtering by
   * creator. To filter by creator, you must specify a valid value. Currently only
   * `creator("users/me")` and `NOT creator("users/me")` are accepted to filter
   * custom emojis by whether they were created by the calling user or not. For
   * example, the following query returns custom emojis created by the caller: ```
   * creator("users/me") ``` Invalid queries are rejected with an
   * `INVALID_ARGUMENT` error.
   * @opt_param int pageSize Optional. The maximum number of custom emojis
   * returned. The service can return fewer custom emojis than this value. If
   * unspecified, the default value is 25. The maximum value is 200; values above
   * 200 are changed to 200.
   * @opt_param string pageToken Optional. (If resuming from a previous query.) A
   * page token received from a previous list custom emoji call. Provide this to
   * retrieve the subsequent page. When paginating, the filter value should match
   * the call that provided the page token. Passing a different value might lead
   * to unexpected results.
   * @return ListCustomEmojisResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomEmojis($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCustomEmojisResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomEmojis::class, 'Google_Service_HangoutsChat_Resource_CustomEmojis');
