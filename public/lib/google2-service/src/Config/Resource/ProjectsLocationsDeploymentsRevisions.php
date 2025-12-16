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

namespace Google\Service\Config\Resource;

use Google\Service\Config\ExportRevisionStatefileRequest;
use Google\Service\Config\ListRevisionsResponse;
use Google\Service\Config\Revision;
use Google\Service\Config\Statefile;

/**
 * The "revisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $configService = new Google\Service\Config(...);
 *   $revisions = $configService->projects_locations_deployments_revisions;
 *  </code>
 */
class ProjectsLocationsDeploymentsRevisions extends \Google\Service\Resource
{
  /**
   * Exports Terraform state file from a given revision. (revisions.exportState)
   *
   * @param string $parent Required. The parent in whose context the statefile is
   * listed. The parent value is in the format: 'projects/{project_id}/locations/{
   * location}/deployments/{deployment}/revisions/{revision}'.
   * @param ExportRevisionStatefileRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Statefile
   * @throws \Google\Service\Exception
   */
  public function exportState($parent, ExportRevisionStatefileRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportState', [$params], Statefile::class);
  }
  /**
   * Gets details about a Revision. (revisions.get)
   *
   * @param string $name Required. The name of the Revision in the format: 'projec
   * ts/{project_id}/locations/{location}/deployments/{deployment}/revisions/{revi
   * sion}'.
   * @param array $optParams Optional parameters.
   * @return Revision
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Revision::class);
  }
  /**
   * Lists Revisions of a deployment.
   * (revisions.listProjectsLocationsDeploymentsRevisions)
   *
   * @param string $parent Required. The parent in whose context the Revisions are
   * listed. The parent value is in the format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the Revisions that match the filter
   * expression. A filter expression filters the resources listed in the response.
   * The expression must be of the form '{field} {operator} {value}' where
   * operators: '<', '>', '<=', '>=', '!=', '=', ':' are supported (colon ':'
   * represents a HAS operator which is roughly synonymous with equality). {field}
   * can refer to a proto or JSON field, or a synthetic field. Field names can be
   * camelCase or snake_case. Examples: - Filter by name: name =
   * "projects/foo/locations/us-central1/deployments/dep/revisions/bar - Filter by
   * labels: - Resources that have a key called 'foo' labels.foo:* - Resources
   * that have a key called 'foo' whose value is 'bar' labels.foo = bar - Filter
   * by state: - Revisions in CREATING state. state=CREATING
   * @opt_param string orderBy Field to use to sort the list.
   * @opt_param int pageSize When requesting a page of resources, `page_size`
   * specifies number of resources to return. If unspecified, at most 500 will be
   * returned. The maximum value is 1000.
   * @opt_param string pageToken Token returned by previous call to
   * 'ListRevisions' which specifies the position in the list from where to
   * continue listing the resources.
   * @return ListRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDeploymentsRevisions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRevisionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDeploymentsRevisions::class, 'Google_Service_Config_Resource_ProjectsLocationsDeploymentsRevisions');
