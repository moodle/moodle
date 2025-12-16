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

namespace Google\Service\Integrations\Resource;

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaDownloadExecutionResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaExecution;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaListExecutionsResponse;

/**
 * The "executions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $executions = $integrationsService->projects_locations_products_integrations_executions;
 *  </code>
 */
class ProjectsLocationsProductsIntegrationsExecutions extends \Google\Service\Resource
{
  /**
   * Download the execution. (executions.download)
   *
   * @param string $name Required. The execution resource name. Format: projects/{
   * gcp_project_id}/locations/{location}/products/{product}/integrations/{integra
   * tion_id}/executions/{execution_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaDownloadExecutionResponse
   * @throws \Google\Service\Exception
   */
  public function download($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('download', [$params], GoogleCloudIntegrationsV1alphaDownloadExecutionResponse::class);
  }
  /**
   * Get an execution in the specified project. (executions.get)
   *
   * @param string $name Required. The execution resource name. Format: projects/{
   * gcp_project_id}/locations/{location}/products/{product}/integrations/{integra
   * tion_id}/executions/{execution_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaExecution
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudIntegrationsV1alphaExecution::class);
  }
  /**
   * Lists the results of all the integration executions. The response includes
   * the same information as the [execution
   * log](https://cloud.google.com/application-integration/docs/viewing-logs) in
   * the Integration UI.
   * (executions.listProjectsLocationsProductsIntegrationsExecutions)
   *
   * @param string $parent Required. The parent resource name of the integration
   * execution.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Standard filter field, we support
   * filtering on following fields: workflow_name: the name of the integration.
   * CreateTimestamp: the execution created time. event_execution_state: the state
   * of the executions. execution_id: the id of the execution. trigger_id: the id
   * of the trigger. parameter_type: the type of the parameters involved in the
   * execution. All fields support for EQUALS, in additional: CreateTimestamp
   * support for LESS_THAN, GREATER_THAN ParameterType support for HAS For
   * example: "parameter_type" HAS \"string\" Also supports operators like AND,
   * OR, NOT For example, trigger_id=\"id1\" AND workflow_name=\"testWorkflow\"
   * @opt_param string filterParams.customFilter Optional user-provided custom
   * filter.
   * @opt_param string filterParams.endTime End timestamp.
   * @opt_param string filterParams.eventStatuses List of possible event statuses.
   * @opt_param string filterParams.executionId Execution id.
   * @opt_param string filterParams.parameterKey Param key. DEPRECATED. User
   * parameter_pair_key instead.
   * @opt_param string filterParams.parameterPairKey Param key in the key value
   * pair filter.
   * @opt_param string filterParams.parameterPairValue Param value in the key
   * value pair filter.
   * @opt_param string filterParams.parameterType Param type.
   * @opt_param string filterParams.parameterValue Param value. DEPRECATED. User
   * parameter_pair_value instead.
   * @opt_param string filterParams.startTime Start timestamp.
   * @opt_param string filterParams.taskStatuses List of possible task statuses.
   * @opt_param string filterParams.workflowName Workflow name.
   * @opt_param string orderBy Optional. The results would be returned in order
   * you specified here. Currently supporting "create_time".
   * @opt_param int pageSize Optional. The size of entries in the response.
   * @opt_param string pageToken Optional. The token returned in the previous
   * response.
   * @opt_param string readMask Optional. View mask for the response data. If set,
   * only the field specified will be returned as part of the result. If not set,
   * all fields in Execution will be filled and returned. Supported fields:
   * trigger_id execution_method create_time update_time execution_details
   * execution_details.state execution_details.execution_snapshots
   * execution_details.attempt_stats
   * execution_details.event_execution_snapshots_size request_parameters
   * cloud_logging_details snapshot_number replay_info
   * @opt_param bool refreshAcl Optional. If true, the service will use the most
   * recent acl information to list event execution infos and renew the acl cache.
   * Note that fetching the most recent acl is synchronous, so it will increase
   * RPC call latency.
   * @opt_param bool snapshotMetadataWithoutParams Optional. If true, the service
   * will provide execution info with snapshot metadata only i.e. without event
   * parameters.
   * @opt_param bool truncateParams Optional. If true, the service will truncate
   * the params to only keep the first 1000 characters of string params and empty
   * the executions in order to make response smaller. Only works for UI and when
   * the params fields are not filtered out.
   * @return GoogleCloudIntegrationsV1alphaListExecutionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsProductsIntegrationsExecutions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudIntegrationsV1alphaListExecutionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsProductsIntegrationsExecutions::class, 'Google_Service_Integrations_Resource_ProjectsLocationsProductsIntegrationsExecutions');
