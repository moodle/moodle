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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchReadFeatureValuesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1Featurestore;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListFeaturestoresResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SearchFeaturesResponse;
use Google\Service\Aiplatform\GoogleIamV1Policy;
use Google\Service\Aiplatform\GoogleIamV1SetIamPolicyRequest;
use Google\Service\Aiplatform\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "featurestores" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $featurestores = $aiplatformService->projects_locations_featurestores;
 *  </code>
 */
class ProjectsLocationsFeaturestores extends \Google\Service\Resource
{
  /**
   * Batch reads Feature values from a Featurestore. This API enables batch
   * reading Feature values, where each read instance in the batch may read
   * Feature values of entities from one or more EntityTypes. Point-in-time
   * correctness is guaranteed for Feature values of each read instance as of each
   * instance's read timestamp. (featurestores.batchReadFeatureValues)
   *
   * @param string $featurestore Required. The resource name of the Featurestore
   * from which to query Feature values. Format:
   * `projects/{project}/locations/{location}/featurestores/{featurestore}`
   * @param GoogleCloudAiplatformV1BatchReadFeatureValuesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function batchReadFeatureValues($featurestore, GoogleCloudAiplatformV1BatchReadFeatureValuesRequest $postBody, $optParams = [])
  {
    $params = ['featurestore' => $featurestore, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchReadFeatureValues', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a new Featurestore in a given project and location.
   * (featurestores.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * Featurestores. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1Featurestore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string featurestoreId Required. The ID to use for this
   * Featurestore, which will become the final component of the Featurestore's
   * resource name. This value may be up to 60 characters, and valid characters
   * are `[a-z0-9_]`. The first character cannot be a number. The value must be
   * unique within the project and location.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Featurestore $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a single Featurestore. The Featurestore must not contain any
   * EntityTypes or `force` must be set to true for the request to succeed.
   * (featurestores.delete)
   *
   * @param string $name Required. The name of the Featurestore to be deleted.
   * Format:
   * `projects/{project}/locations/{location}/featurestores/{featurestore}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force If set to true, any EntityTypes and Features for this
   * Featurestore will also be deleted. (Otherwise, the request will only work if
   * the Featurestore has no EntityTypes.)
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
   * Gets details of a single Featurestore. (featurestores.get)
   *
   * @param string $name Required. The name of the Featurestore resource.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Featurestore
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Featurestore::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (featurestores.getIamPolicy)
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
   * Lists Featurestores in a given project and location.
   * (featurestores.listProjectsLocationsFeaturestores)
   *
   * @param string $parent Required. The resource name of the Location to list
   * Featurestores. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the featurestores that match the filter
   * expression. The following fields are supported: * `create_time`: Supports
   * `=`, `!=`, `<`, `>`, `<=`, and `>=` comparisons. Values must be in RFC 3339
   * format. * `update_time`: Supports `=`, `!=`, `<`, `>`, `<=`, and `>=`
   * comparisons. Values must be in RFC 3339 format. *
   * `online_serving_config.fixed_node_count`: Supports `=`, `!=`, `<`, `>`, `<=`,
   * and `>=` comparisons. * `labels`: Supports key-value equality and key
   * presence. Examples: * `create_time > "2020-01-01" OR update_time >
   * "2020-01-01"` Featurestores created or updated after 2020-01-01. *
   * `labels.env = "prod"` Featurestores with label "env" set to "prod".
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order. Use "desc" after a field name for descending.
   * Supported Fields: * `create_time` * `update_time` *
   * `online_serving_config.fixed_node_count`
   * @opt_param int pageSize The maximum number of Featurestores to return. The
   * service may return fewer than this value. If unspecified, at most 100
   * Featurestores will be returned. The maximum value is 100; any value greater
   * than 100 will be coerced to 100.
   * @opt_param string pageToken A page token, received from a previous
   * FeaturestoreService.ListFeaturestores call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * FeaturestoreService.ListFeaturestores must match the call that provided the
   * page token.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListFeaturestoresResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsFeaturestores($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListFeaturestoresResponse::class);
  }
  /**
   * Updates the parameters of a single Featurestore. (featurestores.patch)
   *
   * @param string $name Output only. Name of the Featurestore. Format:
   * `projects/{project}/locations/{location}/featurestores/{featurestore}`
   * @param GoogleCloudAiplatformV1Featurestore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Field mask is used to specify the fields to be
   * overwritten in the Featurestore resource by the update. The fields specified
   * in the update_mask are relative to the resource, not the full request. A
   * field will be overwritten if it is in the mask. If the user does not provide
   * a mask then only the non-empty fields present in the request will be
   * overwritten. Set the update_mask to `*` to override all fields. Updatable
   * fields: * `labels` * `online_serving_config.fixed_node_count` *
   * `online_serving_config.scaling` * `online_storage_ttl_days`
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1Featurestore $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Searches Features matching a query in a given project.
   * (featurestores.searchFeatures)
   *
   * @param string $location Required. The resource name of the Location to search
   * Features. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of Features to return. The service
   * may return fewer than this value. If unspecified, at most 100 Features will
   * be returned. The maximum value is 100; any value greater than 100 will be
   * coerced to 100.
   * @opt_param string pageToken A page token, received from a previous
   * FeaturestoreService.SearchFeatures call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * FeaturestoreService.SearchFeatures, except `page_size`, must match the call
   * that provided the page token.
   * @opt_param string query Query string that is a conjunction of field-
   * restricted queries and/or field-restricted filters. Field-restricted queries
   * and filters can be combined using `AND` to form a conjunction. A field query
   * is in the form FIELD:QUERY. This implicitly checks if QUERY exists as a
   * substring within Feature's FIELD. The QUERY and the FIELD are converted to a
   * sequence of words (i.e. tokens) for comparison. This is done by: * Removing
   * leading/trailing whitespace and tokenizing the search value. Characters that
   * are not one of alphanumeric `[a-zA-Z0-9]`, underscore `_`, or asterisk `*`
   * are treated as delimiters for tokens. `*` is treated as a wildcard that
   * matches characters within a token. * Ignoring case. * Prepending an asterisk
   * to the first and appending an asterisk to the last token in QUERY. A QUERY
   * must be either a singular token or a phrase. A phrase is one or multiple
   * words enclosed in double quotation marks ("). With phrases, the order of the
   * words is important. Words in the phrase must be matching in order and
   * consecutively. Supported FIELDs for field-restricted queries: * `feature_id`
   * * `description` * `entity_type_id` Examples: * `feature_id: foo` --> Matches
   * a Feature with ID containing the substring `foo` (eg. `foo`, `foofeature`,
   * `barfoo`). * `feature_id: foo*feature` --> Matches a Feature with ID
   * containing the substring `foo*feature` (eg. `foobarfeature`). * `feature_id:
   * foo AND description: bar` --> Matches a Feature with ID containing the
   * substring `foo` and description containing the substring `bar`. Besides field
   * queries, the following exact-match filters are supported. The exact-match
   * filters do not support wildcards. Unlike field-restricted queries, exact-
   * match filters are case-sensitive. * `feature_id`: Supports = comparisons. *
   * `description`: Supports = comparisons. Multi-token filters should be enclosed
   * in quotes. * `entity_type_id`: Supports = comparisons. * `value_type`:
   * Supports = and != comparisons. * `labels`: Supports key-value equality as
   * well as key presence. * `featurestore_id`: Supports = comparisons. Examples:
   * * `description = "foo bar"` --> Any Feature with description exactly equal to
   * `foo bar` * `value_type = DOUBLE` --> Features whose type is DOUBLE. *
   * `labels.active = yes AND labels.env = prod` --> Features having both (active:
   * yes) and (env: prod) labels. * `labels.env: *` --> Any Feature which has a
   * label with `env` as the key.
   * @return GoogleCloudAiplatformV1SearchFeaturesResponse
   * @throws \Google\Service\Exception
   */
  public function searchFeatures($location, $optParams = [])
  {
    $params = ['location' => $location];
    $params = array_merge($params, $optParams);
    return $this->call('searchFeatures', [$params], GoogleCloudAiplatformV1SearchFeaturesResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (featurestores.setIamPolicy)
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
   * (featurestores.testIamPermissions)
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
class_alias(ProjectsLocationsFeaturestores::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsFeaturestores');
