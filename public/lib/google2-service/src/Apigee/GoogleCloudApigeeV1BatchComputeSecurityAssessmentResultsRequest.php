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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequest extends \Google\Model
{
  protected $apiHubApisType = GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestApiHubApiArray::class;
  protected $apiHubApisDataType = '';
  protected $apiHubGatewaysType = GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestApiHubGatewayArray::class;
  protected $apiHubGatewaysDataType = '';
  protected $includeType = GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestResourceArray::class;
  protected $includeDataType = '';
  protected $includeAllResourcesType = GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestIncludeAll::class;
  protected $includeAllResourcesDataType = '';
  /**
   * Optional. The maximum number of results to return. The service may return
   * fewer than this value. If unspecified, at most 50 results will be returned.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token, received from a previous
   * `BatchComputeSecurityAssessmentResults` call. Provide this to retrieve the
   * subsequent page.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. Name of the profile that is used for computation.
   *
   * @var string
   */
  public $profile;
  /**
   * Optional. Scope of the resources for the computation. When computing scores
   * for Apigee proxies, the scope should be set to the environment of the
   * resources. When computing scores for API Hub deployments, api_hub_scope
   * should be set instead.
   *
   * @var string
   */
  public $scope;

  /**
   * An array of API Hub APIs to assess. A maximum of 1 API can be assessed.
   *
   * @param GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestApiHubApiArray $apiHubApis
   */
  public function setApiHubApis(GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestApiHubApiArray $apiHubApis)
  {
    $this->apiHubApis = $apiHubApis;
  }
  /**
   * @return GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestApiHubApiArray
   */
  public function getApiHubApis()
  {
    return $this->apiHubApis;
  }
  /**
   * An array of API Hub Gateways to assess. A maximum of 3 gateways can be
   * assessed.
   *
   * @param GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestApiHubGatewayArray $apiHubGateways
   */
  public function setApiHubGateways(GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestApiHubGatewayArray $apiHubGateways)
  {
    $this->apiHubGateways = $apiHubGateways;
  }
  /**
   * @return GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestApiHubGatewayArray
   */
  public function getApiHubGateways()
  {
    return $this->apiHubGateways;
  }
  /**
   * Include only these resources.
   *
   * @param GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestResourceArray $include
   */
  public function setInclude(GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestResourceArray $include)
  {
    $this->include = $include;
  }
  /**
   * @return GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestResourceArray
   */
  public function getInclude()
  {
    return $this->include;
  }
  /**
   * Include all resources under the scope.
   *
   * @param GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestIncludeAll $includeAllResources
   */
  public function setIncludeAllResources(GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestIncludeAll $includeAllResources)
  {
    $this->includeAllResources = $includeAllResources;
  }
  /**
   * @return GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestIncludeAll
   */
  public function getIncludeAllResources()
  {
    return $this->includeAllResources;
  }
  /**
   * Optional. The maximum number of results to return. The service may return
   * fewer than this value. If unspecified, at most 50 results will be returned.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Optional. A page token, received from a previous
   * `BatchComputeSecurityAssessmentResults` call. Provide this to retrieve the
   * subsequent page.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Required. Name of the profile that is used for computation.
   *
   * @param string $profile
   */
  public function setProfile($profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return string
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Optional. Scope of the resources for the computation. When computing scores
   * for Apigee proxies, the scope should be set to the environment of the
   * resources. When computing scores for API Hub deployments, api_hub_scope
   * should be set instead.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequest');
