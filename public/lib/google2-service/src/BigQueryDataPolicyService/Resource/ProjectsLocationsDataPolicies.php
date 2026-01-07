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

namespace Google\Service\BigQueryDataPolicyService\Resource;

use Google\Service\BigQueryDataPolicyService\AddGranteesRequest;
use Google\Service\BigQueryDataPolicyService\BigquerydatapolicyEmpty;
use Google\Service\BigQueryDataPolicyService\CreateDataPolicyRequest;
use Google\Service\BigQueryDataPolicyService\DataPolicy;
use Google\Service\BigQueryDataPolicyService\GetIamPolicyRequest;
use Google\Service\BigQueryDataPolicyService\ListDataPoliciesResponse;
use Google\Service\BigQueryDataPolicyService\Policy;
use Google\Service\BigQueryDataPolicyService\RemoveGranteesRequest;
use Google\Service\BigQueryDataPolicyService\SetIamPolicyRequest;
use Google\Service\BigQueryDataPolicyService\TestIamPermissionsRequest;
use Google\Service\BigQueryDataPolicyService\TestIamPermissionsResponse;

/**
 * The "dataPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigquerydatapolicyService = new Google\Service\BigQueryDataPolicyService(...);
 *   $dataPolicies = $bigquerydatapolicyService->projects_locations_dataPolicies;
 *  </code>
 */
class ProjectsLocationsDataPolicies extends \Google\Service\Resource
{
  /**
   * Adds new grantees to a data policy. The new grantees will be added to the
   * existing grantees. If the request contains a duplicate grantee, the grantee
   * will be ignored. If the request contains a grantee that already exists, the
   * grantee will be ignored. (dataPolicies.addGrantees)
   *
   * @param string $dataPolicy Required. Resource name of this data policy, in the
   * format of `projects/{project_number}/locations/{location_id}/dataPolicies/{da
   * ta_policy_id}`.
   * @param AddGranteesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DataPolicy
   * @throws \Google\Service\Exception
   */
  public function addGrantees($dataPolicy, AddGranteesRequest $postBody, $optParams = [])
  {
    $params = ['dataPolicy' => $dataPolicy, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('addGrantees', [$params], DataPolicy::class);
  }
  /**
   * Creates a new data policy under a project with the given `data_policy_id`
   * (used as the display name), and data policy type. (dataPolicies.create)
   *
   * @param string $parent Required. Resource name of the project that the data
   * policy will belong to. The format is
   * `projects/{project_number}/locations/{location_id}`.
   * @param CreateDataPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DataPolicy
   * @throws \Google\Service\Exception
   */
  public function create($parent, CreateDataPolicyRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], DataPolicy::class);
  }
  /**
   * Deletes the data policy specified by its resource name. (dataPolicies.delete)
   *
   * @param string $name Required. Resource name of the data policy to delete.
   * Format is
   * `projects/{project_number}/locations/{location_id}/dataPolicies/{id}`.
   * @param array $optParams Optional parameters.
   * @return BigquerydatapolicyEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], BigquerydatapolicyEmpty::class);
  }
  /**
   * Gets the data policy specified by its resource name. (dataPolicies.get)
   *
   * @param string $name Required. Resource name of the requested data policy.
   * Format is
   * `projects/{project_number}/locations/{location_id}/dataPolicies/{id}`.
   * @param array $optParams Optional parameters.
   * @return DataPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DataPolicy::class);
  }
  /**
   * Gets the IAM policy for the specified data policy.
   * (dataPolicies.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, GetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * List all of the data policies in the specified parent project.
   * (dataPolicies.listProjectsLocationsDataPolicies)
   *
   * @param string $parent Required. Resource name of the project for which to
   * list data policies. Format is
   * `projects/{project_number}/locations/{location_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filters the data policies by policy tags
   * that they are associated with. Currently filter only supports "policy_tag"
   * based filtering and OR based predicates. Sample filter can be "policy_tag:
   * projects/1/locations/us/taxonomies/2/policyTags/3". You may also use wildcard
   * such as "policy_tag: projects/1/locations/us/taxonomies/2*". Please note that
   * OR predicates cannot be used with wildcard filters.
   * @opt_param int pageSize Optional. The maximum number of data policies to
   * return. Must be a value between 1 and 1000. If not set, defaults to 50.
   * @opt_param string pageToken Optional. The `nextPageToken` value returned from
   * a previous list request, if any. If not set, defaults to an empty string.
   * @return ListDataPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDataPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDataPoliciesResponse::class);
  }
  /**
   * Updates the metadata for an existing data policy. The target data policy can
   * be specified by the resource name. (dataPolicies.patch)
   *
   * @param string $name Identifier. Resource name of this data policy, in the
   * format of `projects/{project_number}/locations/{location_id}/dataPolicies/{da
   * ta_policy_id}`.
   * @param DataPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the data policy is
   * not found, a new data policy will be created. In this situation, update_mask
   * is ignored.
   * @opt_param string updateMask Optional. The update mask applies to the
   * resource. For the `FieldMask` definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask If not set, defaults to all
   * of the fields that are allowed to update. Updates to the `name` and
   * `dataPolicyId` fields are not allowed.
   * @return DataPolicy
   * @throws \Google\Service\Exception
   */
  public function patch($name, DataPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], DataPolicy::class);
  }
  /**
   * Removes grantees from a data policy. The grantees will be removed from the
   * existing grantees. If the request contains a grantee that does not exist, the
   * grantee will be ignored. (dataPolicies.removeGrantees)
   *
   * @param string $dataPolicy Required. Resource name of this data policy, in the
   * format of `projects/{project_number}/locations/{location_id}/dataPolicies/{da
   * ta_policy_id}`.
   * @param RemoveGranteesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DataPolicy
   * @throws \Google\Service\Exception
   */
  public function removeGrantees($dataPolicy, RemoveGranteesRequest $postBody, $optParams = [])
  {
    $params = ['dataPolicy' => $dataPolicy, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('removeGrantees', [$params], DataPolicy::class);
  }
  /**
   * Sets the IAM policy for the specified data policy.
   * (dataPolicies.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns the caller's permission on the specified data policy resource.
   * (dataPolicies.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDataPolicies::class, 'Google_Service_BigQueryDataPolicyService_Resource_ProjectsLocationsDataPolicies');
