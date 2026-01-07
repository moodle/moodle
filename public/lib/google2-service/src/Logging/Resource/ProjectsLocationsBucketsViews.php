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

namespace Google\Service\Logging\Resource;

use Google\Service\Logging\GetIamPolicyRequest;
use Google\Service\Logging\ListViewsResponse;
use Google\Service\Logging\LogView;
use Google\Service\Logging\LoggingEmpty;
use Google\Service\Logging\Policy;
use Google\Service\Logging\SetIamPolicyRequest;
use Google\Service\Logging\TestIamPermissionsRequest;
use Google\Service\Logging\TestIamPermissionsResponse;

/**
 * The "views" collection of methods.
 * Typical usage is:
 *  <code>
 *   $loggingService = new Google\Service\Logging(...);
 *   $views = $loggingService->projects_locations_buckets_views;
 *  </code>
 */
class ProjectsLocationsBucketsViews extends \Google\Service\Resource
{
  /**
   * Creates a view over log entries in a log bucket. A bucket may contain a
   * maximum of 30 views. (views.create)
   *
   * @param string $parent Required. The bucket in which to create the view
   * `"projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"` For
   * example:"projects/my-project/locations/global/buckets/my-bucket"
   * @param LogView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string viewId Required. A client-assigned identifier such as "my-
   * view". Identifiers are limited to 100 characters and can include only
   * letters, digits, underscores, and hyphens.
   * @return LogView
   * @throws \Google\Service\Exception
   */
  public function create($parent, LogView $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], LogView::class);
  }
  /**
   * Deletes a view on a log bucket. If an UNAVAILABLE error is returned, this
   * indicates that system is not in a state where it can delete the view. If this
   * occurs, please try again in a few minutes. (views.delete)
   *
   * @param string $name Required. The full resource name of the view to delete: "
   * projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW
   * _ID]" For example:"projects/my-project/locations/global/buckets/my-
   * bucket/views/my-view"
   * @param array $optParams Optional parameters.
   * @return LoggingEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], LoggingEmpty::class);
  }
  /**
   * Gets a view on a log bucket. (views.get)
   *
   * @param string $name Required. The resource name of the policy: "projects/[PRO
   * JECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID]" For
   * example:"projects/my-project/locations/global/buckets/my-bucket/views/my-
   * view"
   * @param array $optParams Optional parameters.
   * @return LogView
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], LogView::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (views.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See Resource names
   * (https://cloud.google.com/apis/design/resource_names) for the appropriate
   * value for this field.
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
   * Lists views on a log bucket. (views.listProjectsLocationsBucketsViews)
   *
   * @param string $parent Required. The bucket whose views are to be listed:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of results to return
   * from this request.Non-positive values are ignored. The presence of
   * nextPageToken in the response indicates that more results might be available.
   * @opt_param string pageToken Optional. If present, then retrieve the next
   * batch of results from the preceding call to this method. pageToken must be
   * the value of nextPageToken from the previous response. The values of other
   * method parameters should be identical to those in the previous call.
   * @return ListViewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBucketsViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListViewsResponse::class);
  }
  /**
   * Updates a view on a log bucket. This method replaces the value of the filter
   * field from the existing view with the corresponding value from the new view.
   * If an UNAVAILABLE error is returned, this indicates that system is not in a
   * state where it can update the view. If this occurs, please try again in a few
   * minutes. (views.patch)
   *
   * @param string $name Required. The full resource name of the view to update "p
   * rojects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_
   * ID]" For example:"projects/my-project/locations/global/buckets/my-
   * bucket/views/my-view"
   * @param LogView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask that specifies the fields
   * in view that need an update. A field will be overwritten if, and only if, it
   * is in the update mask. name and output only fields cannot be updated.For a
   * detailed FieldMask definition, see https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.FieldMaskFor example:
   * updateMask=filter
   * @return LogView
   * @throws \Google\Service\Exception
   */
  public function patch($name, LogView $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], LogView::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy.Can return NOT_FOUND, INVALID_ARGUMENT, and PERMISSION_DENIED
   * errors. (views.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See Resource names
   * (https://cloud.google.com/apis/design/resource_names) for the appropriate
   * value for this field.
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
   * NOT_FOUND error.Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning. (views.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See Resource names
   * (https://cloud.google.com/apis/design/resource_names) for the appropriate
   * value for this field.
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
class_alias(ProjectsLocationsBucketsViews::class, 'Google_Service_Logging_Resource_ProjectsLocationsBucketsViews');
