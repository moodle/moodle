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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequest;
use Google\Service\Apigee\GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsResponse;

/**
 * The "securityAssessmentResults" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $securityAssessmentResults = $apigeeService->organizations_securityAssessmentResults;
 *  </code>
 */
class OrganizationsSecurityAssessmentResults extends \Google\Service\Resource
{
  /**
   * Compute RAV2 security scores for a set of resources.
   * (securityAssessmentResults.batchCompute)
   *
   * @param string $name Required. Name of the organization for which the score
   * needs to be computed in the following format:
   * `organizations/{org}/securityAssessmentResults`
   * @param GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsResponse
   * @throws \Google\Service\Exception
   */
  public function batchCompute($name, GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCompute', [$params], GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSecurityAssessmentResults::class, 'Google_Service_Apigee_Resource_OrganizationsSecurityAssessmentResults');
