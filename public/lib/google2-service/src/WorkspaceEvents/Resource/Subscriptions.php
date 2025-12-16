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

namespace Google\Service\WorkspaceEvents\Resource;

use Google\Service\WorkspaceEvents\ListSubscriptionsResponse;
use Google\Service\WorkspaceEvents\Operation;
use Google\Service\WorkspaceEvents\ReactivateSubscriptionRequest;
use Google\Service\WorkspaceEvents\Subscription;

/**
 * The "subscriptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workspaceeventsService = new Google\Service\WorkspaceEvents(...);
 *   $subscriptions = $workspaceeventsService->subscriptions;
 *  </code>
 */
class Subscriptions extends \Google\Service\Resource
{
  /**
   * Creates a Google Workspace subscription. To learn how to use this method, see
   * [Create a Google Workspace
   * subscription](https://developers.google.com/workspace/events/guides/create-
   * subscription). For a subscription on a [Chat target
   * resource](https://developers.google.com/workspace/events/guides/events-chat),
   * you can create a subscription as: - A Chat app by specifying an authorization
   * scope that begins with `chat.app` and getting one-time administrator approval
   * ([Developer Preview](https://developers.google.com/workspace/preview)). To
   * learn more, see [Authorize as a Chat app with administrator
   * approval](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app). - A user by specifying an authorization scope that
   * doesn't include `app` in its name. To learn more, see [Authorize as a Chat
   * user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user). (subscriptions.create)
   *
   * @param Subscription $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool validateOnly Optional. If set to `true`, validates and
   * previews the request, but doesn't create the subscription.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create(Subscription $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a Google Workspace subscription. To learn how to use this method, see
   * [Delete a Google Workspace
   * subscription](https://developers.google.com/workspace/events/guides/delete-
   * subscription). (subscriptions.delete)
   *
   * @param string $name Required. Resource name of the subscription to delete.
   * Format: `subscriptions/{subscription}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to `true` and the subscription
   * isn't found, the request succeeds but doesn't delete the subscription.
   * @opt_param string etag Optional. Etag of the subscription. If present, it
   * must match with the server's etag. Otherwise, request fails with the status
   * `ABORTED`.
   * @opt_param bool validateOnly Optional. If set to `true`, validates and
   * previews the request, but doesn't delete the subscription.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details about a Google Workspace subscription. To learn how to use this
   * method, see [Get details about a Google Workspace
   * subscription](https://developers.google.com/workspace/events/guides/get-
   * subscription). (subscriptions.get)
   *
   * @param string $name Required. Resource name of the subscription. Format:
   * `subscriptions/{subscription}`
   * @param array $optParams Optional parameters.
   * @return Subscription
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Subscription::class);
  }
  /**
   * Lists Google Workspace subscriptions. To learn how to use this method, see
   * [List Google Workspace
   * subscriptions](https://developers.google.com/workspace/events/guides/list-
   * subscriptions). (subscriptions.listSubscriptions)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Required. A query filter. You can filter
   * subscriptions by event type (`event_types`) and target resource
   * (`target_resource`). You must specify at least one event type in your query.
   * To filter for multiple event types, use the `OR` operator. To filter by both
   * event type and target resource, use the `AND` operator and specify the full
   * resource name, such as `//chat.googleapis.com/spaces/{space}`. For example,
   * the following queries are valid: ```
   * event_types:"google.workspace.chat.membership.v1.updated" OR
   * event_types:"google.workspace.chat.message.v1.created"
   * event_types:"google.workspace.chat.message.v1.created" AND
   * target_resource="//chat.googleapis.com/spaces/{space}" (
   * event_types:"google.workspace.chat.membership.v1.updated" OR
   * event_types:"google.workspace.chat.message.v1.created" ) AND
   * target_resource="//chat.googleapis.com/spaces/{space}" ``` The server rejects
   * invalid queries with an `INVALID_ARGUMENT` error.
   * @opt_param int pageSize Optional. The maximum number of subscriptions to
   * return. The service might return fewer than this value. If unspecified or set
   * to `0`, up to 50 subscriptions are returned. The maximum value is 100. If you
   * specify a value more than 100, the system only returns 100 subscriptions.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * list subscriptions call. Provide this parameter to retrieve the subsequent
   * page. When paginating, the filter value should match the call that provided
   * the page token. Passing a different value might lead to unexpected results.
   * @return ListSubscriptionsResponse
   * @throws \Google\Service\Exception
   */
  public function listSubscriptions($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSubscriptionsResponse::class);
  }
  /**
   * Updates or renews a Google Workspace subscription. To learn how to use this
   * method, see [Update or renew a Google Workspace
   * subscription](https://developers.google.com/workspace/events/guides/update-
   * subscription). For a subscription on a [Chat target
   * resource](https://developers.google.com/workspace/events/guides/events-chat),
   * you can update a subscription as: - A Chat app by specifying an authorization
   * scope that begins with `chat.app` andgetting one-time administrator approval
   * ([Developer Preview](https://developers.google.com/workspace/preview)). To
   * learn more, see [Authorize as a Chat app with administrator
   * approval](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app). - A user by specifying an authorization scope that
   * doesn't include `app` in its name. To learn more, see [Authorize as a Chat
   * user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user). (subscriptions.patch)
   *
   * @param string $name Identifier. Resource name of the subscription. Format:
   * `subscriptions/{subscription}`
   * @param Subscription $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The field to update. If omitted,
   * updates any fields included in the request. You can update one of the
   * following fields in a subscription: * `expire_time`: The timestamp when the
   * subscription expires. * `ttl`: The time-to-live (TTL) or duration of the
   * subscription. * `event_types`: The list of event types to receive about the
   * target resource. When using the `*` wildcard (equivalent to `PUT`), omitted
   * fields are set to empty values and rejected if they're invalid.
   * @opt_param bool validateOnly Optional. If set to `true`, validates and
   * previews the request, but doesn't update the subscription.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Subscription $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Reactivates a suspended Google Workspace subscription. This method resets
   * your subscription's `State` field to `ACTIVE`. Before you use this method,
   * you must fix the error that suspended the subscription. This method will
   * ignore or reject any subscription that isn't currently in a suspended state.
   * To learn how to use this method, see [Reactivate a Google Workspace subscript
   * ion](https://developers.google.com/workspace/events/guides/reactivate-
   * subscription). For a subscription on a [Chat target
   * resource](https://developers.google.com/workspace/events/guides/events-chat),
   * you can reactivate a subscription as: - A Chat app by specifying an
   * authorization scope that begins with `chat.app` andgetting one-time
   * administrator approval ([Developer
   * Preview](https://developers.google.com/workspace/preview)). To learn more,
   * see [Authorize as a Chat app with administrator
   * approval](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app). - A user by specifying an authorization scope that
   * doesn't include `app` in its name. To learn more, see [Authorize as a Chat
   * user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user). (subscriptions.reactivate)
   *
   * @param string $name Required. Resource name of the subscription. Format:
   * `subscriptions/{subscription}`
   * @param ReactivateSubscriptionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function reactivate($name, ReactivateSubscriptionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reactivate', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subscriptions::class, 'Google_Service_WorkspaceEvents_Resource_Subscriptions');
