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

namespace Google\Service\Kmsinventory\Resource;

use Google\Service\Kmsinventory\GoogleCloudKmsInventoryV1SearchProtectedResourcesResponse;

/**
 * The "protectedResources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $kmsinventoryService = new Google\Service\Kmsinventory(...);
 *   $protectedResources = $kmsinventoryService->organizations_protectedResources;
 *  </code>
 */
class OrganizationsProtectedResources extends \Google\Service\Resource
{
  /**
   * Returns metadata about the resources protected by the given Cloud KMS
   * CryptoKey in the given Cloud organization. (protectedResources.search)
   *
   * @param string $scope Required. Resource name of the organization. Example:
   * organizations/123
   * @param array $optParams Optional parameters.
   *
   * @opt_param string cryptoKey Required. The resource name of the CryptoKey.
   * @opt_param int pageSize The maximum number of resources to return. The
   * service may return fewer than this value. If unspecified, at most 500
   * resources will be returned. The maximum value is 500; values above 500 will
   * be coerced to 500.
   * @opt_param string pageToken A page token, received from a previous
   * KeyTrackingService.SearchProtectedResources call. Provide this to retrieve
   * the subsequent page. When paginating, all other parameters provided to
   * KeyTrackingService.SearchProtectedResources must match the call that provided
   * the page token.
   * @opt_param string resourceTypes Optional. A list of resource types that this
   * request searches for. If empty, it will search all the [trackable resource
   * types](https://cloud.google.com/kms/docs/view-key-usage#tracked-resource-
   * types). Regular expressions are also supported. For example: *
   * `compute.googleapis.com.*` snapshots resources whose type starts with
   * `compute.googleapis.com`. * `.*Image` snapshots resources whose type ends
   * with `Image`. * `.*Image.*` snapshots resources whose type contains `Image`.
   * See [RE2](https://github.com/google/re2/wiki/Syntax) for all supported
   * regular expression syntax. If the regular expression does not match any
   * supported resource type, an INVALID_ARGUMENT error will be returned.
   * @return GoogleCloudKmsInventoryV1SearchProtectedResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function search($scope, $optParams = [])
  {
    $params = ['scope' => $scope];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GoogleCloudKmsInventoryV1SearchProtectedResourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsProtectedResources::class, 'Google_Service_Kmsinventory_Resource_OrganizationsProtectedResources');
