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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1DebugSessionTransaction;

/**
 * The "data" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $data = $apigeeService->organizations_environments_apis_revisions_debugsessions_data;
 *  </code>
 */
class OrganizationsEnvironmentsApisRevisionsDebugsessionsData extends \Google\Service\Resource
{
  /**
   * Gets the debug data from a transaction. (data.get)
   *
   * @param string $name Required. The name of the debug session transaction. Must
   * be of the form: `organizations/{organization}/environments/{environment}/apis
   * /{api}/revisions/{revision}/debugsessions/{debug_session}/data/{transaction}`
   * . If the API proxy resource has the `space` attribute set, IAM permissions
   * are checked differently . To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1DebugSessionTransaction
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1DebugSessionTransaction::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsEnvironmentsApisRevisionsDebugsessionsData::class, 'Google_Service_Apigee_Resource_OrganizationsEnvironmentsApisRevisionsDebugsessionsData');
