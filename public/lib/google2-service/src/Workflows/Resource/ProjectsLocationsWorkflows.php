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

namespace Google\Service\Workflows\Resource;

use Google\Service\Workflows\ListWorkflowRevisionsResponse;
use Google\Service\Workflows\ListWorkflowsResponse;
use Google\Service\Workflows\Operation;
use Google\Service\Workflows\Workflow;

/**
 * The "workflows" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workflowsService = new Google\Service\Workflows(...);
 *   $workflows = $workflowsService->projects_locations_workflows;
 *  </code>
 */
class ProjectsLocationsWorkflows extends \Google\Service\Resource
{
  /**
   * Creates a new workflow. If a workflow with the specified name already exists
   * in the specified project and location, the long running operation returns a
   * ALREADY_EXISTS error. (workflows.create)
   *
   * @param string $parent Required. Project and location in which the workflow
   * should be created. Format: projects/{project}/locations/{location}
   * @param Workflow $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string workflowId Required. The ID of the workflow to be created.
   * It has to fulfill the following requirements: * Must contain only letters,
   * numbers, underscores and hyphens. * Must start with a letter. * Must be
   * between 1-64 characters. * Must end with a number or a letter. * Must be
   * unique within the customer project and location.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Workflow $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a workflow with the specified name. This method also cancels and
   * deletes all running executions of the workflow. (workflows.delete)
   *
   * @param string $name Required. Name of the workflow to be deleted. Format:
   * projects/{project}/locations/{location}/workflows/{workflow}
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details of a single workflow. (workflows.get)
   *
   * @param string $name Required. Name of the workflow for which information
   * should be retrieved. Format:
   * projects/{project}/locations/{location}/workflows/{workflow}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string revisionId Optional. The revision of the workflow to
   * retrieve. If the revision_id is empty, the latest revision is retrieved. The
   * format is "000001-a4d", where the first six characters define the zero-padded
   * decimal revision number. They are followed by a hyphen and three hexadecimal
   * characters.
   * @return Workflow
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Workflow::class);
  }
  /**
   * Lists workflows in a given project and location. The default order is not
   * specified. (workflows.listProjectsLocationsWorkflows)
   *
   * @param string $parent Required. Project and location from which the workflows
   * should be listed. Format: projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filter to restrict results to specific workflows.
   * For details, see AIP-160. For example, if you are using the Google APIs
   * Explorer: `state="SUCCEEDED"` or `createTime>"2023-08-01" AND state="FAILED"`
   * @opt_param string orderBy Comma-separated list of fields that specify the
   * order of the results. Default sorting order for a field is ascending. To
   * specify descending order for a field, append a "desc" suffix. If not
   * specified, the results are returned in an unspecified order.
   * @opt_param int pageSize Maximum number of workflows to return per call. The
   * service might return fewer than this value even if not at the end of the
   * collection. If a value is not specified, a default value of 500 is used. The
   * maximum permitted value is 1000 and values greater than 1000 are coerced down
   * to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListWorkflows` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListWorkflows` must match the
   * call that provided the page token.
   * @return ListWorkflowsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsWorkflows($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWorkflowsResponse::class);
  }
  /**
   * Lists revisions for a given workflow. (workflows.listRevisions)
   *
   * @param string $name Required. Workflow for which the revisions should be
   * listed. Format: projects/{project}/locations/{location}/workflows/{workflow}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of revisions to return per page.
   * If a value is not specified, a default value of 20 is used. The maximum
   * permitted value is 100. Values greater than 100 are coerced down to 100.
   * @opt_param string pageToken The page token, received from a previous
   * ListWorkflowRevisions call. Provide this to retrieve the subsequent page.
   * @return ListWorkflowRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function listRevisions($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('listRevisions', [$params], ListWorkflowRevisionsResponse::class);
  }
  /**
   * Updates an existing workflow. Running this method has no impact on already
   * running executions of the workflow. A new revision of the workflow might be
   * created as a result of a successful update operation. In that case, the new
   * revision is used in new workflow executions. (workflows.patch)
   *
   * @param string $name The resource name of the workflow. Format:
   * projects/{project}/locations/{location}/workflows/{workflow}. This is a
   * workflow-wide field and is not tied to a specific revision.
   * @param Workflow $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask List of fields to be updated. If not present,
   * the entire workflow will be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Workflow $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWorkflows::class, 'Google_Service_Workflows_Resource_ProjectsLocationsWorkflows');
