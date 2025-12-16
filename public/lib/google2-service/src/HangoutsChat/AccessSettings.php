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

class AccessSettings extends \Google\Model
{
  /**
   * Access state is unknown or not supported in this API.
   */
  public const ACCESS_STATE_ACCESS_STATE_UNSPECIFIED = 'ACCESS_STATE_UNSPECIFIED';
  /**
   * Only users or Google Groups that have been individually added or invited by
   * other users or Google Workspace administrators can discover and access the
   * space.
   */
  public const ACCESS_STATE_PRIVATE = 'PRIVATE';
  /**
   * A space manager has granted a target audience access to the space. Users or
   * Google Groups that have been individually added or invited to the space can
   * also discover and access the space. To learn more, see [Make a space
   * discoverable to specific
   * users](https://developers.google.com/workspace/chat/space-target-audience).
   * Creating discoverable spaces requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user).
   */
  public const ACCESS_STATE_DISCOVERABLE = 'DISCOVERABLE';
  /**
   * Output only. Indicates the access state of the space.
   *
   * @var string
   */
  public $accessState;
  /**
   * Optional. The resource name of the [target
   * audience](https://support.google.com/a/answer/9934697) who can discover the
   * space, join the space, and preview the messages in the space. If unset,
   * only users or Google Groups who have been individually invited or added to
   * the space can access it. For details, see [Make a space discoverable to a
   * target audience](https://developers.google.com/workspace/chat/space-target-
   * audience). Format: `audiences/{audience}` To use the default target
   * audience for the Google Workspace organization, set to `audiences/default`.
   * Reading the target audience supports: - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) with the
   * `chat.app.spaces` scope. This field is not populated when using the
   * `chat.bot` scope with [app
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app). Setting the target audience requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user).
   *
   * @var string
   */
  public $audience;

  /**
   * Output only. Indicates the access state of the space.
   *
   * Accepted values: ACCESS_STATE_UNSPECIFIED, PRIVATE, DISCOVERABLE
   *
   * @param self::ACCESS_STATE_* $accessState
   */
  public function setAccessState($accessState)
  {
    $this->accessState = $accessState;
  }
  /**
   * @return self::ACCESS_STATE_*
   */
  public function getAccessState()
  {
    return $this->accessState;
  }
  /**
   * Optional. The resource name of the [target
   * audience](https://support.google.com/a/answer/9934697) who can discover the
   * space, join the space, and preview the messages in the space. If unset,
   * only users or Google Groups who have been individually invited or added to
   * the space can access it. For details, see [Make a space discoverable to a
   * target audience](https://developers.google.com/workspace/chat/space-target-
   * audience). Format: `audiences/{audience}` To use the default target
   * audience for the Google Workspace organization, set to `audiences/default`.
   * Reading the target audience supports: - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) with the
   * `chat.app.spaces` scope. This field is not populated when using the
   * `chat.bot` scope with [app
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app). Setting the target audience requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user).
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessSettings::class, 'Google_Service_HangoutsChat_AccessSettings');
