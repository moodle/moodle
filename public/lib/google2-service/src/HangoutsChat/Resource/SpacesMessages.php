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
use Google\Service\HangoutsChat\ListMessagesResponse;
use Google\Service\HangoutsChat\Message;

/**
 * The "messages" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $messages = $chatService->spaces_messages;
 *  </code>
 */
class SpacesMessages extends \Google\Service\Resource
{
  /**
   * Creates a message in a Google Chat space. For an example, see [Send a
   * message](https://developers.google.com/workspace/chat/create-messages).
   * Supports the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with the authorization scope: -
   * `https://www.googleapis.com/auth/chat.bot` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.messages.create` -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only) Chat
   * attributes the message sender differently depending on the type of
   * authentication that you use in your request. The following image shows how
   * Chat attributes a message when you use app authentication. Chat displays the
   * Chat app as the message sender. The content of the message can contain text
   * (`text`), cards (`cardsV2`), and accessory widgets (`accessoryWidgets`).
   * ![Message sent with app
   * authentication](https://developers.google.com/workspace/chat/images/message-
   * app-auth.svg) The following image shows how Chat attributes a message when
   * you use user authentication. Chat displays the user as the message sender and
   * attributes the Chat app to the message by displaying its name. The content of
   * message can only contain text (`text`). ![Message sent with user
   * authentication](https://developers.google.com/workspace/chat/images/message-
   * user-auth.svg) The maximum message size, including the message contents, is
   * 32,000 bytes. For
   * [webhook](https://developers.google.com/workspace/chat/quickstart/webhooks)
   * requests, the response doesn't contain the full message. The response only
   * populates the `name` and `thread.name` fields in addition to the information
   * that was in the request. (messages.create)
   *
   * @param string $parent Required. The resource name of the space in which to
   * create a message. Format: `spaces/{space}`
   * @param Message $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string messageId Optional. A custom ID for a message. Lets Chat
   * apps get, update, or delete a message without needing to store the system-
   * assigned ID in the message's resource name (represented in the message `name`
   * field). The value for this field must meet the following requirements: *
   * Begins with `client-`. For example, `client-custom-name` is a valid custom
   * ID, but `custom-name` is not. * Contains up to 63 characters and only
   * lowercase letters, numbers, and hyphens. * Is unique within a space. A Chat
   * app can't use the same custom ID for different messages. For details, see
   * [Name a message](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
   * @opt_param string messageReplyOption Optional. Specifies whether a message
   * starts a thread or replies to one. Only supported in named spaces. When
   * [responding to user
   * interactions](https://developers.google.com/workspace/chat/receive-respond-
   * interactions), this field is ignored. For interactions within a thread, the
   * reply is created in the same thread. Otherwise, the reply is created as a new
   * thread.
   * @opt_param string requestId Optional. A unique request ID for this message.
   * Specifying an existing request ID returns the message created with that ID
   * instead of creating a new message.
   * @opt_param string threadKey Optional. Deprecated: Use thread.thread_key
   * instead. ID for the thread. Supports up to 4000 characters. To start or add
   * to a thread, create a message and specify a `threadKey` or the thread.name.
   * For example usage, see [Start or reply to a message
   * thread](https://developers.google.com/workspace/chat/create-messages#create-
   * message-thread).
   * @return Message
   * @throws \Google\Service\Exception
   */
  public function create($parent, Message $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Message::class);
  }
  /**
   * Deletes a message. For an example, see [Delete a
   * message](https://developers.google.com/workspace/chat/delete-messages).
   * Supports the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with the authorization scope: -
   * `https://www.googleapis.com/auth/chat.bot` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only) When
   * using app authentication, requests can only delete messages created by the
   * calling Chat app. (messages.delete)
   *
   * @param string $name Required. Resource name of the message. Format:
   * `spaces/{space}/messages/{message}` If you've set a custom ID for your
   * message, you can use the value from the `clientAssignedMessageId` field for
   * `{message}`. For details, see [Name a message]
   * (https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. When `true`, deleting a message also deletes
   * its threaded replies. When `false`, if a message has threaded replies,
   * deletion fails. Only applies when [authenticating as a
   * user](https://developers.google.com/workspace/chat/authenticate-authorize-
   * chat-user). Has no effect when [authenticating as a Chat app]
   * (https://developers.google.com/workspace/chat/authenticate-authorize-chat-
   * app).
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
   * Returns details about a message. For an example, see [Get details about a
   * message](https://developers.google.com/workspace/chat/get-messages). Supports
   * the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.bot`: When using this authorization
   * scope, this method returns details about a message the Chat app has access
   * to, like direct messages and [slash
   * commands](https://developers.google.com/workspace/chat/slash-commands) that
   * invoke the Chat app. -
   * `https://www.googleapis.com/auth/chat.app.messages.readonly` with
   * [administrator approval](https://support.google.com/a?p=chat-app-auth)
   * (available in [Developer
   * Preview](https://developers.google.com/workspace/preview)). When using this
   * authentication scope, this method returns details about a public message in a
   * space. - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.messages.readonly` -
   * `https://www.googleapis.com/auth/chat.messages` Note: Might return a message
   * from a blocked member or space. (messages.get)
   *
   * @param string $name Required. Resource name of the message. Format:
   * `spaces/{space}/messages/{message}` If you've set a custom ID for your
   * message, you can use the value from the `clientAssignedMessageId` field for
   * `{message}`. For details, see [Name a message]
   * (https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
   * @param array $optParams Optional parameters.
   * @return Message
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Message::class);
  }
  /**
   * Lists messages in a space that the caller is a member of, including messages
   * from blocked members and spaces. System messages, like those announcing new
   * space members, aren't included. If you list messages from a space with no
   * messages, the response is an empty object. When using a REST/HTTP interface,
   * the response contains an empty JSON object, `{}`. For an example, see [List m
   * essages](https://developers.google.com/workspace/chat/api/guides/v1/messages/
   * list). Supports the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with [administrator
   * approval](https://support.google.com/a?p=chat-app-auth) in [Developer
   * Preview](https://developers.google.com/workspace/preview) with the
   * authorization scope: -
   * `https://www.googleapis.com/auth/chat.app.messages.readonly`. When using this
   * authentication scope, this method only returns public messages in a space. It
   * doesn't include private messages. - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.messages.readonly` -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only)
   * (messages.listSpacesMessages)
   *
   * @param string $parent Required. The resource name of the space to list
   * messages from. Format: `spaces/{space}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A query filter. You can filter messages by
   * date (`create_time`) and thread (`thread.name`). To filter messages by the
   * date they were created, specify the `create_time` with a timestamp in
   * [RFC-3339](https://www.rfc-editor.org/rfc/rfc3339) format and double
   * quotation marks. For example, `"2023-04-21T11:30:00-04:00"`. You can use the
   * greater than operator `>` to list messages that were created after a
   * timestamp, or the less than operator `<` to list messages that were created
   * before a timestamp. To filter messages within a time interval, use the `AND`
   * operator between two timestamps. To filter by thread, specify the
   * `thread.name`, formatted as `spaces/{space}/threads/{thread}`. You can only
   * specify one `thread.name` per query. To filter by both thread and date, use
   * the `AND` operator in your query. For example, the following queries are
   * valid: ``` create_time > "2012-04-21T11:30:00-04:00" create_time >
   * "2012-04-21T11:30:00-04:00" AND thread.name = spaces/AAAAAAAAAAA/threads/123
   * create_time > "2012-04-21T11:30:00+00:00" AND create_time <
   * "2013-01-01T00:00:00+00:00" AND thread.name = spaces/AAAAAAAAAAA/threads/123
   * thread.name = spaces/AAAAAAAAAAA/threads/123 ``` Invalid queries are rejected
   * by the server with an `INVALID_ARGUMENT` error.
   * @opt_param string orderBy Optional. How the list of messages is ordered.
   * Specify a value to order by an ordering operation. Valid ordering operation
   * values are as follows: - `ASC` for ascending. - `DESC` for descending. The
   * default ordering is `create_time ASC`.
   * @opt_param int pageSize Optional. The maximum number of messages returned.
   * The service might return fewer messages than this value. If unspecified, at
   * most 25 are returned. The maximum value is 1000. If you use a value more than
   * 1000, it's automatically changed to 1000. Negative values return an
   * `INVALID_ARGUMENT` error.
   * @opt_param string pageToken Optional. A page token received from a previous
   * list messages call. Provide this parameter to retrieve the subsequent page.
   * When paginating, all other parameters provided should match the call that
   * provided the page token. Passing different values to the other parameters
   * might lead to unexpected results.
   * @opt_param bool showDeleted Optional. Whether to include deleted messages.
   * Deleted messages include deleted time and metadata about their deletion, but
   * message content is unavailable.
   * @return ListMessagesResponse
   * @throws \Google\Service\Exception
   */
  public function listSpacesMessages($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMessagesResponse::class);
  }
  /**
   * Updates a message. There's a difference between the `patch` and `update`
   * methods. The `patch` method uses a `patch` request while the `update` method
   * uses a `put` request. We recommend using the `patch` method. For an example,
   * see [Update a message](https://developers.google.com/workspace/chat/update-
   * messages). Supports the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with the authorization scope: -
   * `https://www.googleapis.com/auth/chat.bot` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only) When
   * using app authentication, requests can only update messages created by the
   * calling Chat app. (messages.patch)
   *
   * @param string $name Identifier. Resource name of the message. Format:
   * `spaces/{space}/messages/{message}` Where `{space}` is the ID of the space
   * where the message is posted and `{message}` is a system-assigned ID for the
   * message. For example, `spaces/AAAAAAAAAAA/messages/BBBBBBBBBBB.BBBBBBBBBBB`.
   * If you set a custom ID when you create a message, you can use this ID to
   * specify the message in a request by replacing `{message}` with the value from
   * the `clientAssignedMessageId` field. For example,
   * `spaces/AAAAAAAAAAA/messages/client-custom-name`. For details, see [Name a
   * message](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
   * @param Message $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If `true` and the message isn't found,
   * a new message is created and `updateMask` is ignored. The specified message
   * ID must be [client-
   * assigned](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message) or the request fails.
   * @opt_param string updateMask Required. The field paths to update. Separate
   * multiple values with commas or use `*` to update all field paths. Currently
   * supported field paths: - `text` - `attachment` - `cards` (Requires [app
   * authentication](/chat/api/guides/auth/service-accounts).) - `cards_v2`
   * (Requires [app authentication](/chat/api/guides/auth/service-accounts).) -
   * `accessory_widgets` (Requires [app
   * authentication](/chat/api/guides/auth/service-accounts).) -
   * `quoted_message_metadata` (Only allows removal of the quoted message.)
   * @return Message
   * @throws \Google\Service\Exception
   */
  public function patch($name, Message $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Message::class);
  }
  /**
   * Updates a message. There's a difference between the `patch` and `update`
   * methods. The `patch` method uses a `patch` request while the `update` method
   * uses a `put` request. We recommend using the `patch` method. For an example,
   * see [Update a message](https://developers.google.com/workspace/chat/update-
   * messages). Supports the following types of
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize): - [App
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-app) with the authorization scope: -
   * `https://www.googleapis.com/auth/chat.bot` - [User
   * authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following authorization scopes: -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only) When
   * using app authentication, requests can only update messages created by the
   * calling Chat app. (messages.update)
   *
   * @param string $name Identifier. Resource name of the message. Format:
   * `spaces/{space}/messages/{message}` Where `{space}` is the ID of the space
   * where the message is posted and `{message}` is a system-assigned ID for the
   * message. For example, `spaces/AAAAAAAAAAA/messages/BBBBBBBBBBB.BBBBBBBBBBB`.
   * If you set a custom ID when you create a message, you can use this ID to
   * specify the message in a request by replacing `{message}` with the value from
   * the `clientAssignedMessageId` field. For example,
   * `spaces/AAAAAAAAAAA/messages/client-custom-name`. For details, see [Name a
   * message](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message).
   * @param Message $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If `true` and the message isn't found,
   * a new message is created and `updateMask` is ignored. The specified message
   * ID must be [client-
   * assigned](https://developers.google.com/workspace/chat/create-
   * messages#name_a_created_message) or the request fails.
   * @opt_param string updateMask Required. The field paths to update. Separate
   * multiple values with commas or use `*` to update all field paths. Currently
   * supported field paths: - `text` - `attachment` - `cards` (Requires [app
   * authentication](/chat/api/guides/auth/service-accounts).) - `cards_v2`
   * (Requires [app authentication](/chat/api/guides/auth/service-accounts).) -
   * `accessory_widgets` (Requires [app
   * authentication](/chat/api/guides/auth/service-accounts).) -
   * `quoted_message_metadata` (Only allows removal of the quoted message.)
   * @return Message
   * @throws \Google\Service\Exception
   */
  public function update($name, Message $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Message::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpacesMessages::class, 'Google_Service_HangoutsChat_Resource_SpacesMessages');
