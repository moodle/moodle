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
use Google\Service\HangoutsChat\CompleteImportSpaceRequest;
use Google\Service\HangoutsChat\CompleteImportSpaceResponse;
use Google\Service\HangoutsChat\ListSpacesResponse;
use Google\Service\HangoutsChat\SearchSpacesResponse;
use Google\Service\HangoutsChat\SetUpSpaceRequest;
use Google\Service\HangoutsChat\Space;

/**
 * The "spaces" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $spaces = $chatService->spaces;
 *  </code>
 */
class Spaces extends \Google\Service\Resource
{
  /**
   * Completes the [import
   * process](https://developers.google.com/workspace/chat/import-data) for the
   * specified space and makes it visible to users. Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) and domain-wide delegation with the [authorization
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): - `https://www.googleapis.com/auth/chat.import`
   * For more information, see [Authorize Google Chat apps to import
   * data](https://developers.google.com/workspace/chat/authorize-import).
   * (spaces.completeImport)
   *
   * @param string $name Required. Resource name of the import mode space. Format:
   * `spaces/{space}`
   * @param CompleteImportSpaceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CompleteImportSpaceResponse
   * @throws \Google\Service\Exception
   */
  public function completeImport($name, CompleteImportSpaceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('completeImport', [$params], CompleteImportSpaceResponse::class);
  }
  /**
   * Creates a space. Can be used to create a named space, or a group chat in
   * `Import mode`. For an example, see [Create a
   * space](https://developers.google.com/workspace/chat/create-spaces). Supports
   * the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) and one of the
   * following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.app.spaces.create` -
   * `https://www.googleapis.com/auth/chat.app.spaces` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.spaces.create` -
   * `https://www.googleapis.com/auth/chat.spaces` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only) When
   * authenticating as an app, the `space.customer` field must be set in the
   * request. When authenticating as an app, the Chat app is added as a member of
   * the space. However, unlike human authentication, the Chat app is not added as
   * a space manager. By default, the Chat app can be removed from the space by
   * all space members. To allow only space managers to remove the app from a
   * space, set `space.permission_settings.manage_apps` to `managers_allowed`.
   * Space membership upon creation depends on whether the space is created in
   * `Import mode`: * **Import mode:** No members are created. * **All other
   * modes:** The calling user is added as a member. This is: * The app itself
   * when using app authentication. * The human user when using user
   * authentication. If you receive the error message `ALREADY_EXISTS` when
   * creating a space, try a different `displayName`. An existing space within the
   * Google Workspace organization might already use this display name.
   * (spaces.create)
   *
   * @param Space $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request. A
   * random UUID is recommended. Specifying an existing request ID returns the
   * space created with that ID instead of creating a new space. Specifying an
   * existing request ID from the same Chat app with a different authenticated
   * user returns an error.
   * @return Space
   * @throws \Google\Service\Exception
   */
  public function create(Space $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Space::class);
  }
  /**
   * Deletes a named space. Always performs a cascading delete, which means that
   * the space's child resources—like messages posted in the space and memberships
   * in the space—are also deleted. For an example, see [Delete a
   * space](https://developers.google.com/workspace/chat/delete-spaces). Supports
   * the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) and the authorization
   * scope: - `https://www.googleapis.com/auth/chat.app.delete` (only in spaces
   * the app created) - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.delete` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only) -
   * User authentication grants administrator privileges when an administrator
   * account authenticates, `use_admin_access` is `true`, and the following
   * authorization scope is used: -
   * `https://www.googleapis.com/auth/chat.admin.delete` (spaces.delete)
   *
   * @param string $name Required. Resource name of the space to delete. Format:
   * `spaces/{space}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool useAdminAccess Optional. When `true`, the method runs using
   * the user's Google Workspace administrator privileges. The calling user must
   * be a Google Workspace administrator with the [manage chat and spaces
   * conversations privilege](https://support.google.com/a/answer/13369245).
   * Requires the `chat.admin.delete` [OAuth 2.0
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes).
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
   * Returns the existing direct message with the specified user. If no direct
   * message space is found, returns a `404 NOT_FOUND` error. For an example, see
   * [Find a direct message](/chat/api/guides/v1/spaces/find-direct-message). With
   * [app
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app), returns the direct message space between the specified
   * user and the calling Chat app. With [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user), returns the direct message space between the specified
   * user and the authenticated user. Supports the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with the authorization scope: -
   * `https://www.googleapis.com/auth/chat.bot` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.spaces.readonly` -
   * `https://www.googleapis.com/auth/chat.spaces` (spaces.findDirectMessage)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string name Required. Resource name of the user to find direct
   * message with. Format: `users/{user}`, where `{user}` is either the `id` for
   * the [person](https://developers.google.com/people/api/rest/v1/people) from
   * the People API, or the `id` for the
   * [user](https://developers.google.com/admin-
   * sdk/directory/reference/rest/v1/users) in the Directory API. For example, if
   * the People API profile ID is `123456789`, you can find a direct message with
   * that person by using `users/123456789` as the `name`. When [authenticated as
   * a user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user), you can use the email as an alias for `{user}`. For example,
   * `users/example@gmail.com` where `example@gmail.com` is the email of the
   * Google Chat user.
   * @return Space
   * @throws \Google\Service\Exception
   */
  public function findDirectMessage($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('findDirectMessage', [$params], Space::class);
  }
  /**
   * Returns details about a space. For an example, see [Get details about a
   * space](https://developers.google.com/workspace/chat/get-spaces). Supports the
   * following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.bot` -
   * `https://www.googleapis.com/auth/chat.app.spaces` with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.spaces.readonly` -
   * `https://www.googleapis.com/auth/chat.spaces` - User authentication grants
   * administrator privileges when an administrator account authenticates,
   * `use_admin_access` is `true`, and one of the following authorization scopes
   * is used: - `https://www.googleapis.com/auth/chat.admin.spaces.readonly` -
   * `https://www.googleapis.com/auth/chat.admin.spaces` App authentication has
   * the following limitations: - `space.access_settings` is only populated when
   * using the `chat.app.spaces` scope. - `space.predefind_permission_settings`
   * and `space.permission_settings` are only populated when using the
   * `chat.app.spaces` scope, and only for spaces the app created. (spaces.get)
   *
   * @param string $name Required. Resource name of the space, in the form
   * `spaces/{space}`. Format: `spaces/{space}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool useAdminAccess Optional. When `true`, the method runs using
   * the user's Google Workspace administrator privileges. The calling user must
   * be a Google Workspace administrator with the [manage chat and spaces
   * conversations privilege](https://support.google.com/a/answer/13369245).
   * Requires the `chat.admin.spaces` or `chat.admin.spaces.readonly` [OAuth 2.0
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes).
   * @return Space
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Space::class);
  }
  /**
   * Lists spaces the caller is a member of. Group chats and DMs aren't listed
   * until the first message is sent. For an example, see [List
   * spaces](https://developers.google.com/workspace/chat/list-spaces). Supports
   * the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with the authorization scope: -
   * `https://www.googleapis.com/auth/chat.bot` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.spaces.readonly` -
   * `https://www.googleapis.com/auth/chat.spaces` To list all named spaces by
   * Google Workspace organization, use the [`spaces.search()`](https://developers
   * .google.com/workspace/chat/api/reference/rest/v1/spaces/search) method using
   * Workspace administrator privileges instead. (spaces.listSpaces)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A query filter. You can filter spaces by
   * the space type ([`space_type`](https://developers.google.com/workspace/chat/a
   * pi/reference/rest/v1/spaces#spacetype)). To filter by space type, you must
   * specify valid enum value, such as `SPACE` or `GROUP_CHAT` (the `space_type`
   * can't be `SPACE_TYPE_UNSPECIFIED`). To query for multiple space types, use
   * the `OR` operator. For example, the following queries are valid: ```
   * space_type = "SPACE" spaceType = "GROUP_CHAT" OR spaceType = "DIRECT_MESSAGE"
   * ``` Invalid queries are rejected by the server with an `INVALID_ARGUMENT`
   * error.
   * @opt_param int pageSize Optional. The maximum number of spaces to return. The
   * service might return fewer than this value. If unspecified, at most 100
   * spaces are returned. The maximum value is 1000. If you use a value more than
   * 1000, it's automatically changed to 1000. Negative values return an
   * `INVALID_ARGUMENT` error.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * list spaces call. Provide this parameter to retrieve the subsequent page.
   * When paginating, the filter value should match the call that provided the
   * page token. Passing a different value may lead to unexpected results.
   * @return ListSpacesResponse
   * @throws \Google\Service\Exception
   */
  public function listSpaces($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSpacesResponse::class);
  }
  /**
   * Updates a space. For an example, see [Update a
   * space](https://developers.google.com/workspace/chat/update-spaces). If you're
   * updating the `displayName` field and receive the error message
   * `ALREADY_EXISTS`, try a different display name.. An existing space within the
   * Google Workspace organization might already use this display name. Supports
   * the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) and one of the
   * following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.app.spaces` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.spaces` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only) -
   * User authentication grants administrator privileges when an administrator
   * account authenticates, `use_admin_access` is `true`, and the following
   * authorization scopes is used: -
   * `https://www.googleapis.com/auth/chat.admin.spaces` App authentication has
   * the following limitations: - To update either
   * `space.predefined_permission_settings` or `space.permission_settings`, the
   * app must be the space creator. - Updating the
   * `space.access_settings.audience` is not supported for app authentication.
   * (spaces.patch)
   *
   * @param string $name Identifier. Resource name of the space. Format:
   * `spaces/{space}` Where `{space}` represents the system-assigned ID for the
   * space. You can obtain the space ID by calling the [`spaces.list()`](https://d
   * evelopers.google.com/workspace/chat/api/reference/rest/v1/spaces/list) method
   * or from the space URL. For example, if the space URL is
   * `https://mail.google.com/mail/u/0/#chat/space/AAAAAAAAA`, the space ID is
   * `AAAAAAAAA`.
   * @param Space $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The updated field paths, comma
   * separated if there are multiple. You can update the following fields for a
   * space: `space_details`: Updates the space's description and guidelines. You
   * must pass both description and guidelines in the update request as
   * `SpaceDetails`. If you only want to update one of the fields, pass the
   * existing value for the other field. `display_name`: Only supports updating
   * the display name for spaces where `spaceType` field is `SPACE`. If you
   * receive the error message `ALREADY_EXISTS`, try a different value. An
   * existing space within the Google Workspace organization might already use
   * this display name. `space_type`: Only supports changing a `GROUP_CHAT` space
   * type to `SPACE`. Include `display_name` together with `space_type` in the
   * update mask and ensure that the specified space has a non-empty display name
   * and the `SPACE` space type. Including the `space_type` mask and the `SPACE`
   * type in the specified space when updating the display name is optional if the
   * existing space already has the `SPACE` type. Trying to update the space type
   * in other ways results in an invalid argument error. `space_type` is not
   * supported with `useAdminAccess`. `space_history_state`: Updates [space
   * history settings](https://support.google.com/chat/answer/7664687) by turning
   * history on or off for the space. Only supported if history settings are
   * enabled for the Google Workspace organization. To update the space history
   * state, you must omit all other field masks in your request.
   * `space_history_state` is not supported with `useAdminAccess`.
   * `access_settings.audience`: Updates the [access
   * setting](https://support.google.com/chat/answer/11971020) of who can discover
   * the space, join the space, and preview the messages in named space where
   * `spaceType` field is `SPACE`. If the existing space has a target audience,
   * you can remove the audience and restrict space access by omitting a value for
   * this field mask. To update access settings for a space, the authenticating
   * user must be a space manager and omit all other field masks in your request.
   * You can't update this field if the space is in [import
   * mode](https://developers.google.com/workspace/chat/import-data-overview). To
   * learn more, see [Make a space discoverable to specific
   * users](https://developers.google.com/workspace/chat/space-target-audience).
   * `access_settings.audience` is not supported with `useAdminAccess`.
   * `permission_settings`: Supports changing the [permission
   * settings](https://support.google.com/chat/answer/13340792) of a space. When
   * updating permission settings, you can only specify `permissionSettings` field
   * masks; you cannot update other field masks at the same time. The supported
   * field masks include: - `permission_settings.manageMembersAndGroups` -
   * `permission_settings.modifySpaceDetails` -
   * `permission_settings.toggleHistory` - `permission_settings.useAtMentionAll` -
   * `permission_settings.manageApps` - `permission_settings.manageWebhooks` -
   * `permission_settings.replyMessages`
   * @opt_param bool useAdminAccess Optional. When `true`, the method runs using
   * the user's Google Workspace administrator privileges. The calling user must
   * be a Google Workspace administrator with the [manage chat and spaces
   * conversations privilege](https://support.google.com/a/answer/13369245).
   * Requires the `chat.admin.spaces` [OAuth 2.0
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes). Some `FieldMask` values are not supported using
   * admin access. For details, see the description of `update_mask`.
   * @return Space
   * @throws \Google\Service\Exception
   */
  public function patch($name, Space $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Space::class);
  }
  /**
   * Returns a list of spaces in a Google Workspace organization based on an
   * administrator's search. In the request, set `use_admin_access` to `true`. For
   * an example, see [Search for and manage
   * spaces](https://developers.google.com/workspace/chat/search-manage-admin).
   * Requires [user authentication with administrator
   * privileges](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user#admin-privileges) and one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.admin.spaces.readonly` -
   * `https://www.googleapis.com/auth/chat.admin.spaces` (spaces.search)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Optional. How the list of spaces is ordered.
   * Supported attributes to order by are: -
   * `membership_count.joined_direct_human_user_count` — Denotes the count of
   * human users that have directly joined a space. - `last_active_time` — Denotes
   * the time when last eligible item is added to any topic of this space. -
   * `create_time` — Denotes the time of the space creation. Valid ordering
   * operation values are: - `ASC` for ascending. Default value. - `DESC` for
   * descending. The supported syntax are: -
   * `membership_count.joined_direct_human_user_count DESC` -
   * `membership_count.joined_direct_human_user_count ASC` - `last_active_time
   * DESC` - `last_active_time ASC` - `create_time DESC` - `create_time ASC`
   * @opt_param int pageSize The maximum number of spaces to return. The service
   * may return fewer than this value. If unspecified, at most 100 spaces are
   * returned. The maximum value is 1000. If you use a value more than 1000, it's
   * automatically changed to 1000.
   * @opt_param string pageToken A token, received from the previous search spaces
   * call. Provide this parameter to retrieve the subsequent page. When
   * paginating, all other parameters provided should match the call that provided
   * the page token. Passing different values to the other parameters might lead
   * to unexpected results.
   * @opt_param string query Required. A search query. You can search by using the
   * following parameters: - `create_time` - `customer` - `display_name` -
   * `external_user_allowed` - `last_active_time` - `space_history_state` -
   * `space_type` `create_time` and `last_active_time` accept a timestamp in
   * [RFC-3339](https://www.rfc-editor.org/rfc/rfc3339) format and the supported
   * comparison operators are: `=`, `<`, `>`, `<=`, `>=`. `customer` is required
   * and is used to indicate which customer to fetch spaces from.
   * `customers/my_customer` is the only supported value. `display_name` only
   * accepts the `HAS` (`:`) operator. The text to match is first tokenized into
   * tokens and each token is prefix-matched case-insensitively and independently
   * as a substring anywhere in the space's `display_name`. For example, `Fun Eve`
   * matches `Fun event` or `The evening was fun`, but not `notFun event` or
   * `even`. `external_user_allowed` accepts either `true` or `false`.
   * `space_history_state` only accepts values from the [`historyState`] (https://
   * developers.google.com/workspace/chat/api/reference/rest/v1/spaces#Space.Histo
   * ryState) field of a `space` resource. `space_type` is required and the only
   * valid value is `SPACE`. Across different fields, only `AND` operators are
   * supported. A valid example is `space_type = "SPACE" AND display_name:"Hello"`
   * and an invalid example is `space_type = "SPACE" OR display_name:"Hello"`.
   * Among the same field, `space_type` doesn't support `AND` or `OR` operators.
   * `display_name`, 'space_history_state', and 'external_user_allowed' only
   * support `OR` operators. `last_active_time` and `create_time` support both
   * `AND` and `OR` operators. `AND` can only be used to represent an interval,
   * such as `last_active_time < "2022-01-01T00:00:00+00:00" AND last_active_time
   * > "2023-01-01T00:00:00+00:00"`. The following example queries are valid: ```
   * customer = "customers/my_customer" AND space_type = "SPACE" customer =
   * "customers/my_customer" AND space_type = "SPACE" AND display_name:"Hello
   * World" customer = "customers/my_customer" AND space_type = "SPACE" AND
   * (last_active_time < "2020-01-01T00:00:00+00:00" OR last_active_time >
   * "2022-01-01T00:00:00+00:00") customer = "customers/my_customer" AND
   * space_type = "SPACE" AND (display_name:"Hello World" OR display_name:"Fun
   * event") AND (last_active_time > "2020-01-01T00:00:00+00:00" AND
   * last_active_time < "2022-01-01T00:00:00+00:00") customer =
   * "customers/my_customer" AND space_type = "SPACE" AND (create_time >
   * "2019-01-01T00:00:00+00:00" AND create_time < "2020-01-01T00:00:00+00:00")
   * AND (external_user_allowed = "true") AND (space_history_state = "HISTORY_ON"
   * OR space_history_state = "HISTORY_OFF") ```
   * @opt_param bool useAdminAccess When `true`, the method runs using the user's
   * Google Workspace administrator privileges. The calling user must be a Google
   * Workspace administrator with the [manage chat and spaces conversations
   * privilege](https://support.google.com/a/answer/13369245). Requires either the
   * `chat.admin.spaces.readonly` or `chat.admin.spaces` [OAuth 2.0
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes). This method currently only supports admin access,
   * thus only `true` is accepted for this field.
   * @return SearchSpacesResponse
   * @throws \Google\Service\Exception
   */
  public function search($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], SearchSpacesResponse::class);
  }
  /**
   * Creates a space and adds specified users to it. The calling user is
   * automatically added to the space, and shouldn't be specified as a membership
   * in the request. For an example, see [Set up a space with initial
   * members](https://developers.google.com/workspace/chat/set-up-spaces). To
   * specify the human members to add, add memberships with the appropriate
   * `membership.member.name`. To add a human user, use `users/{user}`, where
   * `{user}` can be the email address for the user. For users in the same
   * Workspace organization `{user}` can also be the `id` for the person from the
   * People API, or the `id` for the user in the Directory API. For example, if
   * the People API Person profile ID for `user@example.com` is `123456789`, you
   * can add the user to the space by setting the `membership.member.name` to
   * `users/user@example.com` or `users/123456789`. To specify the Google groups
   * to add, add memberships with the appropriate `membership.group_member.name`.
   * To add or invite a Google group, use `groups/{group}`, where `{group}` is the
   * `id` for the group from the Cloud Identity Groups API. For example, you can
   * use [Cloud Identity Groups lookup
   * API](https://cloud.google.com/identity/docs/reference/rest/v1/groups/lookup)
   * to retrieve the ID `123456789` for group email `group@example.com`, then you
   * can add the group to the space by setting the `membership.group_member.name`
   * to `groups/123456789`. Group email is not supported, and Google groups can
   * only be added as members in named spaces. For a named space or group chat, if
   * the caller blocks, or is blocked by some members, or doesn't have permission
   * to add some members, then those members aren't added to the created space. To
   * create a direct message (DM) between the calling user and another human user,
   * specify exactly one membership to represent the human user. If one user
   * blocks the other, the request fails and the DM isn't created. To create a DM
   * between the calling user and the calling app, set `Space.singleUserBotDm` to
   * `true` and don't specify any memberships. You can only use this method to set
   * up a DM with the calling app. To add the calling app as a member of a space
   * or an existing DM between two human users, see [Invite or add a user or app
   * to a space](https://developers.google.com/workspace/chat/create-members). If
   * a DM already exists between two users, even when one user blocks the other at
   * the time a request is made, then the existing DM is returned. Spaces with
   * threaded replies aren't supported. If you receive the error message
   * `ALREADY_EXISTS` when setting up a space, try a different `displayName`. An
   * existing space within the Google Workspace organization might already use
   * this display name. Requires [user
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.spaces.create` -
   * `https://www.googleapis.com/auth/chat.spaces` (spaces.setup)
   *
   * @param SetUpSpaceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Space
   * @throws \Google\Service\Exception
   */
  public function setup(SetUpSpaceRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setup', [$params], Space::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Spaces::class, 'Google_Service_HangoutsChat_Resource_Spaces');
