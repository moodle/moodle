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

use Google\Service\HangoutsChat\SpaceNotificationSetting;

/**
 * The "spaceNotificationSetting" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $spaceNotificationSetting = $chatService->users_spaces_spaceNotificationSetting;
 *  </code>
 */
class UsersSpacesSpaceNotificationSetting extends \Google\Service\Resource
{
  /**
   * Gets the space notification setting. For an example, see [Get the caller's
   * space notification setting](https://developers.google.com/workspace/chat/get-
   * space-notification-setting). Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with the [authorization
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.users.spacesettings`
   * (spaceNotificationSetting.get)
   *
   * @param string $name Required. Format:
   * users/{user}/spaces/{space}/spaceNotificationSetting -
   * `users/me/spaces/{space}/spaceNotificationSetting`, OR -
   * `users/user@example.com/spaces/{space}/spaceNotificationSetting`, OR -
   * `users/123456789/spaces/{space}/spaceNotificationSetting`. Note: Only the
   * caller's user id or email is allowed in the path.
   * @param array $optParams Optional parameters.
   * @return SpaceNotificationSetting
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SpaceNotificationSetting::class);
  }
  /**
   * Updates the space notification setting. For an example, see [Update the
   * caller's space notification
   * setting](https://developers.google.com/workspace/chat/update-space-
   * notification-setting). Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with the [authorization
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.users.spacesettings`
   * (spaceNotificationSetting.patch)
   *
   * @param string $name Identifier. The resource name of the space notification
   * setting. Format: `users/{user}/spaces/{space}/spaceNotificationSetting`.
   * @param SpaceNotificationSetting $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Supported field paths: -
   * `notification_setting` - `mute_setting`
   * @return SpaceNotificationSetting
   * @throws \Google\Service\Exception
   */
  public function patch($name, SpaceNotificationSetting $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], SpaceNotificationSetting::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsersSpacesSpaceNotificationSetting::class, 'Google_Service_HangoutsChat_Resource_UsersSpacesSpaceNotificationSetting');
