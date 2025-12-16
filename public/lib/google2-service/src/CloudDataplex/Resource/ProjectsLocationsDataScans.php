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

namespace Google\Service\CloudDataplex\Resource;

use Google\Service\CloudDataplex\GoogleCloudDataplexV1DataScan;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1GenerateDataQualityRulesRequest;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1GenerateDataQualityRulesResponse;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListDataScansResponse;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1RunDataScanRequest;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1RunDataScanResponse;
use Google\Service\CloudDataplex\GoogleIamV1Policy;
use Google\Service\CloudDataplex\GoogleIamV1SetIamPolicyRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\CloudDataplex\GoogleLongrunningOperation;

/**
 * The "dataScans" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $dataScans = $dataplexService->projects_locations_dataScans;
 *  </code>
 */
class ProjectsLocationsDataScans extends \Google\Service\Resource
{
  /**
   * Creates a DataScan resource. (dataScans.create)
   *
   * @param string $parent Required. The resource name of the parent location:
   * projects/{project}/locations/{location_id} where project refers to a
   * project_id or project_number and location_id refers to a Google Cloud region.
   * @param GoogleCloudDataplexV1DataScan $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dataScanId Required. DataScan identifier. Must contain only
   * lowercase letters, numbers and hyphens. Must start with a letter. Must end
   * with a number or a letter. Must be between 1-63 characters. Must be unique
   * within the customer project / location.
   * @opt_param bool validateOnly Optional. Only validate the request, but do not
   * perform mutations. The default is false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1DataScan $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a DataScan resource. (dataScans.delete)
   *
   * @param string $name Required. The resource name of the dataScan:
   * projects/{project}/locations/{location_id}/dataScans/{data_scan_id} where
   * project refers to a project_id or project_number and location_id refers to a
   * Google Cloud region.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, any child resources of this
   * data scan will also be deleted. (Otherwise, the request will only work if the
   * data scan has no child resources.)
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Generates recommended data quality rules based on the results of a data
   * profiling scan.Use the recommendations to build rules for a data quality
   * scan. (dataScans.generateDataQualityRules)
   *
   * @param string $name Required. The name must be one of the following: The name
   * of a data scan with at least one successful, completed data profiling job The
   * name of a successful, completed data profiling job (a data scan job where the
   * job type is data profiling)
   * @param GoogleCloudDataplexV1GenerateDataQualityRulesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1GenerateDataQualityRulesResponse
   * @throws \Google\Service\Exception
   */
  public function generateDataQualityRules($name, GoogleCloudDataplexV1GenerateDataQualityRulesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateDataQualityRules', [$params], GoogleCloudDataplexV1GenerateDataQualityRulesResponse::class);
  }
  /**
   * Gets a DataScan resource. (dataScans.get)
   *
   * @param string $name Required. The resource name of the dataScan:
   * projects/{project}/locations/{location_id}/dataScans/{data_scan_id} where
   * project refers to a project_id or project_number and location_id refers to a
   * Google Cloud region.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. Select the DataScan view to return. Defaults
   * to BASIC.
   * @return GoogleCloudDataplexV1DataScan
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1DataScan::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (dataScans.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See Resource names
   * (https://cloud.google.com/apis/design/resource_names) for the appropriate
   * value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy.Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected.Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset.The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1.To learn which resources support conditions in their
   * IAM policies, see the IAM documentation
   * (https://cloud.google.com/iam/help/conditions/resource-policies).
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Lists DataScans. (dataScans.listProjectsLocationsDataScans)
   *
   * @param string $parent Required. The resource name of the parent location:
   * projects/{project}/locations/{location_id} where project refers to a
   * project_id or project_number and location_id refers to a Google Cloud region.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter request.
   * @opt_param string orderBy Optional. Order by fields (name or create_time) for
   * the result. If not specified, the ordering is undefined.
   * @opt_param int pageSize Optional. Maximum number of dataScans to return. The
   * service may return fewer than this value. If unspecified, at most 500 scans
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. Page token received from a previous
   * ListDataScans call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to ListDataScans must match the
   * call that provided the page token.
   * @return GoogleCloudDataplexV1ListDataScansResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDataScans($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListDataScansResponse::class);
  }
  /**
   * Updates a DataScan resource. (dataScans.patch)
   *
   * @param string $name Output only. Identifier. The relative resource name of
   * the scan, of the form:
   * projects/{project}/locations/{location_id}/dataScans/{datascan_id}, where
   * project refers to a project_id or project_number and location_id refers to a
   * Google Cloud region.
   * @param GoogleCloudDataplexV1DataScan $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Mask of fields to update.
   * @opt_param bool validateOnly Optional. Only validate the request, but do not
   * perform mutations. The default is false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDataplexV1DataScan $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Runs an on-demand execution of a DataScan (dataScans.run)
   *
   * @param string $name Required. The resource name of the DataScan:
   * projects/{project}/locations/{location_id}/dataScans/{data_scan_id}. where
   * project refers to a project_id or project_number and location_id refers to a
   * Google Cloud region.Only OnDemand data scans are allowed.
   * @param GoogleCloudDataplexV1RunDataScanRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1RunDataScanResponse
   * @throws \Google\Service\Exception
   */
  public function run($name, GoogleCloudDataplexV1RunDataScanRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('run', [$params], GoogleCloudDataplexV1RunDataScanResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy.Can return NOT_FOUND, INVALID_ARGUMENT, and PERMISSION_DENIED
   * errors. (dataScans.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See Resource names
   * (https://cloud.google.com/apis/design/resource_names) for the appropriate
   * value for this field.
   * @param GoogleIamV1SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, GoogleIamV1SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * NOT_FOUND error.Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (dataScans.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See Resource names
   * (https://cloud.google.com/apis/design/resource_names) for the appropriate
   * value for this field.
   * @param GoogleIamV1TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleIamV1TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, GoogleIamV1TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], GoogleIamV1TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDataScans::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsDataScans');
