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

namespace Google\Service\Eventarc\Resource;

use Google\Service\Eventarc\GoogleApiSource;
use Google\Service\Eventarc\GoogleLongrunningOperation;
use Google\Service\Eventarc\ListGoogleApiSourcesResponse;
use Google\Service\Eventarc\Policy;
use Google\Service\Eventarc\SetIamPolicyRequest;
use Google\Service\Eventarc\TestIamPermissionsRequest;
use Google\Service\Eventarc\TestIamPermissionsResponse;

/**
 * The "googleApiSources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $eventarcService = new Google\Service\Eventarc(...);
 *   $googleApiSources = $eventarcService->projects_locations_googleApiSources;
 *  </code>
 */
class ProjectsLocationsGoogleApiSources extends \Google\Service\Resource
{
  /**
   * Create a new GoogleApiSource in a particular project and location.
   * (googleApiSources.create)
   *
   * @param string $parent Required. The parent collection in which to add this
   * google api source.
   * @param GoogleApiSource $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string googleApiSourceId Required. The user-provided ID to be
   * assigned to the GoogleApiSource. It should match the format
   * `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the review, but do not post it.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleApiSource $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Delete a single GoogleApiSource. (googleApiSources.delete)
   *
   * @param string $name Required. The name of the GoogleApiSource to be deleted.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the MessageBus is
   * not found, the request will succeed but no action will be taken on the
   * server.
   * @opt_param string etag Optional. If provided, the MessageBus will only be
   * deleted if the etag matches the current etag on the resource.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the review, but do not post it.
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
   * Get a single GoogleApiSource. (googleApiSources.get)
   *
   * @param string $name Required. The name of the google api source to get.
   * @param array $optParams Optional parameters.
   * @return GoogleApiSource
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleApiSource::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (googleApiSources.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * List GoogleApiSources.
   * (googleApiSources.listProjectsLocationsGoogleApiSources)
   *
   * @param string $parent Required. The parent collection to list
   * GoogleApiSources on.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter field that the list request
   * will filter on. Possible filtersare described in https://google.aip.dev/160.
   * @opt_param string orderBy Optional. The sorting order of the resources
   * returned. Value should be a comma-separated list of fields. The default
   * sorting order is ascending. To specify descending order for a field, append a
   * `desc` suffix; for example: `name desc, update_time`.
   * @opt_param int pageSize Optional. The maximum number of results to return on
   * each page. Note: The service may send fewer.
   * @opt_param string pageToken Optional. The page token; provide the value from
   * the `next_page_token` field in a previous call to retrieve the subsequent
   * page. When paginating, all other parameters provided must match the previous
   * call that provided the page token.
   * @return ListGoogleApiSourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGoogleApiSources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListGoogleApiSourcesResponse::class);
  }
  /**
   * Update a single GoogleApiSource. (googleApiSources.patch)
   *
   * @param string $name Identifier. Resource name of the form
   * projects/{project}/locations/{location}/googleApiSources/{google_api_source}
   * @param GoogleApiSource $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the
   * GoogleApiSource is not found, a new GoogleApiSource will be created. In this
   * situation, `update_mask` is ignored.
   * @opt_param string updateMask Optional. The fields to be updated; only fields
   * explicitly provided are updated. If no field mask is provided, all provided
   * fields in the request are updated. To update all fields, provide a field mask
   * of "*".
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the review, but do not post it.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleApiSource $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (googleApiSources.setIamPolicy)
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
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (googleApiSources.testIamPermissions)
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
class_alias(ProjectsLocationsGoogleApiSources::class, 'Google_Service_Eventarc_Resource_ProjectsLocationsGoogleApiSources');
