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

namespace Google\Service\PolicySimulator\Resource;

use Google\Service\PolicySimulator\GoogleCloudPolicysimulatorV1ListOrgPolicyViolationsPreviewsResponse;
use Google\Service\PolicySimulator\GoogleCloudPolicysimulatorV1OrgPolicyViolationsPreview;
use Google\Service\PolicySimulator\GoogleLongrunningOperation;

/**
 * The "orgPolicyViolationsPreviews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $policysimulatorService = new Google\Service\PolicySimulator(...);
 *   $orgPolicyViolationsPreviews = $policysimulatorService->organizations_locations_orgPolicyViolationsPreviews;
 *  </code>
 */
class OrganizationsLocationsOrgPolicyViolationsPreviews extends \Google\Service\Resource
{
  /**
   * CreateOrgPolicyViolationsPreview creates an OrgPolicyViolationsPreview for
   * the proposed changes in the provided
   * OrgPolicyViolationsPreview.OrgPolicyOverlay. The changes to OrgPolicy are
   * specified by this `OrgPolicyOverlay`. The resources to scan are inferred from
   * these specified changes. (orgPolicyViolationsPreviews.create)
   *
   * @param string $parent Required. The organization under which this
   * OrgPolicyViolationsPreview will be created. Example: `organizations/my-
   * example-org/locations/global`
   * @param GoogleCloudPolicysimulatorV1OrgPolicyViolationsPreview $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orgPolicyViolationsPreviewId Optional. An optional user-
   * specified ID for the OrgPolicyViolationsPreview. If not provided, a random ID
   * will be generated.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudPolicysimulatorV1OrgPolicyViolationsPreview $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * GetOrgPolicyViolationsPreview gets the specified OrgPolicyViolationsPreview.
   * Each OrgPolicyViolationsPreview is available for at least 7 days.
   * (orgPolicyViolationsPreviews.get)
   *
   * @param string $name Required. The name of the OrgPolicyViolationsPreview to
   * get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudPolicysimulatorV1OrgPolicyViolationsPreview
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudPolicysimulatorV1OrgPolicyViolationsPreview::class);
  }
  /**
   * ListOrgPolicyViolationsPreviews lists each OrgPolicyViolationsPreview in an
   * organization. Each OrgPolicyViolationsPreview is available for at least 7
   * days. (orgPolicyViolationsPreviews.listOrganizationsLocationsOrgPolicyViolati
   * onsPreviews)
   *
   * @param string $parent Required. The parent the violations are scoped to.
   * Format: `organizations/{organization}/locations/{location}` Example:
   * `organizations/my-example-org/locations/global`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return. The
   * service may return fewer than this value. If unspecified, at most 5 items
   * will be returned. The maximum value is 10; values above 10 will be coerced to
   * 10.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * call. Provide this to retrieve the subsequent page. When paginating, all
   * other parameters must match the call that provided the page token.
   * @return GoogleCloudPolicysimulatorV1ListOrgPolicyViolationsPreviewsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsOrgPolicyViolationsPreviews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudPolicysimulatorV1ListOrgPolicyViolationsPreviewsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsOrgPolicyViolationsPreviews::class, 'Google_Service_PolicySimulator_Resource_OrganizationsLocationsOrgPolicyViolationsPreviews');
