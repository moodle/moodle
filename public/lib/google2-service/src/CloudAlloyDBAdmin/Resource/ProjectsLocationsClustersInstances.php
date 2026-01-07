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

namespace Google\Service\CloudAlloyDBAdmin\Resource;

use Google\Service\CloudAlloyDBAdmin\ConnectionInfo;
use Google\Service\CloudAlloyDBAdmin\FailoverInstanceRequest;
use Google\Service\CloudAlloyDBAdmin\InjectFaultRequest;
use Google\Service\CloudAlloyDBAdmin\Instance;
use Google\Service\CloudAlloyDBAdmin\ListInstancesResponse;
use Google\Service\CloudAlloyDBAdmin\Operation;
use Google\Service\CloudAlloyDBAdmin\RestartInstanceRequest;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $alloydbService = new Google\Service\CloudAlloyDBAdmin(...);
 *   $instances = $alloydbService->projects_locations_clusters_instances;
 *  </code>
 */
class ProjectsLocationsClustersInstances extends \Google\Service\Resource
{
  /**
   * Creates a new Instance in a given project and location. (instances.create)
   *
   * @param string $parent Required. The name of the parent resource. For the
   * required format, see the comment on the Instance.name field.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string instanceId Required. ID of the requesting object.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, performs request validation,
   * for example, permission checks and any other type of validation, but does not
   * actually execute the create request.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Instance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Creates a new SECONDARY Instance in a given project and location.
   * (instances.createsecondary)
   *
   * @param string $parent Required. The name of the parent resource. For the
   * required format, see the comment on the Instance.name field.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string instanceId Required. ID of the requesting object.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, performs request validation,
   * for example, permission checks and any other type of validation, but does not
   * actually execute the create request.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function createsecondary($parent, Instance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('createsecondary', [$params], Operation::class);
  }
  /**
   * Deletes a single Instance. (instances.delete)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Instance.name field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the Instance. If an etag
   * is provided and does not match the current etag of the Instance, deletion
   * will be blocked and an ABORTED error will be returned.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, performs request validation,
   * for example, permission checks and any other type of validation, but does not
   * actually execute the create request.
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
   * Forces a Failover for a highly available instance. Failover promotes the HA
   * standby instance as the new primary. Imperative only. (instances.failover)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Instance.name field.
   * @param FailoverInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function failover($name, FailoverInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('failover', [$params], Operation::class);
  }
  /**
   * Gets details of a single Instance. (instances.get)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Instance.name field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view The view of the instance to return.
   * @return Instance
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Instance::class);
  }
  /**
   * Get instance metadata used for a connection. (instances.getConnectionInfo)
   *
   * @param string $parent Required. The name of the parent resource. The required
   * format is: projects/{project}/locations/{location}/clusters/{cluster}/instanc
   * es/{instance}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return ConnectionInfo
   * @throws \Google\Service\Exception
   */
  public function getConnectionInfo($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('getConnectionInfo', [$params], ConnectionInfo::class);
  }
  /**
   * Injects fault in an instance. Imperative only. (instances.injectFault)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Instance.name field.
   * @param InjectFaultRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function injectFault($name, InjectFaultRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('injectFault', [$params], Operation::class);
  }
  /**
   * Lists Instances in a given project and location.
   * (instances.listProjectsLocationsClustersInstances)
   *
   * @param string $parent Required. The name of the parent resource. For the
   * required format, see the comment on the Instance.name field. Additionally,
   * you can perform an aggregated list operation by specifying a value with one
   * of the following formats: * projects/{project}/locations/-/clusters/- *
   * projects/{project}/locations/{region}/clusters/-
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListInstancesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsClustersInstances($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstancesResponse::class);
  }
  /**
   * Updates the parameters of a single Instance. (instances.patch)
   *
   * @param string $name Output only. The name of the instance resource with the
   * format: * projects/{project}/locations/{region}/clusters/{cluster_id}/instanc
   * es/{instance_id} where the cluster and instance ID segments should satisfy
   * the regex expression `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`, e.g. 1-63 characters
   * of lowercase letters, numbers, and dashes, starting with a letter, and ending
   * with a letter or number. For more details see https://google.aip.dev/122. The
   * prefix of the instance resource name is the name of the parent resource: *
   * projects/{project}/locations/{region}/clusters/{cluster_id}
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, update succeeds even
   * if instance is not found. In that case, a new instance is created and
   * `update_mask` is ignored.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Instance resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. If set, performs request validation,
   * for example, permission checks and any other type of validation, but does not
   * actually execute the create request.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Instance $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Restart an Instance in a cluster. Imperative only. (instances.restart)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Instance.name field.
   * @param RestartInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restart($name, RestartInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restart', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClustersInstances::class, 'Google_Service_CloudAlloyDBAdmin_Resource_ProjectsLocationsClustersInstances');
