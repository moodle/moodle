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
use Google\Service\Firebasestorage\FirebasestorageEmpty;

/**
 * The "projects" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebasestorageService = new Google\Service\Firebasestorage(...);
 *   $projects = $firebasestorageService->projects;
 *  </code>
 */
class Projects extends \Google\Service\Resource
{
  /**
   * Unlinks and deletes the default bucket. (projects.deleteDefaultBucket)
   *
   * @param string $name Required. The name of the default bucket to delete,
   * `projects/{project_id_or_number}/defaultBucket`.
   * @param array $optParams Optional parameters.
   * @return FirebasestorageEmpty
   * @throws \Google\Service\Exception
   */
  public function deleteDefaultBucket($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('deleteDefaultBucket', [$params], FirebasestorageEmpty::class);
  }
  /**
   * Gets the default bucket. (projects.getDefaultBucket)
   *
   * @param string $name Required. The name of the default bucket to retrieve,
   * `projects/{project_id_or_number}/defaultBucket`.
   * @param array $optParams Optional parameters.
   * @return DefaultBucket
   * @throws \Google\Service\Exception
   */
  public function getDefaultBucket($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDefaultBucket', [$params], DefaultBucket::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Projects::class, 'Google_Service_Firebasestorage_Resource_Projects');
