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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1FeatureView;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1FeatureViewDirectWriteRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1FeatureViewDirectWriteResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1FetchFeatureValuesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1FetchFeatureValuesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1GenerateFetchAccessTokenRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1GenerateFetchAccessTokenResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListFeatureViewsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SearchNearestEntitiesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SearchNearestEntitiesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SyncFeatureViewRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SyncFeatureViewResponse;
use Google\Service\Aiplatform\GoogleIamV1Policy;
use Google\Service\Aiplatform\GoogleIamV1SetIamPolicyRequest;
use Google\Service\Aiplatform\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "featureViews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $featureViews = $aiplatformService->projects_locations_featureOnlineStores_featureViews;
 *  </code>
 */
class ProjectsLocationsFeatureOnlineStoresFeatureViews extends \Google\Service\Resource
{
  /**
   * Creates a new FeatureView in a given FeatureOnlineStore.
   * (featureViews.create)
   *
   * @param string $parent Required. The resource name of the FeatureOnlineStore
   * to create FeatureViews. Format: `projects/{project}/locations/{location}/feat
   * ureOnlineStores/{feature_online_store}`
   * @param GoogleCloudAiplatformV1FeatureView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string featureViewId Required. The ID to use for the FeatureView,
   * which will become the final component of the FeatureView's resource name.
   * This value may be up to 60 characters, and valid characters are `[a-z0-9_]`.
   * The first character cannot be a number. The value must be unique within a
   * FeatureOnlineStore.
   * @opt_param bool runSyncImmediately Immutable. If set to true, one on demand
   * sync will be run immediately, regardless whether the FeatureView.sync_config
   * is configured or not.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1FeatureView $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a single FeatureView. (featureViews.delete)
   *
   * @param string $name Required. The name of the FeatureView to be deleted.
   * Format: `projects/{project}/locations/{location}/featureOnlineStores/{feature
   * _online_store}/featureViews/{feature_view}`
   * @param array $optParams Optional parameters.
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
   * Bidirectional streaming RPC to directly write to feature values in a feature
   * view. Requests may not have a one-to-one mapping to responses and responses
   * may be returned out-of-order to reduce latency. (featureViews.directWrite)
   *
   * @param string $featureView FeatureView resource format `projects/{project}/lo
   * cations/{location}/featureOnlineStores/{featureOnlineStore}/featureViews/{fea
   * tureView}`
   * @param GoogleCloudAiplatformV1FeatureViewDirectWriteRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1FeatureViewDirectWriteResponse
   * @throws \Google\Service\Exception
   */
  public function directWrite($featureView, GoogleCloudAiplatformV1FeatureViewDirectWriteRequest $postBody, $optParams = [])
  {
    $params = ['featureView' => $featureView, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('directWrite', [$params], GoogleCloudAiplatformV1FeatureViewDirectWriteResponse::class);
  }
  /**
   * Fetch feature values under a FeatureView. (featureViews.fetchFeatureValues)
   *
   * @param string $featureView Required. FeatureView resource format `projects/{p
   * roject}/locations/{location}/featureOnlineStores/{featureOnlineStore}/feature
   * Views/{featureView}`
   * @param GoogleCloudAiplatformV1FetchFeatureValuesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1FetchFeatureValuesResponse
   * @throws \Google\Service\Exception
   */
  public function fetchFeatureValues($featureView, GoogleCloudAiplatformV1FetchFeatureValuesRequest $postBody, $optParams = [])
  {
    $params = ['featureView' => $featureView, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('fetchFeatureValues', [$params], GoogleCloudAiplatformV1FetchFeatureValuesResponse::class);
  }
  /**
   * RPC to generate an access token for the given feature view. FeatureViews
   * under the same FeatureOnlineStore share the same access token.
   * (featureViews.generateFetchAccessToken)
   *
   * @param string $featureView FeatureView resource format `projects/{project}/lo
   * cations/{location}/featureOnlineStores/{featureOnlineStore}/featureViews/{fea
   * tureView}`
   * @param GoogleCloudAiplatformV1GenerateFetchAccessTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1GenerateFetchAccessTokenResponse
   * @throws \Google\Service\Exception
   */
  public function generateFetchAccessToken($featureView, GoogleCloudAiplatformV1GenerateFetchAccessTokenRequest $postBody, $optParams = [])
  {
    $params = ['featureView' => $featureView, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateFetchAccessToken', [$params], GoogleCloudAiplatformV1GenerateFetchAccessTokenResponse::class);
  }
  /**
   * Gets details of a single FeatureView. (featureViews.get)
   *
   * @param string $name Required. The name of the FeatureView resource. Format: `
   * projects/{project}/locations/{location}/featureOnlineStores/{feature_online_s
   * tore}/featureViews/{feature_view}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1FeatureView
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1FeatureView::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (featureViews.getIamPolicy)
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
   * Lists FeatureViews in a given FeatureOnlineStore.
   * (featureViews.listProjectsLocationsFeatureOnlineStoresFeatureViews)
   *
   * @param string $parent Required. The resource name of the FeatureOnlineStore
   * to list FeatureViews. Format: `projects/{project}/locations/{location}/featur
   * eOnlineStores/{feature_online_store}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the FeatureViews that match the filter
   * expression. The following filters are supported: * `create_time`: Supports
   * `=`, `!=`, `<`, `>`, `>=`, and `<=` comparisons. Values must be in RFC 3339
   * format. * `update_time`: Supports `=`, `!=`, `<`, `>`, `>=`, and `<=`
   * comparisons. Values must be in RFC 3339 format. * `labels`: Supports key-
   * value equality as well as key presence. Examples: * `create_time >
   * \"2020-01-31T15:30:00.000000Z\" OR update_time >
   * \"2020-01-31T15:30:00.000000Z\"` --> FeatureViews created or updated after
   * 2020-01-31T15:30:00.000000Z. * `labels.active = yes AND labels.env = prod`
   * --> FeatureViews having both (active: yes) and (env: prod) labels. *
   * `labels.env: *` --> Any FeatureView which has a label with 'env' as the key.
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `feature_view_id` * `create_time` * `update_time`
   * @opt_param int pageSize The maximum number of FeatureViews to return. The
   * service may return fewer than this value. If unspecified, at most 1000
   * FeatureViews will be returned. The maximum value is 1000; any value greater
   * than 1000 will be coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * FeatureOnlineStoreAdminService.ListFeatureViews call. Provide this to
   * retrieve the subsequent page. When paginating, all other parameters provided
   * to FeatureOnlineStoreAdminService.ListFeatureViews must match the call that
   * provided the page token.
   * @return GoogleCloudAiplatformV1ListFeatureViewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsFeatureOnlineStoresFeatureViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListFeatureViewsResponse::class);
  }
  /**
   * Updates the parameters of a single FeatureView. (featureViews.patch)
   *
   * @param string $name Identifier. Name of the FeatureView. Format: `projects/{p
   * roject}/locations/{location}/featureOnlineStores/{feature_online_store}/featu
   * reViews/{feature_view}`
   * @param GoogleCloudAiplatformV1FeatureView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Field mask is used to specify the fields to be
   * overwritten in the FeatureView resource by the update. The fields specified
   * in the update_mask are relative to the resource, not the full request. A
   * field will be overwritten if it is in the mask. If the user does not provide
   * a mask then only the non-empty fields present in the request will be
   * overwritten. Set the update_mask to `*` to override all fields. Updatable
   * fields: * `labels` * `service_agent_type` * `big_query_source` *
   * `big_query_source.uri` * `big_query_source.entity_id_columns` *
   * `feature_registry_source` * `feature_registry_source.feature_groups` *
   * `sync_config` * `sync_config.cron` * `optimized_config.automatic_resources`
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1FeatureView $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Search the nearest entities under a FeatureView. Search only works for
   * indexable feature view; if a feature view isn't indexable, returns Invalid
   * argument response. (featureViews.searchNearestEntities)
   *
   * @param string $featureView Required. FeatureView resource format `projects/{p
   * roject}/locations/{location}/featureOnlineStores/{featureOnlineStore}/feature
   * Views/{featureView}`
   * @param GoogleCloudAiplatformV1SearchNearestEntitiesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1SearchNearestEntitiesResponse
   * @throws \Google\Service\Exception
   */
  public function searchNearestEntities($featureView, GoogleCloudAiplatformV1SearchNearestEntitiesRequest $postBody, $optParams = [])
  {
    $params = ['featureView' => $featureView, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('searchNearestEntities', [$params], GoogleCloudAiplatformV1SearchNearestEntitiesResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (featureViews.setIamPolicy)
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
   * Triggers on-demand sync for the FeatureView. (featureViews.sync)
   *
   * @param string $featureView Required. Format: `projects/{project}/locations/{l
   * ocation}/featureOnlineStores/{feature_online_store}/featureViews/{feature_vie
   * w}`
   * @param GoogleCloudAiplatformV1SyncFeatureViewRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1SyncFeatureViewResponse
   * @throws \Google\Service\Exception
   */
  public function sync($featureView, GoogleCloudAiplatformV1SyncFeatureViewRequest $postBody, $optParams = [])
  {
    $params = ['featureView' => $featureView, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('sync', [$params], GoogleCloudAiplatformV1SyncFeatureViewResponse::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (featureViews.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string permissions The set of permissions to check for the
   * `resource`. Permissions with wildcards (such as `*` or `storage.*`) are not
   * allowed. For more information see [IAM
   * Overview](https://cloud.google.com/iam/docs/overview#permissions).
   * @return GoogleIamV1TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], GoogleIamV1TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsFeatureOnlineStoresFeatureViews::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsFeatureOnlineStoresFeatureViews');
