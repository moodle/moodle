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

use Google\Service\HangoutsChat\ListSpaceEventsResponse;
use Google\Service\HangoutsChat\SpaceEvent;

/**
 * The "spaceEvents" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $spaceEvents = $chatService->spaces_spaceEvents;
 *  </code>
 */
class SpacesSpaceEvents extends \Google\Service\Resource
{
  /**
   * Returns an event from a Google Chat space. The [event payload](https://develo
   * pers.google.com/workspace/chat/api/reference/rest/v1/spaces.spaceEvents#Space
   * Event.FIELDS.oneof_payload) contains the most recent version of the resource
   * that changed. For example, if you request an event about a new message but
   * the message was later updated, the server returns the updated `Message`
   * resource in the event payload. Note: The `permissionSettings` field is not
   * returned in the Space object of the Space event data for this request.
   * Supports the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize) with an [authorization
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes) appropriate for reading the requested data: - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) in [Developer
   * Preview](https://developers.google.com/workspace/preview) with one of the
   * following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.app.spaces` -
   * `https://www.googleapis.com/auth/chat.app.messages.readonly` -
   * `https://www.googleapis.com/auth/chat.app.memberships` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.spaces.readonly` -
   * `https://www.googleapis.com/auth/chat.spaces` -
   * `https://www.googleapis.com/auth/chat.messages.readonly` -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.messages.reactions.readonly` -
   * `https://www.googleapis.com/auth/chat.messages.reactions` -
   * `https://www.googleapis.com/auth/chat.memberships.readonly` -
   * `https://www.googleapis.com/auth/chat.memberships` To get an event, the
   * authenticated caller must be a member of the space. For an example, see [Get
   * details about an event from a Google Chat
   * space](https://developers.google.com/workspace/chat/get-space-event).
   * (spaceEvents.get)
   *
   * @param string $name Required. The resource name of the space event. Format:
   * `spaces/{space}/spaceEvents/{spaceEvent}`
   * @param array $optParams Optional parameters.
   * @return SpaceEvent
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SpaceEvent::class);
  }
  /**
   * Lists events from a Google Chat space. For each event, the [payload](https://
   * developers.google.com/workspace/chat/api/reference/rest/v1/spaces.spaceEvents
   * #SpaceEvent.FIELDS.oneof_payload) contains the most recent version of the
   * Chat resource. For example, if you list events about new space members, the
   * server returns `Membership` resources that contain the latest membership
   * details. If new members were removed during the requested period, the event
   * payload contains an empty `Membership` resource. Supports the following types
   * of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize) with an [authorization
   * scope](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes) appropriate for reading the requested data: - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) in [Developer
   * Preview](https://developers.google.com/workspace/preview) with one of the
   * following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.app.spaces` -
   * `https://www.googleapis.com/auth/chat.app.messages.readonly` -
   * `https://www.googleapis.com/auth/chat.app.memberships` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.spaces.readonly` -
   * `https://www.googleapis.com/auth/chat.spaces` -
   * `https://www.googleapis.com/auth/chat.messages.readonly` -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.messages.reactions.readonly` -
   * `https://www.googleapis.com/auth/chat.messages.reactions` -
   * `https://www.googleapis.com/auth/chat.memberships.readonly` -
   * `https://www.googleapis.com/auth/chat.memberships` To list events, the
   * authenticated caller must be a member of the space. For an example, see [List
   * events from a Google Chat
   * space](https://developers.google.com/workspace/chat/list-space-events).
   * (spaceEvents.listSpacesSpaceEvents)
   *
   * @param string $parent Required. Resource name of the [Google Chat space](http
   * s://developers.google.com/workspace/chat/api/reference/rest/v1/spaces) where
   * the events occurred. Format: `spaces/{space}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Required. A query filter. You must specify at least
   * one event type (`event_type`) using the has `:` operator. To filter by
   * multiple event types, use the `OR` operator. Omit batch event types in your
   * filter. The request automatically returns any related batch events. For
   * example, if you filter by new reactions
   * (`google.workspace.chat.reaction.v1.created`), the server also returns batch
   * new reactions events (`google.workspace.chat.reaction.v1.batchCreated`). For
   * a list of supported event types, see the [`SpaceEvents` reference documentati
   * on](https://developers.google.com/workspace/chat/api/reference/rest/v1/spaces
   * .spaceEvents#SpaceEvent.FIELDS.event_type). Optionally, you can also filter
   * by start time (`start_time`) and end time (`end_time`): * `start_time`:
   * Exclusive timestamp from which to start listing space events. You can list
   * events that occurred up to 28 days ago. If unspecified, lists space events
   * from the past 28 days. * `end_time`: Inclusive timestamp until which space
   * events are listed. If unspecified, lists events up to the time of the
   * request. To specify a start or end time, use the equals `=` operator and
   * format in [RFC-3339](https://www.rfc-editor.org/rfc/rfc3339). To filter by
   * both `start_time` and `end_time`, use the `AND` operator. For example, the
   * following queries are valid: ``` start_time="2023-08-23T19:20:33+00:00" AND
   * end_time="2023-08-23T19:21:54+00:00" ``` ```
   * start_time="2023-08-23T19:20:33+00:00" AND
   * (event_types:"google.workspace.chat.space.v1.updated" OR
   * event_types:"google.workspace.chat.message.v1.created") ``` The following
   * queries are invalid: ``` start_time="2023-08-23T19:20:33+00:00" OR
   * end_time="2023-08-23T19:21:54+00:00" ``` ```
   * event_types:"google.workspace.chat.space.v1.updated" AND
   * event_types:"google.workspace.chat.message.v1.created" ``` Invalid queries
   * are rejected by the server with an `INVALID_ARGUMENT` error.
   * @opt_param int pageSize Optional. The maximum number of space events
   * returned. The service might return fewer than this value. Negative values
   * return an `INVALID_ARGUMENT` error.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * list space events call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to list space events must match the
   * call that provided the page token. Passing different values to the other
   * parameters might lead to unexpected results.
   * @return ListSpaceEventsResponse
   * @throws \Google\Service\Exception
   */
  public function listSpacesSpaceEvents($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSpaceEventsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpacesSpaceEvents::class, 'Google_Service_HangoutsChat_Resource_SpacesSpaceEvents');
