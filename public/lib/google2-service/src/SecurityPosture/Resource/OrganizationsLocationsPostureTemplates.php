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

namespace Google\Service\SecurityPosture\Resource;

use Google\Service\SecurityPosture\ListPostureTemplatesResponse;
use Google\Service\SecurityPosture\PostureTemplate;

/**
 * The "postureTemplates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitypostureService = new Google\Service\SecurityPosture(...);
 *   $postureTemplates = $securitypostureService->organizations_locations_postureTemplates;
 *  </code>
 */
class OrganizationsLocationsPostureTemplates extends \Google\Service\Resource
{
  /**
   * Gets a single revision of a PostureTemplate. (postureTemplates.get)
   *
   * @param string $name Required. The name of the PostureTemplate, in the format
   * `organizations/{organization}/locations/global/postureTemplates/{posture_temp
   * late}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string revisionId Optional. The posture template revision to
   * retrieve. If not specified, the most recently updated revision is retrieved.
   * @return PostureTemplate
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PostureTemplate::class);
  }
  /**
   * Lists every PostureTemplate in a given organization and location.
   * (postureTemplates.listOrganizationsLocationsPostureTemplates)
   *
   * @param string $parent Required. The parent resource name, in the format
   * `organizations/{organization}/locations/global`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to apply to the list of postures,
   * in the format defined in [AIP-160: Filtering](https://google.aip.dev/160).
   * @opt_param int pageSize Optional. The maximum number of posture templates to
   * return. The default value is `500`. If you exceed the maximum value of
   * `1000`, then the service uses the maximum value.
   * @opt_param string pageToken Optional. A pagination token returned from a
   * previous request to list posture templates. Provide this token to retrieve
   * the next page of results.
   * @return ListPostureTemplatesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsPostureTemplates($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPostureTemplatesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsPostureTemplates::class, 'Google_Service_SecurityPosture_Resource_OrganizationsLocationsPostureTemplates');
