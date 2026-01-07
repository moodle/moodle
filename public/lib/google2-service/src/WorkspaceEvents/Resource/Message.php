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

use Google\Service\WorkspaceEvents\SendMessageRequest;
use Google\Service\WorkspaceEvents\StreamResponse;

/**
 * The "message" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workspaceeventsService = new Google\Service\WorkspaceEvents(...);
 *   $message = $workspaceeventsService->message;
 *  </code>
 */
class Message extends \Google\Service\Resource
{
  /**
   * SendStreamingMessage is a streaming call that will return a stream of task
   * update events until the Task is in an interrupted or terminal state.
   * (message.stream)
   *
   * @param SendMessageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return StreamResponse
   * @throws \Google\Service\Exception
   */
  public function stream(SendMessageRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('stream', [$params], StreamResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_WorkspaceEvents_Resource_Message');
