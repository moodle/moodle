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

class Space extends \Google\Model
{
  /**
   * Unspecified. Don't use.
   */
  public const PREDEFINED_PERMISSION_SETTINGS_PREDEFINED_PERMISSION_SETTINGS_UNSPECIFIED = 'PREDEFINED_PERMISSION_SETTINGS_UNSPECIFIED';
  /**
   * Setting to make the space a collaboration space where all members can post
   * messages.
   */
  public const PREDEFINED_PERMISSION_SETTINGS_COLLABORATION_SPACE = 'COLLABORATION_SPACE';
  /**
   * Setting to make the space an announcement space where only space managers
   * can post messages.
   */
  public const PREDEFINED_PERMISSION_SETTINGS_ANNOUNCEMENT_SPACE = 'ANNOUNCEMENT_SPACE';
  /**
   * Default value. Do not use.
   */
  public const SPACE_HISTORY_STATE_HISTORY_STATE_UNSPECIFIED = 'HISTORY_STATE_UNSPECIFIED';
  /**
   * History off. [Messages and threads are kept for 24
   * hours](https://support.google.com/chat/answer/7664687).
   */
  public const SPACE_HISTORY_STATE_HISTORY_OFF = 'HISTORY_OFF';
  /**
   * History on. The organization's [Vault retention
   * rules](https://support.google.com/vault/answer/7657597) specify for how
   * long messages and threads are kept.
   */
  public const SPACE_HISTORY_STATE_HISTORY_ON = 'HISTORY_ON';
  /**
   * Reserved.
   */
  public const SPACE_THREADING_STATE_SPACE_THREADING_STATE_UNSPECIFIED = 'SPACE_THREADING_STATE_UNSPECIFIED';
  /**
   * Named spaces that support message threads. When users respond to a message,
   * they can reply in-thread, which keeps their response in the context of the
   * original message.
   */
  public const SPACE_THREADING_STATE_THREADED_MESSAGES = 'THREADED_MESSAGES';
  /**
   * Named spaces where the conversation is organized by topic. Topics and their
   * replies are grouped together.
   */
  public const SPACE_THREADING_STATE_GROUPED_MESSAGES = 'GROUPED_MESSAGES';
  /**
   * Direct messages (DMs) between two people and group conversations between 3
   * or more people.
   */
  public const SPACE_THREADING_STATE_UNTHREADED_MESSAGES = 'UNTHREADED_MESSAGES';
  /**
   * Reserved.
   */
  public const SPACE_TYPE_SPACE_TYPE_UNSPECIFIED = 'SPACE_TYPE_UNSPECIFIED';
  /**
   * A place where people send messages, share files, and collaborate. A `SPACE`
   * can include Chat apps.
   */
  public const SPACE_TYPE_SPACE = 'SPACE';
  /**
   * Group conversations between 3 or more people. A `GROUP_CHAT` can include
   * Chat apps.
   */
  public const SPACE_TYPE_GROUP_CHAT = 'GROUP_CHAT';
  /**
   * 1:1 messages between two humans or a human and a Chat app.
   */
  public const SPACE_TYPE_DIRECT_MESSAGE = 'DIRECT_MESSAGE';
  /**
   * Reserved.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Conversations between two or more humans.
   */
  public const TYPE_ROOM = 'ROOM';
  /**
   * 1:1 Direct Message between a human and a Chat app, where all messages are
   * flat. Note that this doesn't include direct messages between two humans.
   */
  public const TYPE_DM = 'DM';
  protected $accessSettingsType = AccessSettings::class;
  protected $accessSettingsDataType = '';
  /**
   * Output only. For direct message (DM) spaces with a Chat app, whether the
   * space was created by a Google Workspace administrator. Administrators can
   * install and set up a direct message with a Chat app on behalf of users in
   * their organization. To support admin install, your Chat app must feature
   * direct messaging.
   *
   * @var bool
   */
  public $adminInstalled;
  /**
   * Optional. Immutable. For spaces created in Chat, the time the space was
   * created. This field is output only, except when used in import mode spaces.
   * For import mode spaces, set this field to the historical timestamp at which
   * the space was created in the source in order to preserve the original
   * creation time. Only populated in the output when `spaceType` is
   * `GROUP_CHAT` or `SPACE`.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Immutable. The customer id of the domain of the space. Required
   * only when creating a space with [app
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) and `SpaceType` is `SPACE`, otherwise should not be
   * set. In the format `customers/{customer}`, where `customer` is the `id`
   * from the [Admin SDK customer resource](https://developers.google.com/admin-
   * sdk/directory/reference/rest/v1/customers). Private apps can also use the
   * `customers/my_customer` alias to create the space in the same Google
   * Workspace organization as the app. This field isn't populated for direct
   * messages (DMs) or when the space is created by non-Google Workspace users.
   *
   * @var string
   */
  public $customer;
  /**
   * Optional. The space's display name. Required when [creating a space](https:
   * //developers.google.com/workspace/chat/api/reference/rest/v1/spaces/create)
   * with a `spaceType` of `SPACE`. If you receive the error message
   * `ALREADY_EXISTS` when creating a space or updating the `displayName`, try a
   * different `displayName`. An existing space within the Google Workspace
   * organization might already use this display name. For direct messages, this
   * field might be empty. Supports up to 128 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Immutable. Whether this space permits any Google Chat user as a
   * member. Input when creating a space in a Google Workspace organization.
   * Omit this field when creating spaces in the following conditions: * The
   * authenticated user uses a consumer account (unmanaged user account). By
   * default, a space created by a consumer account permits any Google Chat
   * user. For existing spaces, this field is output only.
   *
   * @var bool
   */
  public $externalUserAllowed;
  /**
   * Optional. Whether this space is created in `Import Mode` as part of a data
   * migration into Google Workspace. While spaces are being imported, they
   * aren't visible to users until the import is complete. Creating a space in
   * `Import Mode`requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user).
   *
   * @var bool
   */
  public $importMode;
  /**
   * Output only. The time when the space will be automatically deleted by the
   * system if it remains in import mode. Each space created in import mode must
   * exit this mode before this expire time using `spaces.completeImport`. This
   * field is only populated for spaces that were created with import mode.
   *
   * @var string
   */
  public $importModeExpireTime;
  /**
   * Output only. Timestamp of the last message in the space.
   *
   * @var string
   */
  public $lastActiveTime;
  protected $membershipCountType = MembershipCount::class;
  protected $membershipCountDataType = '';
  /**
   * Identifier. Resource name of the space. Format: `spaces/{space}` Where
   * `{space}` represents the system-assigned ID for the space. You can obtain
   * the space ID by calling the [`spaces.list()`](https://developers.google.com
   * /workspace/chat/api/reference/rest/v1/spaces/list) method or from the space
   * URL. For example, if the space URL is
   * `https://mail.google.com/mail/u/0/#chat/space/AAAAAAAAA`, the space ID is
   * `AAAAAAAAA`.
   *
   * @var string
   */
  public $name;
  protected $permissionSettingsType = PermissionSettings::class;
  protected $permissionSettingsDataType = '';
  /**
   * Optional. Input only. Predefined space permission settings, input only when
   * creating a space. If the field is not set, a collaboration space is
   * created. After you create the space, settings are populated in the
   * `PermissionSettings` field. Setting predefined permission settings
   * supports: - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) with the
   * `chat.app.spaces` or `chat.app.spaces.create` scopes. - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user)
   *
   * @var string
   */
  public $predefinedPermissionSettings;
  /**
   * Optional. Whether the space is a DM between a Chat app and a single human.
   *
   * @var bool
   */
  public $singleUserBotDm;
  protected $spaceDetailsType = SpaceDetails::class;
  protected $spaceDetailsDataType = '';
  /**
   * Optional. The message history state for messages and threads in this space.
   *
   * @var string
   */
  public $spaceHistoryState;
  /**
   * Output only. The threading state in the Chat space.
   *
   * @var string
   */
  public $spaceThreadingState;
  /**
   * Optional. The type of space. Required when creating a space or updating the
   * space type of a space. Output only for other usage.
   *
   * @var string
   */
  public $spaceType;
  /**
   * Output only. The URI for a user to access the space.
   *
   * @var string
   */
  public $spaceUri;
  /**
   * Output only. Deprecated: Use `spaceThreadingState` instead. Whether
   * messages are threaded in this space.
   *
   * @deprecated
   * @var bool
   */
  public $threaded;
  /**
   * Output only. Deprecated: Use `space_type` instead. The type of a space.
   *
   * @deprecated
   * @var string
   */
  public $type;

