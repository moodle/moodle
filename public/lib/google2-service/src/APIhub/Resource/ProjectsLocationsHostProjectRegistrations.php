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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\GoogleCloudApihubV1HostProjectRegistration;
use Google\Service\APIhub\GoogleCloudApihubV1ListHostProjectRegistrationsResponse;

/**
 * The "hostProjectRegistrations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $hostProjectRegistrations = $apihubService->projects_locations_hostProjectRegistrations;
 *  </code>
 */
class ProjectsLocationsHostProjectRegistrations extends \Google\Service\Resource
{
  /**
   * Create a host project registration. A Google cloud project can be registered
   * as a host project if it is not attached as a runtime project to another host
   * project. A project can be registered as a host project only once. Subsequent
   * register calls for the same project will fail.
   * (hostProjectRegistrations.create)
   *
   * @param string $parent Required. The parent resource for the host project.
   * Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1HostProjectRegistration $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string hostProjectRegistrationId Required. The ID to use for the
   * Host Project Registration, which will become the final component of the host
   * project registration's resource name. The ID must be the same as the Google
   * cloud project specified in the host_project_registration.gcp_project field.
   * @return GoogleCloudApihubV1HostProjectRegistration
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1HostProjectRegistration $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1HostProjectRegistration::class);
  }
  /**
   * Get a host project registration. (hostProjectRegistrations.get)
   *
   * @param string $name Required. Host project registration resource name. projec
   * ts/{project}/locations/{location}/hostProjectRegistrations/{host_project_regi
   * stration_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1HostProjectRegistration
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1HostProjectRegistration::class);
  }
  /**
   * Lists host project registrations.
   * (hostProjectRegistrations.listProjectsLocationsHostProjectRegistrations)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * host projects. Format: `projects/locations`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * HostProjectRegistrations. A filter expression consists of a field name, a
   * comparison operator, and a value for filtering. The value must be a string.
   * All standard operators as documented at https://google.aip.dev/160 are
   * supported. The following fields in the `HostProjectRegistration` are eligible
   * for filtering: * `name` - The name of the HostProjectRegistration. *
   * `create_time` - The time at which the HostProjectRegistration was created.
   * The value should be in the (RFC3339)[https://tools.ietf.org/html/rfc3339]
   * format. * `gcp_project` - The Google cloud project associated with the
   * HostProjectRegistration.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. The maximum number of host project
   * registrations to return. The service may return fewer than this value. If
   * unspecified, at most 50 host project registrations will be returned. The
   * maximum value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListHostProjectRegistrations` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters (except page_size) provided to
   * `ListHostProjectRegistrations` must match the call that provided the page
   * token.
   * @return GoogleCloudApihubV1ListHostProjectRegistrationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsHostProjectRegistrations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListHostProjectRegistrationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsHostProjectRegistrations::class, 'Google_Service_APIhub_Resource_ProjectsLocationsHostProjectRegistrations');
