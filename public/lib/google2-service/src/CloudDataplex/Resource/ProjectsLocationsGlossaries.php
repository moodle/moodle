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

use Google\Service\CloudDataplex\GoogleCloudDataplexV1Glossary;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListGlossariesResponse;
use Google\Service\CloudDataplex\GoogleIamV1Policy;
use Google\Service\CloudDataplex\GoogleIamV1SetIamPolicyRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\CloudDataplex\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\CloudDataplex\GoogleLongrunningOperation;

/**
 * The "glossaries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $glossaries = $dataplexService->projects_locations_glossaries;
 *  </code>
 */
class ProjectsLocationsGlossaries extends \Google\Service\Resource
{
  /**
   * Creates a new Glossary resource. (glossaries.create)
   *
   * @param string $parent Required. The parent resource where this Glossary will
   * be created. Format: projects/{project_id_or_number}/locations/{location_id}
   * where location_id refers to a Google Cloud region.
   * @param GoogleCloudDataplexV1Glossary $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string glossaryId Required. Glossary ID: Glossary identifier.
   * @opt_param bool validateOnly Optional. Validates the request without actually
   * creating the Glossary. Default: false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1Glossary $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Glossary resource. All the categories and terms within the Glossary
   * must be deleted before the Glossary can be deleted. (glossaries.delete)
   *
   * @param string $name Required. The name of the Glossary to delete. Format: pro
   * jects/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The etag of the Glossary. If this is
   * provided, it must match the server's etag. If the etag is provided and does
   * not match the server-computed etag, the request must fail with a ABORTED
   * error code.
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
   * Gets a Glossary resource. (glossaries.get)
   *
   * @param string $name Required. The name of the Glossary to retrieve. Format: p
   * rojects/{project_id_or_number}/locations/{location_id}/glossaries/{glossary_i
   * d}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1Glossary
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1Glossary::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (glossaries.getIamPolicy)
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
   * Lists Glossary resources in a project and location.
   * (glossaries.listProjectsLocationsGlossaries)
   *
   * @param string $parent Required. The parent, which has this collection of
   * Glossaries. Format: projects/{project_id_or_number}/locations/{location_id}
   * where location_id refers to a Google Cloud region.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that filters Glossaries
   * listed in the response. Filters on proto fields of Glossary are supported.
   * Examples of using a filter are: - display_name="my-glossary" -
   * categoryCount=1 - termCount=0
   * @opt_param string orderBy Optional. Order by expression that orders
   * Glossaries listed in the response. Order by fields are: name or create_time
   * for the result. If not specified, the ordering is undefined.
   * @opt_param int pageSize Optional. The maximum number of Glossaries to return.
   * The service may return fewer than this value. If unspecified, at most 50
   * Glossaries will be returned. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * ListGlossaries call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to ListGlossaries must match the
   * call that provided the page token.
   * @return GoogleCloudDataplexV1ListGlossariesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGlossaries($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListGlossariesResponse::class);
  }
  /**
   * Updates a Glossary resource. (glossaries.patch)
   *
   * @param string $name Output only. Identifier. The resource name of the
   * Glossary. Format: projects/{project_id_or_number}/locations/{location_id}/glo
   * ssaries/{glossary_id}
   * @param GoogleCloudDataplexV1Glossary $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @opt_param bool validateOnly Optional. Validates the request without actually
   * updating the Glossary. Default: false.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDataplexV1Glossary $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy.Can return NOT_FOUND, INVALID_ARGUMENT, and PERMISSION_DENIED
   * errors. (glossaries.setIamPolicy)
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
   * (glossaries.testIamPermissions)
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
class_alias(ProjectsLocationsGlossaries::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsGlossaries');
