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

namespace Google\Service\ChecksService\Resource;

use Google\Service\ChecksService\GoogleChecksRepoScanV1alphaGenerateScanRequest;
use Google\Service\ChecksService\GoogleChecksRepoScanV1alphaListRepoScansResponse;
use Google\Service\ChecksService\GoogleChecksRepoScanV1alphaRepoScan;
use Google\Service\ChecksService\Operation;

/**
 * The "scans" collection of methods.
 * Typical usage is:
 *  <code>
 *   $checksService = new Google\Service\ChecksService(...);
 *   $scans = $checksService->accounts_repos_scans;
 *  </code>
 */
class AccountsReposScans extends \Google\Service\Resource
{
  /**
   * Uploads the results of local Code Compliance analysis and generates a scan of
   * privacy issues. Returns a google.longrunning.Operation containing analysis
   * and findings. (scans.generate)
   *
   * @param string $parent Required. Resource name of the repo. Example:
   * `accounts/123/repos/456`
   * @param GoogleChecksRepoScanV1alphaGenerateScanRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function generate($parent, GoogleChecksRepoScanV1alphaGenerateScanRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generate', [$params], Operation::class);
  }
  /**
   * Gets a repo scan. By default, only the name and results_uri fields are
   * returned. You can include other fields by listing them in the `fields` URL
   * query parameter. For example, `?fields=name,sources` will return the name and
   * sources fields. (scans.get)
   *
   * @param string $name Required. Resource name of the repo scan. Example:
   * `accounts/123/repos/456/scans/789`
   * @param array $optParams Optional parameters.
   * @return GoogleChecksRepoScanV1alphaRepoScan
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleChecksRepoScanV1alphaRepoScan::class);
  }
  /**
   * Lists repo scans for the specified repo. (scans.listAccountsReposScans)
   *
   * @param string $parent Required. Resource name of the repo. Example:
   * `accounts/123/repos/456`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An [AIP-160](https://google.aip.dev/160)
   * filter string to filter repo scans. Example: `scmMetadata.branch = main`
   * @opt_param int pageSize Optional. The maximum number of repo scans to return.
   * If unspecified, at most 10 repo scans will be returned. The maximum value is
   * 50; values above 50 will be coerced to 50.
   * @opt_param string pageToken Optional. A page token received from a previous
   * `ListRepoScans` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListRepoScans` must match the
   * call that provided the page token.
   * @return GoogleChecksRepoScanV1alphaListRepoScansResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsReposScans($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleChecksRepoScanV1alphaListRepoScansResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsReposScans::class, 'Google_Service_ChecksService_Resource_AccountsReposScans');
