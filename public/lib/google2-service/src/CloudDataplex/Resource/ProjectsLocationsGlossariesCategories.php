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

use Google\Service\CloudDataplex\DataplexEmpty;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1GlossaryCategory;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListGlossaryCategoriesResponse;
use Google\Service\CloudDataplex\GoogleIamV1Policy;
use Google\Service\CloudDataplex\GoogleIamV1SetIamPolicyRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsResponse;

/**
 * The "categories" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $categories = $dataplexService->projects_locations_glossaries_categories;
 *  </code>
 */
class ProjectsLocationsGlossariesCategories extends \Google\Service\Resource
{
  /**
   * Creates a new GlossaryCategory resource. (categories.create)
   *
   * @param string $parent Required. The parent resource where this
   * GlossaryCategory will be created. Format: projects/{project_id_or_number}/loc
   * ations/{location_id}/glossaries/{glossary_id} where locationId refers to a
   * Google Cloud region.
   * @param GoogleCloudDataplexV1GlossaryCategory $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string categoryId Required. GlossaryCategory identifier.
   * @return GoogleCloudDataplexV1GlossaryCategory
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1GlossaryCategory $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDataplexV1GlossaryCategory::class);
  }
  /**
   * Deletes a GlossaryCategory resource. All the GlossaryCategories and
   * GlossaryTerms nested directly under the specified GlossaryCategory will be
   * moved one level up to the parent in the hierarchy. (categories.delete)
   *
   * @param string $name Required. The name of the GlossaryCategory to delete.
   * Format: projects/{project_id_or_number}/locations/{location_id}/glossaries/{g
   * lossary_id}/categories/{category_id}
   * @param array $optParams Optional parameters.
   * @return DataplexEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DataplexEmpty::class);
  }
  /**
   * Gets a GlossaryCategory resource. (categories.get)
   *
   * @param string $name Required. The name of the GlossaryCategory to retrieve.
   * Format: projects/{project_id_or_number}/locations/{location_id}/glossaries/{g
   * lossary_id}/categories/{category_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1GlossaryCategory
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1GlossaryCategory::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (categories.getIamPolicy)
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
   * Lists GlossaryCategory resources in a Glossary.
   * (categories.listProjectsLocationsGlossariesCategories)
   *
   * @param string $parent Required. The parent, which has this collection of
   * GlossaryCategories. Format: projects/{project_id_or_number}/locations/{locati
   * on_id}/glossaries/{glossary_id} Location is the Google Cloud region.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that filters
   * GlossaryCategories listed in the response. Filters are supported on the
   * following fields: - immediate_parentExamples of using a filter are: - immedia
   * te_parent="projects/{project_id_or_number}/locations/{location_id}/glossaries
   * /{glossary_id}" - immediate_parent="projects/{project_id_or_number}/locations
   * /{location_id}/glossaries/{glossary_id}/categories/{category_id}"This will
   * only return the GlossaryCategories that are directly nested under the
   * specified parent.
   * @opt_param string orderBy Optional. Order by expression that orders
   * GlossaryCategories listed in the response. Order by fields are: name or
   * create_time for the result. If not specified, the ordering is undefined.
   * @opt_param int pageSize Optional. The maximum number of GlossaryCategories to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 GlossaryCategories will be returned. The maximum value is 1000; values
   * above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * ListGlossaryCategories call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to ListGlossaryCategories must
   * match the call that provided the page token.
   * @return GoogleCloudDataplexV1ListGlossaryCategoriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGlossariesCategories($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListGlossaryCategoriesResponse::class);
  }
  /**
   * Updates a GlossaryCategory resource. (categories.patch)
   *
   * @param string $name Output only. Identifier. The resource name of the
   * GlossaryCategory. Format: projects/{project_id_or_number}/locations/{location
   * _id}/glossaries/{glossary_id}/categories/{category_id}
   * @param GoogleCloudDataplexV1GlossaryCategory $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudDataplexV1GlossaryCategory
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDataplexV1GlossaryCategory $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDataplexV1GlossaryCategory::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy.Can return NOT_FOUND, INVALID_ARGUMENT, and PERMISSION_DENIED
   * errors. (categories.setIamPolicy)
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
   * (categories.testIamPermissions)
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
class_alias(ProjectsLocationsGlossariesCategories::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsGlossariesCategories');
