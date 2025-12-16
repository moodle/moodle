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

namespace Google\Service\AndroidPublisher\Resource;

use Google\Service\AndroidPublisher\SafetyLabelsUpdateRequest;
use Google\Service\AndroidPublisher\SafetyLabelsUpdateResponse;

/**
 * The "applications" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $applications = $androidpublisherService->applications;
 *  </code>
 */
class Applications extends \Google\Service\Resource
{
  /**
   * Writes the Safety Labels declaration of an app. (applications.dataSafety)
   *
   * @param string $packageName Required. Package name of the app.
   * @param SafetyLabelsUpdateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SafetyLabelsUpdateResponse
   * @throws \Google\Service\Exception
   */
  public function dataSafety($packageName, SafetyLabelsUpdateRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('dataSafety', [$params], SafetyLabelsUpdateResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Applications::class, 'Google_Service_AndroidPublisher_Resource_Applications');
