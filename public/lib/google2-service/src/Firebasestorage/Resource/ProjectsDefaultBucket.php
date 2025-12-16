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

namespace Google\Service\Firebasestorage\Resource;

use Google\Service\Firebasestorage\DefaultBucket;

/**
 * The "defaultBucket" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebasestorageService = new Google\Service\Firebasestorage(...);
 *   $defaultBucket = $firebasestorageService->projects_defaultBucket;
 *  </code>
 */
class ProjectsDefaultBucket extends \Google\Service\Resource
{
  /**
   * Creates a Spark tier-eligible Cloud Storage bucket and links it to your
   * Firebase project. If the default bucket already exists, this method will re-
   * link it to your Firebase project. See https://firebase.google.com/pricing for
   * pricing details. (defaultBucket.create)
   *
   * @param string $parent Required. The parent resource where the default bucket
   * will be created, `projects/{project_id_or_number}`.
   * @param DefaultBucket $postBody
   * @param array $optParams Optional parameters.
   * @return DefaultBucket
   * @throws \Google\Service\Exception
   */
  public function create($parent, DefaultBucket $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], DefaultBucket::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsDefaultBucket::class, 'Google_Service_Firebasestorage_Resource_ProjectsDefaultBucket');
