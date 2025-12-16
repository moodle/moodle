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

use Google\Service\CloudDataplex\GoogleCloudDataplexV1DataProduct;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListDataProductsResponse;
use Google\Service\CloudDataplex\GoogleIamV1Policy;
use Google\Service\CloudDataplex\GoogleIamV1SetIamPolicyRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\CloudDataplex\GoogleLongrunningOperation;

/**
 * The "dataProducts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $dataProducts = $dataplexService->projects_locations_dataProducts;
 *  </code>
 */
class ProjectsLocationsDataProducts extends \Google\Service\Resource
{
  /**
   * Creates a Data Product. (dataProducts.create)
   *
   * @param string $parent Required. The parent resource where this Data Product
   * will be created. Format:
   * projects/{project_id_or_number}/locations/{location_id}
   * @param GoogleCloudDataplexV1DataProduct $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dataProductId Optional. The ID of the Data Product to
   * create.The ID must conform to RFC-1034 and contain only lower-case letters
   * (a-z), numbers (0-9), or hyphens, with the first character a letter, the last
   * a letter or a number, and a 63 character maximum. Characters outside of ASCII
   * are not permitted. Valid format regex: (^a-z?$) If not provided, a system
   * generated ID will be used.
   * @opt_param bool validateOnly Optional. Validates the request without actually
   * creating the Data Product. Default: false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1DataProduct $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Data Product. The deletion will fail if the Data Product is not
   * empty (i.e. contains at least one Data Asset). (dataProducts.delete)
   *
   * @param string $name Required. The name of the Data Product to delete. Format:
   * projects/{project_id_or_number}/locations/{location_id}/dataProducts/{data_pr
   * oduct_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The etag of the Data Product.If an etag is
   * provided and does not match the current etag of the Data Product, then the
   * deletion will be blocked and an ABORTED error will be returned.
   * @opt_param bool validateOnly Optional. Validates the request without actually
   * deleting the Data Product. Default: false.
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
   * Gets a Data Product. (dataProducts.get)
   *
   * @param string $name Required. The name of the Data Product to retrieve.
   * Format: projects/{project_id_or_number}/locations/{location_id}/dataProducts/
   * {data_product_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1DataProduct
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1DataProduct::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (dataProducts.getIamPolicy)
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
   * Lists Data Products for a given project.
   * (dataProducts.listProjectsLocationsDataProducts)
   *
   * @param string $parent Required. The parent, which has this collection of Data
   * Products.Format:
   * projects/{project_id_or_number}/locations/{location_id}.Supports listing
   * across all locations with the wildcard - (hyphen) character. Example:
   * projects/{project_id_or_number}/locations/-
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that filters Data
   * Products listed in the response.Example of using this filter is:
   * display_name="my-data-product"
   * @opt_param string orderBy Optional. Order by expression that orders Data
   * Products listed in the response.Supported Order by fields are: name or
   * create_time.If not specified, the ordering is undefined.Ordering by
   * create_time is not supported when listing resources across locations (i.e.
   * when request contains /locations/-).
   * @opt_param int pageSize Optional. The maximum number of Data Products to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 Data Products will be returned. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * ListDataProducts call. Provide this to retrieve the subsequent page.When
   * paginating, all other parameters provided to ListDataProducts must match the
   * call that provided the page token.
   * @return GoogleCloudDataplexV1ListDataProductsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDataProducts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListDataProductsResponse::class);
  }
  /**
   * Updates a Data Product. (dataProducts.patch)
   *
   * @param string $name Identifier. Resource name of the Data Product. Format: pr
   * ojects/{project_id_or_number}/locations/{location_id}/dataProducts/{data_prod
   * uct_id}.
   * @param GoogleCloudDataplexV1DataProduct $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update. If this
   * is empty or not set, then all the fields will be updated.
   * @opt_param bool validateOnly Optional. Validates the request without actually
   * updating the Data Product. Default: false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDataplexV1DataProduct $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy.Can return NOT_FOUND, INVALID_ARGUMENT, and PERMISSION_DENIED
   * errors. (dataProducts.setIamPolicy)
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
   * (dataProducts.testIamPermissions)
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
class_alias(ProjectsLocationsDataProducts::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsDataProducts');
