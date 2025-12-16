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

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaDownloadTestCaseResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaExecuteTestCaseRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaExecuteTestCaseResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaExecuteTestCasesRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaExecuteTestCasesResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaListTestCasesResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaTakeoverTestCaseEditLockRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaTestCase;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUploadTestCaseRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUploadTestCaseResponse;
use Google\Service\Integrations\GoogleProtobufEmpty;

/**
 * The "testCases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $testCases = $integrationsService->projects_locations_integrations_versions_testCases;
 *  </code>
 */
class ProjectsLocationsIntegrationsVersionsTestCases extends \Google\Service\Resource
{
  /**
   * Creates a new test case (testCases.create)
   *
   * @param string $parent Required. The parent resource where this test case will
   * be created. Format: projects/{project}/locations/{location}/integrations/{int
   * egration}/versions/{integration_version}
   * @param GoogleCloudIntegrationsV1alphaTestCase $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string testCaseId Required. Required
   * @return GoogleCloudIntegrationsV1alphaTestCase
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudIntegrationsV1alphaTestCase $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudIntegrationsV1alphaTestCase::class);
  }
  /**
   * Deletes a test case (testCases.delete)
   *
   * @param string $name Required. ID for the test case to be deleted
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Downloads a test case. Retrieves the `TestCase` for a given `test_case_id`
   * and returns the response as a string. (testCases.download)
   *
   * @param string $name Required. The test case to download. Format: projects/{pr
   * oject}/locations/{location}/integrations/{integration}/versions/{integration_
   * version}/testCases/{test_case_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fileFormat File format for download request.
   * @return GoogleCloudIntegrationsV1alphaDownloadTestCaseResponse
   * @throws \Google\Service\Exception
   */
  public function download($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('download', [$params], GoogleCloudIntegrationsV1alphaDownloadTestCaseResponse::class);
  }
  /**
   * Executes all test cases in an integration version. (testCases.execute)
   *
   * @param string $parent Required. The parent resource whose test cases are
   * executed. Format: projects/{project}/locations/{location}/integrations/{integ
   * ration}/versions/{integration_version}
   * @param GoogleCloudIntegrationsV1alphaExecuteTestCasesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaExecuteTestCasesResponse
   * @throws \Google\Service\Exception
   */
  public function execute($parent, GoogleCloudIntegrationsV1alphaExecuteTestCasesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('execute', [$params], GoogleCloudIntegrationsV1alphaExecuteTestCasesResponse::class);
  }
  /**
   * Executes functional test (testCases.executeTest)
   *
   * @param string $testCaseName Required. Test case resource name
   * @param GoogleCloudIntegrationsV1alphaExecuteTestCaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaExecuteTestCaseResponse
   * @throws \Google\Service\Exception
   */
  public function executeTest($testCaseName, GoogleCloudIntegrationsV1alphaExecuteTestCaseRequest $postBody, $optParams = [])
  {
    $params = ['testCaseName' => $testCaseName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('executeTest', [$params], GoogleCloudIntegrationsV1alphaExecuteTestCaseResponse::class);
  }
  /**
   * Get a test case (testCases.get)
   *
   * @param string $name Required. The ID of the test case to retrieve
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaTestCase
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudIntegrationsV1alphaTestCase::class);
  }
  /**
   * Lists all the test cases that satisfy the filters.
   * (testCases.listProjectsLocationsIntegrationsVersionsTestCases)
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
  public function listProjectsLocationsIntegrationsVersionsTestCases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudIntegrationsV1alphaListTestCasesResponse::class);
  }
  /**
   * Updates a test case (testCases.patch)
   *
   * @param string $name Output only. Auto-generated primary key.
   * @param GoogleCloudIntegrationsV1alphaTestCase $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask specifying the fields in
   * the above integration that have been modified and need to be updated.
   * @return GoogleCloudIntegrationsV1alphaTestCase
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudIntegrationsV1alphaTestCase $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudIntegrationsV1alphaTestCase::class);
  }
  /**
   * Clear the lock fields and assign them to current user
   * (testCases.takeoverEditLock)
   *
   * @param string $name Required. The ID of test case to takeover edit lock.
   * Format: projects/{project}/locations/{location}/integrations/{integration}/ve
   * rsions/{integration_version}/testCases/{test_case_id}
   * @param GoogleCloudIntegrationsV1alphaTakeoverTestCaseEditLockRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaTestCase
   * @throws \Google\Service\Exception
   */
  public function takeoverEditLock($name, GoogleCloudIntegrationsV1alphaTakeoverTestCaseEditLockRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('takeoverEditLock', [$params], GoogleCloudIntegrationsV1alphaTestCase::class);
  }
  /**
   * Uploads a test case. The content can be a previously downloaded test case.
   * Performs the same function as CreateTestCase, but accepts input in a string
   * format, which holds the complete representation of the TestCase content.
   * (testCases.upload)
   *
   * @param string $parent Required. The test case to upload. Format: projects/{pr
   * oject}/locations/{location}/integrations/{integration}/versions/{integration_
   * version}
   * @param GoogleCloudIntegrationsV1alphaUploadTestCaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaUploadTestCaseResponse
   * @throws \Google\Service\Exception
   */
  public function upload($parent, GoogleCloudIntegrationsV1alphaUploadTestCaseRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], GoogleCloudIntegrationsV1alphaUploadTestCaseResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsIntegrationsVersionsTestCases::class, 'Google_Service_Integrations_Resource_ProjectsLocationsIntegrationsVersionsTestCases');
