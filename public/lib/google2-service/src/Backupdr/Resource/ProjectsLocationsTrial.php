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

namespace Google\Service\Backupdr\Resource;

use Google\Service\Backupdr\EndTrialRequest;
use Google\Service\Backupdr\SubscribeTrialRequest;
use Google\Service\Backupdr\Trial;

/**
 * The "trial" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $trial = $backupdrService->projects_locations_trial;
 *  </code>
 */
class ProjectsLocationsTrial extends \Google\Service\Resource
{
  /**
   * Ends the trial for a project (trial.end)
   *
   * @param string $parent Required. The parent resource where the trial has been
   * created. Format: projects/{project}/locations/{location}
   * @param EndTrialRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Trial
   * @throws \Google\Service\Exception
   */
  public function end($parent, EndTrialRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('end', [$params], Trial::class);
  }
  /**
   * Subscribes to a trial for a project (trial.subscribe)
   *
   * @param string $parent Required. The project where this trial will be created.
   * Format: projects/{project}/locations/{location} Supported Locations are - us,
   * eu and asia.
   * @param SubscribeTrialRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Trial
   * @throws \Google\Service\Exception
   */
  public function subscribe($parent, SubscribeTrialRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('subscribe', [$params], Trial::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTrial::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsTrial');
