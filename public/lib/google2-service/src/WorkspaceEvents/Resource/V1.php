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

use Google\Service\WorkspaceEvents\AgentCard;

/**
 * The "v1" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workspaceeventsService = new Google\Service\WorkspaceEvents(...);
 *   $v1 = $workspaceeventsService->v1;
 *  </code>
 */
class V1 extends \Google\Service\Resource
{
  /**
   * GetAgentCard returns the agent card for the agent. (v1.getCard)
   *
   * @param array $optParams Optional parameters.
   * @return AgentCard
   * @throws \Google\Service\Exception
   */
  public function getCard($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('getCard', [$params], AgentCard::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V1::class, 'Google_Service_WorkspaceEvents_Resource_V1');
