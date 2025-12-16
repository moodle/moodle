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

namespace Google\Service\Contactcenterinsights\Resource;

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1AuthorizedView;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListAuthorizedViewsResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1QueryMetricsRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1QueryPerformanceOverviewRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1SearchAuthorizedViewsResponse;
use Google\Service\Contactcenterinsights\GoogleIamV1Policy;
use Google\Service\Contactcenterinsights\GoogleIamV1SetIamPolicyRequest;
use Google\Service\Contactcenterinsights\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\Contactcenterinsights\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\Contactcenterinsights\GoogleLongrunningOperation;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "authorizedViews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $authorizedViews = $contactcenterinsightsService->projects_locations_authorizedViewSets_authorizedViews;
 *  </code>
 */
class ProjectsLocationsAuthorizedViewSetsAuthorizedViews extends \Google\Service\Resource
{
  /**
   * Create AuthorizedView (authorizedViews.create)
   *
   * @param string $parent Required. The parent resource of the AuthorizedView.
   * @param GoogleCloudContactcenterinsightsV1AuthorizedView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string authorizedViewId Optional. A unique ID for the new
   * AuthorizedView. This ID will become the final component of the
   * AuthorizedView's resource name. If no ID is specified, a server-generated ID
   * will be used. This value should be 4-64 characters and must match the regular
   * expression `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`. See
   * https://google.aip.dev/122#resource-id-segments
   * @return GoogleCloudContactcenterinsightsV1AuthorizedView
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1AuthorizedView $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1AuthorizedView::class);
  }
  /**
   * Deletes an AuthorizedView. (authorizedViews.delete)
   *
   * @param string $name Required. The name of the AuthorizedView to delete.
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
   * Get AuthorizedView (authorizedViews.get)
   *
   * @param string $name Required. The name of the AuthorizedView to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1AuthorizedView
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1AuthorizedView::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (authorizedViews.getIamPolicy)
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
   * List AuthorizedViewSets
   * (authorizedViews.listProjectsLocationsAuthorizedViewSetsAuthorizedViews)
   *
   * @param string $parent Required. The parent resource of the AuthorizedViews.
   * If the parent is set to `-`, all AuthorizedViews under the location will be
   * returned.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter expression to filter authorized
   * views listed in the response.
   * @opt_param string orderBy Optional. The order by expression to order
   * authorized views listed in the response.
   * @opt_param int pageSize Optional. The maximum number of view to return in the
   * response. If the value is zero, the service will select a default size. A
   * call might return fewer objects than requested. A non-empty `next_page_token`
   * in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListAuthorizedViewsResponse`. This value indicates that this is a
   * continuation of a prior `ListAuthorizedViews` call and that the system should
   * return the next page of data.
   * @return GoogleCloudContactcenterinsightsV1ListAuthorizedViewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAuthorizedViewSetsAuthorizedViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListAuthorizedViewsResponse::class);
  }
  /**
   * Updates an AuthorizedView. (authorizedViews.patch)
   *
   * @param string $name Identifier. The resource name of the AuthorizedView.
   * Format: projects/{project}/locations/{location}/authorizedViewSets/{authorize
   * d_view_set}/authorizedViews/{authorized_view}
   * @param GoogleCloudContactcenterinsightsV1AuthorizedView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to be updated. All
   * possible fields can be updated by passing `*`, or a subset of the following
   * updateable fields can be provided: * `conversation_filter` * `display_name`
   * @return GoogleCloudContactcenterinsightsV1AuthorizedView
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1AuthorizedView $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1AuthorizedView::class);
  }
  /**
   * Query metrics. (authorizedViews.queryMetrics)
   *
   * @param string $location Required. The location of the data.
   * "projects/{project}/locations/{location}"
   * @param GoogleCloudContactcenterinsightsV1QueryMetricsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function queryMetrics($location, GoogleCloudContactcenterinsightsV1QueryMetricsRequest $postBody, $optParams = [])
  {
    $params = ['location' => $location, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('queryMetrics', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Generates a summary of predefined performance metrics for a set of
   * conversations. Conversations can be specified by specifying a time window and
   * an agent id, for now. The summary includes a comparison of metrics computed
   * for conversations in the previous time period, and also a comparison with
   * peers in the same time period. (authorizedViews.queryPerformanceOverview)
   *
   * @param string $parent Required. The parent resource of the conversations to
   * derive performance stats from. "projects/{project}/locations/{location}"
   * @param GoogleCloudContactcenterinsightsV1QueryPerformanceOverviewRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function queryPerformanceOverview($parent, GoogleCloudContactcenterinsightsV1QueryPerformanceOverviewRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('queryPerformanceOverview', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * SearchAuthorizedViewSets (authorizedViews.search)
   *
   * @param string $parent Required. The parent resource of the AuthorizedViews.
   * If the parent is set to `-`, all AuthorizedViews under the location will be
   * returned.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Optional. The order by expression to order
   * authorized views listed in the response.
   * @opt_param int pageSize Optional. The maximum number of view to return in the
   * response. If the value is zero, the service will select a default size. A
   * call might return fewer objects than requested. A non-empty `next_page_token`
   * in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListAuthorizedViewsResponse`. This value indicates that this is a
   * continuation of a prior `ListAuthorizedViews` call and that the system should
   * return the next page of data.
   * @opt_param string query Optional. The query expression to search authorized
   * views.
   * @return GoogleCloudContactcenterinsightsV1SearchAuthorizedViewsResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GoogleCloudContactcenterinsightsV1SearchAuthorizedViewsResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (authorizedViews.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
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
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (authorizedViews.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
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
class_alias(ProjectsLocationsAuthorizedViewSetsAuthorizedViews::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsAuthorizedViewSetsAuthorizedViews');
