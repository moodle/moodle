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

namespace Google\Service\Assuredworkloads\Resource;

use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1AnalyzeWorkloadMoveResponse;
use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1EnableComplianceUpdatesResponse;
use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1EnableResourceMonitoringResponse;
use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1ListWorkloadsResponse;
use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1MutatePartnerPermissionsRequest;
use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesRequest;
use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesResponse;
use Google\Service\Assuredworkloads\GoogleCloudAssuredworkloadsV1Workload;
use Google\Service\Assuredworkloads\GoogleLongrunningOperation;
use Google\Service\Assuredworkloads\GoogleProtobufEmpty;

/**
 * The "workloads" collection of methods.
 * Typical usage is:
 *  <code>
 *   $assuredworkloadsService = new Google\Service\Assuredworkloads(...);
 *   $workloads = $assuredworkloadsService->organizations_locations_workloads;
 *  </code>
 */
class OrganizationsLocationsWorkloads extends \Google\Service\Resource
{
  /**
   * Analyzes a hypothetical move of a source resource to a target workload to
   * surface compliance risks. The analysis is best effort and is not guaranteed
   * to be exhaustive. (workloads.analyzeWorkloadMove)
   *
   * @param string $target Required. The resource ID of the folder-based
   * destination workload. This workload is where the source resource will
   * hypothetically be moved to. Specify the workload's relative resource name,
   * formatted as: "organizations/{ORGANIZATION_ID}/locations/{LOCATION_ID}/worklo
   * ads/{WORKLOAD_ID}" For example: "organizations/123/locations/us-
   * east1/workloads/assured-workload-2"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string assetTypes Optional. List of asset types to be analyzed,
   * including and under the source resource. If empty, all assets are analyzed.
   * The complete list of asset types is available
   * [here](https://cloud.google.com/asset-inventory/docs/supported-asset-types).
   * @opt_param int pageSize Optional. Page size. If a value is not specified, the
   * default value of 10 is used. The maximum value is 50.
   * @opt_param string pageToken Optional. The page token from the previous
   * response. It needs to be passed in the second and following requests.
   * @opt_param string project The source type is a project. Specify the project's
   * relative resource name, formatted as either a project number or a project ID:
   * "projects/{PROJECT_NUMBER}" or "projects/{PROJECT_ID}" For example:
   * "projects/951040570662" when specifying a project number, or "projects/my-
   * project-123" when specifying a project ID.
   * @return GoogleCloudAssuredworkloadsV1AnalyzeWorkloadMoveResponse
   * @throws \Google\Service\Exception
   */
  public function analyzeWorkloadMove($target, $optParams = [])
  {
    $params = ['target' => $target];
    $params = array_merge($params, $optParams);
    return $this->call('analyzeWorkloadMove', [$params], GoogleCloudAssuredworkloadsV1AnalyzeWorkloadMoveResponse::class);
  }
  /**
   * Creates Assured Workload. (workloads.create)
   *
   * @param string $parent Required. The resource name of the new Workload's
   * parent. Must be of the form `organizations/{org_id}/locations/{location_id}`.
   * @param GoogleCloudAssuredworkloadsV1Workload $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string externalId Optional. A identifier associated with the
   * workload and underlying projects which allows for the break down of billing
   * costs for a workload. The value provided for the identifier will add a label
   * to the workload and contained projects with the identifier as the value.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAssuredworkloadsV1Workload $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes the workload. Make sure that workload's direct children are already
   * in a deleted state, otherwise the request will fail with a
   * FAILED_PRECONDITION error. In addition to assuredworkloads.workload.delete
   * permission, the user should also have orgpolicy.policy.set permission on the
   * deleted folder to remove Assured Workloads OrgPolicies. (workloads.delete)
   *
   * @param string $name Required. The `name` field is used to identify the
   * workload. Format:
   * organizations/{org_id}/locations/{location_id}/workloads/{workload_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The etag of the workload. If this is
   * provided, it must match the server's etag.
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
   * This endpoint enables Assured Workloads service to offer compliance updates
   * for the folder based assured workload. It sets up an Assured Workloads
   * Service Agent, having permissions to read compliance controls (for example:
   * Org Policies) applied on the workload. The caller must have
   * `resourcemanager.folders.getIamPolicy` and
   * `resourcemanager.folders.setIamPolicy` permissions on the assured workload
   * folder. (workloads.enableComplianceUpdates)
   *
   * @param string $name Required. The `name` field is used to identify the
   * workload. Format:
   * organizations/{org_id}/locations/{location_id}/workloads/{workload_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAssuredworkloadsV1EnableComplianceUpdatesResponse
   * @throws \Google\Service\Exception
   */
  public function enableComplianceUpdates($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('enableComplianceUpdates', [$params], GoogleCloudAssuredworkloadsV1EnableComplianceUpdatesResponse::class);
  }
  /**
   * Enable resource violation monitoring for a workload.
   * (workloads.enableResourceMonitoring)
   *
   * @param string $name Required. The `name` field is used to identify the
   * workload. Format:
   * organizations/{org_id}/locations/{location_id}/workloads/{workload_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAssuredworkloadsV1EnableResourceMonitoringResponse
   * @throws \Google\Service\Exception
   */
  public function enableResourceMonitoring($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('enableResourceMonitoring', [$params], GoogleCloudAssuredworkloadsV1EnableResourceMonitoringResponse::class);
  }
  /**
   * Gets Assured Workload associated with a CRM Node (workloads.get)
   *
   * @param string $name Required. The resource name of the Workload to fetch.
   * This is the workloads's relative path in the API, formatted as "organizations
   * /{organization_id}/locations/{location_id}/workloads/{workload_id}". For
   * example, "organizations/123/locations/us-east1/workloads/assured-workload-1".
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAssuredworkloadsV1Workload
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAssuredworkloadsV1Workload::class);
  }
  /**
   * Lists Assured Workloads under a CRM Node.
   * (workloads.listOrganizationsLocationsWorkloads)
   *
   * @param string $parent Required. Parent Resource to list workloads from. Must
   * be of the form `organizations/{org_id}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A custom filter for filtering by properties of a
   * workload. At this time, only filtering by labels is supported.
   * @opt_param int pageSize Page size.
   * @opt_param string pageToken Page token returned from previous request. Page
   * token contains context from previous request. Page token needs to be passed
   * in the second and following requests.
   * @return GoogleCloudAssuredworkloadsV1ListWorkloadsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsWorkloads($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAssuredworkloadsV1ListWorkloadsResponse::class);
  }
  /**
   * Update the permissions settings for an existing partner workload. For force
   * updates don't set etag field in the Workload. Only one update operation per
   * workload can be in progress. (workloads.mutatePartnerPermissions)
   *
   * @param string $name Required. The `name` field is used to identify the
   * workload. Format:
   * organizations/{org_id}/locations/{location_id}/workloads/{workload_id}
   * @param GoogleCloudAssuredworkloadsV1MutatePartnerPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAssuredworkloadsV1Workload
   * @throws \Google\Service\Exception
   */
  public function mutatePartnerPermissions($name, GoogleCloudAssuredworkloadsV1MutatePartnerPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('mutatePartnerPermissions', [$params], GoogleCloudAssuredworkloadsV1Workload::class);
  }
  /**
   * Updates an existing workload. Currently allows updating of workload
   * display_name and labels. For force updates don't set etag field in the
   * Workload. Only one update operation per workload can be in progress.
   * (workloads.patch)
   *
   * @param string $name Optional. The resource name of the workload. Format:
   * organizations/{organization}/locations/{location}/workloads/{workload} Read-
   * only.
   * @param GoogleCloudAssuredworkloadsV1Workload $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * @return GoogleCloudAssuredworkloadsV1Workload
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAssuredworkloadsV1Workload $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAssuredworkloadsV1Workload::class);
  }
  /**
   * Restrict the list of resources allowed in the Workload environment. The
   * current list of allowed products can be found at
   * https://cloud.google.com/assured-workloads/docs/supported-products In
   * addition to assuredworkloads.workload.update permission, the user should also
   * have orgpolicy.policy.set permission on the folder resource to use this
   * functionality. (workloads.restrictAllowedResources)
   *
   * @param string $name Required. The resource name of the Workload. This is the
   * workloads's relative path in the API, formatted as "organizations/{organizati
   * on_id}/locations/{location_id}/workloads/{workload_id}". For example,
   * "organizations/123/locations/us-east1/workloads/assured-workload-1".
   * @param GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function restrictAllowedResources($name, GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restrictAllowedResources', [$params], GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsWorkloads::class, 'Google_Service_Assuredworkloads_Resource_OrganizationsLocationsWorkloads');
