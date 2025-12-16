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

class User extends \Google\Model
{
  /**
   * Default value for the enum. DO NOT USE.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Human user.
   */
  public const TYPE_HUMAN = 'HUMAN';
  /**
   * Chat app user.
   */
  public const TYPE_BOT = 'BOT';
  /**
   * Output only. The user's display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Unique identifier of the user's Google Workspace domain.
   *
   * @var string
   */
  public $domainId;
  /**
   * Output only. When `true`, the user is deleted or their profile is not
   * visible.
   *
   * @var bool
   */
  public $isAnonymous;
  /**
   * Resource name for a Google Chat user. Format: `users/{user}`. `users/app`
   * can be used as an alias for the calling app bot user. For human users,
   * `{user}` is the same user identifier as: - the `id` for the
   * [Person](https://developers.google.com/people/api/rest/v1/people) in the
   * People API. For example, `users/123456789` in Chat API represents the same
   * person as the `123456789` Person profile ID in People API. - the `id` for a
   * [user](https://developers.google.com/admin-
   * sdk/directory/reference/rest/v1/users) in the Admin SDK Directory API. -
   * the user's email address can be used as an alias for `{user}` in API
   * requests. For example, if the People API Person profile ID for
   * `user@example.com` is `123456789`, you can use `users/user@example.com` as
   * an alias to reference `users/123456789`. Only the canonical resource name
   * (for example `users/123456789`) will be returned from the API.
   *
   * @var string
   */
  public $name;
  /**
   * User type.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The user's display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Unique identifier of the user's Google Workspace domain.
   *
   * @param string $domainId
   */
  public function setDomainId($domainId)
  {
    $this->domainId = $domainId;
  }
  /**
   * @return string
   */
  public function getDomainId()
  {
    return $this->domainId;
  }
  /**
   * Output only. When `true`, the user is deleted or their profile is not
   * visible.
   *
   * @param bool $isAnonymous
   */
  public function setIsAnonymous($isAnonymous)
  {
    $this->isAnonymous = $isAnonymous;
  }
  /**
   * @return bool
   */
  public function getIsAnonymous()
  {
    return $this->isAnonymous;
  }
  /**
   * Resource name for a Google Chat user. Format: `users/{user}`. `users/app`
   * can be used as an alias for the calling app bot user. For human users,
   * `{user}` is the same user identifier as: - the `id` for the
   * [Person](https://developers.google.com/people/api/rest/v1/people) in the
   * People API. For example, `users/123456789` in Chat API represents the same
   * person as the `123456789` Person profile ID in People API. - the `id` for a
   * [user](https://developers.google.com/admin-
   * sdk/directory/reference/rest/v1/users) in the Admin SDK Directory API. -
   * the user's email address can be used as an alias for `{user}` in API
   * requests. For example, if the People API Person profile ID for
   * `user@example.com` is `123456789`, you can use `users/user@example.com` as
   * an alias to reference `users/123456789`. Only the canonical resource name
   * (for example `users/123456789`) will be returned from the API.
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
   * User type.
   *
   * Accepted values: TYPE_UNSPECIFIED, HUMAN, BOT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_HangoutsChat_User');
