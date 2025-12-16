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

namespace Google\Service\Dialogflow\Resource;

use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3ListPlaybookVersionsResponse;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3PlaybookVersion;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3RestorePlaybookVersionRequest;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3RestorePlaybookVersionResponse;
use Google\Service\Dialogflow\GoogleProtobufEmpty;

/**
 * The "versions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dialogflowService = new Google\Service\Dialogflow(...);
 *   $versions = $dialogflowService->projects_locations_agents_playbooks_versions;
 *  </code>
 */
class ProjectsLocationsAgentsPlaybooksVersions extends \Google\Service\Resource
{
  /**
   * Creates a version for the specified Playbook. (versions.create)
   *
   * @param string $parent Required. The playbook to create a version for. Format:
   * `projects//locations//agents//playbooks/`.
   * @param GoogleCloudDialogflowCxV3PlaybookVersion $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3PlaybookVersion
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDialogflowCxV3PlaybookVersion $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDialogflowCxV3PlaybookVersion::class);
  }
  /**
   * Deletes the specified version of the Playbook. (versions.delete)
   *
   * @param string $name Required. The name of the playbook version to delete.
   * Format: `projects//locations//agents//playbooks//versions/`.
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Retrieves the specified version of the Playbook. (versions.get)
   *
   * @param string $name Required. The name of the playbook version. Format:
   * `projects//locations//agents//playbooks//versions/`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3PlaybookVersion
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDialogflowCxV3PlaybookVersion::class);
  }
  /**
   * Lists versions for the specified Playbook.
   * (versions.listProjectsLocationsAgentsPlaybooksVersions)
   *
   * @param string $parent Required. The playbook to list versions for. Format:
   * `projects//locations//agents//playbooks/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return in a
   * single page. By default 100 and at most 1000.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous list request.
   * @return GoogleCloudDialogflowCxV3ListPlaybookVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAgentsPlaybooksVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDialogflowCxV3ListPlaybookVersionsResponse::class);
  }
  /**
   * Retrieves the specified version of the Playbook and stores it as the current
   * playbook draft, returning the playbook with resources updated.
   * (versions.restore)
   *
   * @param string $name Required. The name of the playbook version. Format:
   * `projects//locations//agents//playbooks//versions/`.
   * @param GoogleCloudDialogflowCxV3RestorePlaybookVersionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3RestorePlaybookVersionResponse
   * @throws \Google\Service\Exception
   */
  public function restore($name, GoogleCloudDialogflowCxV3RestorePlaybookVersionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], GoogleCloudDialogflowCxV3RestorePlaybookVersionResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAgentsPlaybooksVersions::class, 'Google_Service_Dialogflow_Resource_ProjectsLocationsAgentsPlaybooksVersions');
