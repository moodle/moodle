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

namespace Google\Service\ManagedKafka\Resource;

use Google\Service\ManagedKafka\CheckCompatibilityRequest;
use Google\Service\ManagedKafka\CheckCompatibilityResponse;

/**
 * The "compatibility" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $compatibility = $managedkafkaService->projects_locations_schemaRegistries_compatibility;
 *  </code>
 */
class ProjectsLocationsSchemaRegistriesCompatibility extends \Google\Service\Resource
{
  /**
   * Check compatibility of a schema with all versions or a specific version of a
   * subject. (compatibility.checkCompatibility)
   *
   * @param string $name Required. The name of the resource to check compatibility
   * for. The format is either of following: * projects/{project}/locations/{locat
   * ion}/schemaRegistries/{schema_registry}/compatibility/subjects/versions:
   * Check compatibility with one or more versions of the specified subject. * pro
   * jects/{project}/locations/{location}/schemaRegistries/{schema_registry}/compa
   * tibility/subjects/{subject}/versions/{version}: Check compatibility with a
   * specific version of the subject.
   * @param CheckCompatibilityRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CheckCompatibilityResponse
   * @throws \Google\Service\Exception
   */
  public function checkCompatibility($name, CheckCompatibilityRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('checkCompatibility', [$params], CheckCompatibilityResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchemaRegistriesCompatibility::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsSchemaRegistriesCompatibility');
