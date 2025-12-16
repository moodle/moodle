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

namespace Google\Service\Integrations\Resource;

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaListTestCasesResponse;

/**
 * The "testCases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $testCases = $integrationsService->projects_locations_products_integrations_versions_testCases;
 *  </code>
 */
class ProjectsLocationsProductsIntegrationsVersionsTestCases extends \Google\Service\Resource
{
  /**
   * Lists all the test cases that satisfy the filters.
   * (testCases.listProjectsLocationsProductsIntegrationsVersionsTestCases)
   *
   * @param string $parent Required. The parent resource where this TestCase was
   * created.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Standard filter field. Filtering as
   * supported in https://developers.google.com/authorized-
   * buyers/apis/guides/list-filters.
   * @opt_param string orderBy Optional. The results would be returned in order
   * specified here. Currently supported sort keys are: Descending sort order for
   * "last_modified_time", "created_time". Ascending sort order for "name".
   * @opt_param int pageSize Optional. The maximum number of test cases to return.
   * The service may return fewer than this value. If unspecified, at most 100
   * test cases will be returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListTestCases` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListTestCases` must match the
   * call that provided the page token.
   * @opt_param string readMask Optional. The mask which specifies fields that
   * need to be returned in the TestCases's response.
   * @return GoogleCloudIntegrationsV1alphaListTestCasesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsProductsIntegrationsVersionsTestCases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudIntegrationsV1alphaListTestCasesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsProductsIntegrationsVersionsTestCases::class, 'Google_Service_Integrations_Resource_ProjectsLocationsProductsIntegrationsVersionsTestCases');