  /**
   * Optional. Specifies the [access
   * setting](https://support.google.com/chat/answer/11971020) of the space.
   * Only populated when the `space_type` is `SPACE`.
   *
   * @param AccessSettings $accessSettings
   */
  public function setAccessSettings(AccessSettings $accessSettings)
  {
    $this->accessSettings = $accessSettings;
  }
  /**
   * @return AccessSettings
   */
  public function getAccessSettings()
  {
    return $this->accessSettings;
  }
  /**
   * Output only. For direct message (DM) spaces with a Chat app, whether the
   * space was created by a Google Workspace administrator. Administrators can
   * install and set up a direct message with a Chat app on behalf of users in
   * their organization. To support admin install, your Chat app must feature
   * direct messaging.
   *
   * @param bool $adminInstalled
   */
  public function setAdminInstalled($adminInstalled)
  {
    $this->adminInstalled = $adminInstalled;
  }
  /**
   * @return bool
   */
  public function getAdminInstalled()
  {
    return $this->adminInstalled;
  }
  /**
   * Optional. Immutable. For spaces created in Chat, the time the space was
   * created. This field is output only, except when used in import mode spaces.
   * For import mode spaces, set this field to the historical timestamp at which
   * the space was created in the source in order to preserve the original
   * creation time. Only populated in the output when `spaceType` is
   * `GROUP_CHAT` or `SPACE`.
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
   * Optional. Immutable. The customer id of the domain of the space. Required
   * only when creating a space with [app
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) and `SpaceType` is `SPACE`, otherwise should not be
   * set. In the format `customers/{customer}`, where `customer` is the `id`
   * from the [Admin SDK customer resource](https://developers.google.com/admin-
   * sdk/directory/reference/rest/v1/customers). Private apps can also use the
   * `customers/my_customer` alias to create the space in the same Google
   * Workspace organization as the app. This field isn't populated for direct
   * messages (DMs) or when the space is created by non-Google Workspace users.
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Optional. The space's display name. Required when [creating a space](https:
   * //developers.google.com/workspace/chat/api/reference/rest/v1/spaces/create)
   * with a `spaceType` of `SPACE`. If you receive the error message
   * `ALREADY_EXISTS` when creating a space or updating the `displayName`, try a
   * different `displayName`. An existing space within the Google Workspace
   * organization might already use this display name. For direct messages, this
   * field might be empty. Supports up to 128 characters.
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
   * Optional. Immutable. Whether this space permits any Google Chat user as a
   * member. Input when creating a space in a Google Workspace organization.
   * Omit this field when creating spaces in the following conditions: * The
   * authenticated user uses a consumer account (unmanaged user account). By
   * default, a space created by a consumer account permits any Google Chat
   * user. For existing spaces, this field is output only.
   *
   * @param bool $externalUserAllowed
   */
  public function setExternalUserAllowed($externalUserAllowed)
  {
    $this->externalUserAllowed = $externalUserAllowed;
  }
  /**
   * @return bool
   */
  public function getExternalUserAllowed()
  {
    return $this->externalUserAllowed;
  }
  /**
   * Optional. Whether this space is created in `Import Mode` as part of a data
   * migration into Google Workspace. While spaces are being imported, they
   * aren't visible to users until the import is complete. Creating a space in
   * `Import Mode`requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user).
   *
   * @param bool $importMode
   */
  public function setImportMode($importMode)
  {
    $this->importMode = $importMode;
  }
  /**
   * @return bool
   */
  public function getImportMode()
  {
    return $this->importMode;
  }
  /**
   * Output only. The time when the space will be automatically deleted by the
   * system if it remains in import mode. Each space created in import mode must
   * exit this mode before this expire time using `spaces.completeImport`. This
   * field is only populated for spaces that were created with import mode.
   *
   * @param string $importModeExpireTime
   */
  public function setImportModeExpireTime($importModeExpireTime)
  {
    $this->importModeExpireTime = $importModeExpireTime;
  }
  /**
   * @return string
   */
  public function getImportModeExpireTime()
  {
    return $this->importModeExpireTime;
  }
  /**
   * Output only. Timestamp of the last message in the space.
   *
   * @param string $lastActiveTime
   */
  public function setLastActiveTime($lastActiveTime)
  {
    $this->lastActiveTime = $lastActiveTime;
  }
  /**
   * @return string
   */
  public function getLastActiveTime()
  {
    return $this->lastActiveTime;
  }
  /**
   * Output only. The count of joined memberships grouped by member type.
   * Populated when the `space_type` is `SPACE`, `DIRECT_MESSAGE` or
   * `GROUP_CHAT`.
   *
   * @param MembershipCount $membershipCount
   */
  public function setMembershipCount(MembershipCount $membershipCount)
  {
    $this->membershipCount = $membershipCount;
  }
  /**
   * @return MembershipCount
   */
  public function getMembershipCount()
  {
    return $this->membershipCount;
  }
  /**
   * Identifier. Resource name of the space. Format: `spaces/{space}` Where
   * `{space}` represents the system-assigned ID for the space. You can obtain
   * the space ID by calling the [`spaces.list()`](https://developers.google.com
   * /workspace/chat/api/reference/rest/v1/spaces/list) method or from the space
   * URL. For example, if the space URL is
   * `https://mail.google.com/mail/u/0/#chat/space/AAAAAAAAA`, the space ID is
   * `AAAAAAAAA`.
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
   * Optional. Space permission settings for existing spaces. Input for updating
   * exact space permission settings, where existing permission settings are
   * replaced. Output lists current permission settings. Reading and updating
   * permission settings supports: - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) with the
   * `chat.app.spaces` scope. Only populated and settable when the Chat app
   * created the space. - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user)
   *
   * @param PermissionSettings $permissionSettings
   */
  public function setPermissionSettings(PermissionSettings $permissionSettings)
  {
    $this->permissionSettings = $permissionSettings;
  }
  /**
   * @return PermissionSettings
   */
  public function getPermissionSettings()
  {
    return $this->permissionSettings;
  }
  /**
   * Optional. Input only. Predefined space permission settings, input only when
   * creating a space. If the field is not set, a collaboration space is
   * created. After you create the space, settings are populated in the
   * `PermissionSettings` field. Setting predefined permission settings
   * supports: - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) with the
   * `chat.app.spaces` or `chat.app.spaces.create` scopes. - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user)
   *
   * Accepted values: PREDEFINED_PERMISSION_SETTINGS_UNSPECIFIED,
   * COLLABORATION_SPACE, ANNOUNCEMENT_SPACE
   *
   * @param self::PREDEFINED_PERMISSION_SETTINGS_* $predefinedPermissionSettings
   */
  public function setPredefinedPermissionSettings($predefinedPermissionSettings)
  {
    $this->predefinedPermissionSettings = $predefinedPermissionSettings;
  }
  /**
   * @return self::PREDEFINED_PERMISSION_SETTINGS_*
   */
  public function getPredefinedPermissionSettings()
  {
    return $this->predefinedPermissionSettings;
  }
  /**
   * Optional. Whether the space is a DM between a Chat app and a single human.
   *
   * @param bool $singleUserBotDm
   */
  public function setSingleUserBotDm($singleUserBotDm)
  {
    $this->singleUserBotDm = $singleUserBotDm;
  }
  /**
   * @return bool
   */
  public function getSingleUserBotDm()
  {
    return $this->singleUserBotDm;
  }
  /**
   * Optional. Details about the space including description and rules.
   *
   * @param SpaceDetails $spaceDetails
   */
  public function setSpaceDetails(SpaceDetails $spaceDetails)
  {
    $this->spaceDetails = $spaceDetails;
  }
  /**
   * @return SpaceDetails
   */
  public function getSpaceDetails()
  {
    return $this->spaceDetails;
  }
  /**
   * Optional. The message history state for messages and threads in this space.
   *
   * Accepted values: HISTORY_STATE_UNSPECIFIED, HISTORY_OFF, HISTORY_ON
   *
   * @param self::SPACE_HISTORY_STATE_* $spaceHistoryState
   */
  public function setSpaceHistoryState($spaceHistoryState)
  {
    $this->spaceHistoryState = $spaceHistoryState;
  }
  /**
   * @return self::SPACE_HISTORY_STATE_*
   */
  public function getSpaceHistoryState()
  {
    return $this->spaceHistoryState;
  }
  /**
   * Output only. The threading state in the Chat space.
   *
   * Accepted values: SPACE_THREADING_STATE_UNSPECIFIED, THREADED_MESSAGES,
   * GROUPED_MESSAGES, UNTHREADED_MESSAGES
   *
   * @param self::SPACE_THREADING_STATE_* $spaceThreadingState
   */
  public function setSpaceThreadingState($spaceThreadingState)
  {
    $this->spaceThreadingState = $spaceThreadingState;
  }
  /**
   * @return self::SPACE_THREADING_STATE_*
   */
  public function getSpaceThreadingState()
  {
    return $this->spaceThreadingState;
  }
  /**
   * Optional. The type of space. Required when creating a space or updating the
   * space type of a space. Output only for other usage.
   *
   * Accepted values: SPACE_TYPE_UNSPECIFIED, SPACE, GROUP_CHAT, DIRECT_MESSAGE
   *
   * @param self::SPACE_TYPE_* $spaceType
   */
  public function setSpaceType($spaceType)
  {
    $this->spaceType = $spaceType;
  }
  /**
   * @return self::SPACE_TYPE_*
   */
  public function getSpaceType()
  {
    return $this->spaceType;
  }
  /**
   * Output only. The URI for a user to access the space.
   *
   * @param string $spaceUri
   */
  public function setSpaceUri($spaceUri)
  {
    $this->spaceUri = $spaceUri;
  }
  /**
   * @return string
   */
  public function getSpaceUri()
  {
    return $this->spaceUri;
  }
  /**
   * Output only. Deprecated: Use `spaceThreadingState` instead. Whether
   * messages are threaded in this space.
   *
   * @deprecated
   * @param bool $threaded
   */
  public function setThreaded($threaded)
  {
    $this->threaded = $threaded;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getThreaded()
  {
    return $this->threaded;
  }
  /**
   * Output only. Deprecated: Use `space_type` instead. The type of a space.
   *
   * Accepted values: TYPE_UNSPECIFIED, ROOM, DM
   *
   * @deprecated
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @deprecated
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Space::class, 'Google_Service_HangoutsChat_Space');
