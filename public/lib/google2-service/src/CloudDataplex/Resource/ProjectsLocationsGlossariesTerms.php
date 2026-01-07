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
use Google\Service\CloudDataplex\GoogleCloudDataplexV1GlossaryTerm;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListGlossaryTermsResponse;
use Google\Service\CloudDataplex\GoogleIamV1Policy;
use Google\Service\CloudDataplex\GoogleIamV1SetIamPolicyRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsResponse;

/**
 * The "terms" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $terms = $dataplexService->projects_locations_glossaries_terms;
 *  </code>
 */
class ProjectsLocationsGlossariesTerms extends \Google\Service\Resource
{
  /**
   * Creates a new GlossaryTerm resource. (terms.create)
   *
   * @param string $parent Required. The parent resource where the GlossaryTerm
   * will be created. Format: projects/{project_id_or_number}/locations/{location_
   * id}/glossaries/{glossary_id} where location_id refers to a Google Cloud
   * region.
   * @param GoogleCloudDataplexV1GlossaryTerm $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string termId Required. GlossaryTerm identifier.
   * @return GoogleCloudDataplexV1GlossaryTerm
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1GlossaryTerm $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDataplexV1GlossaryTerm::class);
  }
  /**
   * Deletes a GlossaryTerm resource. (terms.delete)
   *
   * @param string $name Required. The name of the GlossaryTerm to delete. Format:
   * projects/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_
   * id}/terms/{term_id}
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
   * Gets a GlossaryTerm resource. (terms.get)
   *
   * @param string $name Required. The name of the GlossaryTerm to retrieve.
   * Format: projects/{project_id_or_number}/locations/{location_id}/glossaries/{g
   * lossary_id}/terms/{term_id}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1GlossaryTerm
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1GlossaryTerm::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (terms.getIamPolicy)
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
   * Lists GlossaryTerm resources in a Glossary.
   * (terms.listProjectsLocationsGlossariesTerms)
   *
   * @param string $parent Required. The parent, which has this collection of
   * GlossaryTerms. Format: projects/{project_id_or_number}/locations/{location_id
   * }/glossaries/{glossary_id} where location_id refers to a Google Cloud region.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that filters
   * GlossaryTerms listed in the response. Filters are supported on the following
   * fields: - immediate_parentExamples of using a filter are: - immediate_parent=
   * "projects/{project_id_or_number}/locations/{location_id}/glossaries/{glossary
   * _id}" - immediate_parent="projects/{project_id_or_number}/locations/{location
   * _id}/glossaries/{glossary_id}/categories/{category_id}"This will only return
   * the GlossaryTerms that are directly nested under the specified parent.
   * @opt_param string orderBy Optional. Order by expression that orders
   * GlossaryTerms listed in the response. Order by fields are: name or
   * create_time for the result. If not specified, the ordering is undefined.
   * @opt_param int pageSize Optional. The maximum number of GlossaryTerms to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 GlossaryTerms will be returned. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * ListGlossaryTerms call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to ListGlossaryTerms must match the
   * call that provided the page token.
   * @return GoogleCloudDataplexV1ListGlossaryTermsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGlossariesTerms($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListGlossaryTermsResponse::class);
  }
  /**
   * Updates a GlossaryTerm resource. (terms.patch)
   *
   * @param string $name Output only. Identifier. The resource name of the
   * GlossaryTerm. Format: projects/{project_id_or_number}/locations/{location_id}
   * /glossaries/{glossary_id}/terms/{term_id}
   * @param GoogleCloudDataplexV1GlossaryTerm $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudDataplexV1GlossaryTerm
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDataplexV1GlossaryTerm $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDataplexV1GlossaryTerm::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy.Can return NOT_FOUND, INVALID_ARGUMENT, and PERMISSION_DENIED
   * errors. (terms.setIamPolicy)
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
   * This operation may "fail open" without warning. (terms.testIamPermissions)
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
class_alias(ProjectsLocationsGlossariesTerms::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsGlossariesTerms');
