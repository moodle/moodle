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

use Google\Service\CloudDataplex\GoogleCloudDataplexV1EncryptionConfig;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListEncryptionConfigsResponse;
use Google\Service\CloudDataplex\GoogleIamV1Policy;
use Google\Service\CloudDataplex\GoogleIamV1SetIamPolicyRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\CloudDataplex\GoogleLongrunningOperation;

/**
 * The "encryptionConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $encryptionConfigs = $dataplexService->organizations_locations_encryptionConfigs;
 *  </code>
 */
class OrganizationsLocationsEncryptionConfigs extends \Google\Service\Resource
{
  /**
   * Create an EncryptionConfig. (encryptionConfigs.create)
   *
   * @param string $parent Required. The location at which the EncryptionConfig is
   * to be created.
   * @param GoogleCloudDataplexV1EncryptionConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string encryptionConfigId Required. The ID of the EncryptionConfig
   * to create. Currently, only a value of "default" is supported.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1EncryptionConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Delete an EncryptionConfig. (encryptionConfigs.delete)
   *
   * @param string $name Required. The name of the EncryptionConfig to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. Etag of the EncryptionConfig. This is a
   * strong etag.
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
   * Get an EncryptionConfig. (encryptionConfigs.get)
   *
   * @param string $name Required. The name of the EncryptionConfig to fetch.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1EncryptionConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1EncryptionConfig::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (encryptionConfigs.getIamPolicy)
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
   * List EncryptionConfigs.
   * (encryptionConfigs.listOrganizationsLocationsEncryptionConfigs)
   *
   * @param string $parent Required. The location for which the EncryptionConfig
   * is to be listed.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter the EncryptionConfigs to be
   * returned. Using bare literals: (These values will be matched anywhere it may
   * appear in the object's field values) * filter=some_value Using fields: (These
   * values will be matched only in the specified field) *
   * filter=some_field=some_value Supported fields: * name, key, create_time,
   * update_time, encryption_state Example: *
   * filter=name=organizations/123/locations/us-central1/encryptionConfigs/test-
   * config conjunctions: (AND, OR, NOT) *
   * filter=name=organizations/123/locations/us-central1/encryptionConfigs/test-
   * config AND mode=CMEK logical operators: (>, <, >=, <=, !=, =, :), *
   * filter=create_time>2024-05-01T00:00:00.000Z
   * @opt_param string orderBy Optional. Order by fields for the result.
   * @opt_param int pageSize Optional. Maximum number of EncryptionConfigs to
   * return. The service may return fewer than this value. If unspecified, at most
   * 10 EncryptionConfigs will be returned. The maximum value is 1000; values
   * above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. Page token received from a previous
   * ListEncryptionConfigs call. Provide this to retrieve the subsequent page.
   * When paginating, the parameters - filter and order_by provided to
   * ListEncryptionConfigs must match the call that provided the page token.
   * @return GoogleCloudDataplexV1ListEncryptionConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsEncryptionConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListEncryptionConfigsResponse::class);
  }
  /**
   * Update an EncryptionConfig. (encryptionConfigs.patch)
   *
   * @param string $name Identifier. The resource name of the EncryptionConfig.
   * Format: organizations/{organization}/locations/{location}/encryptionConfigs/{
   * encryption_config} Global location is not supported.
   * @param GoogleCloudDataplexV1EncryptionConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Mask of fields to update. The service
   * treats an omitted field mask as an implied field mask equivalent to all
   * fields that are populated (have a non-empty value).
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDataplexV1EncryptionConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy.Can return NOT_FOUND, INVALID_ARGUMENT, and PERMISSION_DENIED
   * errors. (encryptionConfigs.setIamPolicy)
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
   * (encryptionConfigs.testIamPermissions)
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
class_alias(OrganizationsLocationsEncryptionConfigs::class, 'Google_Service_CloudDataplex_Resource_OrganizationsLocationsEncryptionConfigs');
