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

namespace Google\Service\Script\Resource;

use Google\Service\Script\ExecutionRequest;
use Google\Service\Script\Operation;

/**
 * The "scripts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $scriptService = new Google\Service\Script(...);
 *   $scripts = $scriptService->scripts;
 *  </code>
 */
class Scripts extends \Google\Service\Resource
{
  /**
   * (scripts.run)
   *
   * @param string $scriptId The script ID of the script to be executed. Find the
   * script ID on the **Project settings** page under "IDs." As multiple
   * executable APIs can be deployed in new IDE for same script, this field should
   * be populated with DeploymentID generated while deploying in new IDE instead
   * of script ID.
   * @param ExecutionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function run($scriptId, ExecutionRequest $postBody, $optParams = [])
  {
    $params = ['scriptId' => $scriptId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('run', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Scripts::class, 'Google_Service_Script_Resource_Scripts');
