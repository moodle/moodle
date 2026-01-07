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

use Google\Service\PolicySimulator\GoogleCloudPolicysimulatorV1ListOrgPolicyViolationsResponse;

/**
 * The "orgPolicyViolations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $policysimulatorService = new Google\Service\PolicySimulator(...);
 *   $orgPolicyViolations = $policysimulatorService->organizations_locations_orgPolicyViolationsPreviews_orgPolicyViolations;
 *  </code>
 */
class OrganizationsLocationsOrgPolicyViolationsPreviewsOrgPolicyViolations extends \Google\Service\Resource
{
  /**
   * ListOrgPolicyViolations lists the OrgPolicyViolations that are present in an
   * OrgPolicyViolationsPreview. (orgPolicyViolations.listOrganizationsLocationsOr
   * gPolicyViolationsPreviewsOrgPolicyViolations)
   *
   * @param string $parent Required. The OrgPolicyViolationsPreview to get
   * OrgPolicyViolations from. Format: organizations/{organization}/locations/{loc
   * ation}/orgPolicyViolationsPreviews/{orgPolicyViolationsPreview}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return. The
   * service may return fewer than this value. If unspecified, at most 1000 items
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * call. Provide this to retrieve the subsequent page. When paginating, all
   * other parameters must match the call that provided the page token.
   * @return GoogleCloudPolicysimulatorV1ListOrgPolicyViolationsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsOrgPolicyViolationsPreviewsOrgPolicyViolations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudPolicysimulatorV1ListOrgPolicyViolationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsOrgPolicyViolationsPreviewsOrgPolicyViolations::class, 'Google_Service_PolicySimulator_Resource_OrganizationsLocationsOrgPolicyViolationsPreviewsOrgPolicyViolations');
