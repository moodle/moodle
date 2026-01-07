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

namespace Google\Service\CloudSupport\Resource;

use Google\Service\CloudSupport\SearchCaseClassificationsResponse;

/**
 * The "caseClassifications" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudsupportService = new Google\Service\CloudSupport(...);
 *   $caseClassifications = $cloudsupportService->caseClassifications;
 *  </code>
 */
class CaseClassifications extends \Google\Service\Resource
{
  /**
   * Retrieve valid classifications to use when creating a support case.
   * Classifications are hierarchical. Each classification is a string containing
   * all levels of the hierarchy separated by `" > "`. For example, `"Technical
   * Issue > Compute > Compute Engine"`. Classification IDs returned by this
   * endpoint are valid for at least six months. When a classification is
   * deactivated, this endpoint immediately stops returning it. After six months,
   * `case.create` requests using the classification will fail. EXAMPLES: cURL:
   * ```shell curl \ --header "Authorization: Bearer $(gcloud auth print-access-
   * token)" \ 'https://cloudsupport.googleapis.com/v2/caseClassifications:search?
   * query=display_name:"*Compute%20Engine*"' ``` Python: ```python import
   * googleapiclient.discovery supportApiService =
   * googleapiclient.discovery.build( serviceName="cloudsupport", version="v2", di
   * scoveryServiceUrl=f"https://cloudsupport.googleapis.com/$discovery/rest?versi
   * on=v2", ) request = supportApiService.caseClassifications().search(
   * query='display_name:"*Compute Engine*"' ) print(request.execute()) ```
   * (caseClassifications.search)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of classifications fetched with
   * each request.
   * @opt_param string pageToken A token identifying the page of results to
   * return. If unspecified, the first page is retrieved.
   * @opt_param string query An expression used to filter case classifications. If
   * it's an empty string, then no filtering happens. Otherwise, case
   * classifications will be returned that match the filter.
   * @return SearchCaseClassificationsResponse
   * @throws \Google\Service\Exception
   */
  public function search($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], SearchCaseClassificationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CaseClassifications::class, 'Google_Service_CloudSupport_Resource_CaseClassifications');
