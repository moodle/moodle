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

namespace Google\Service\Compute;

class CustomErrorResponsePolicy extends \Google\Collection
{
  protected $collection_key = 'errorResponseRules';
  protected $errorResponseRulesType = CustomErrorResponsePolicyCustomErrorResponseRule::class;
  protected $errorResponseRulesDataType = 'array';
  /**
   * The full or partial URL to the BackendBucket resource that contains the
   * custom error content. Examples are:              - https://www.googleapis.c
   * om/compute/v1/projects/project/global/backendBuckets/myBackendBucket      -
   * compute/v1/projects/project/global/backendBuckets/myBackendBucket      -
   * global/backendBuckets/myBackendBucket
   *
   * If errorService is not specified at lower levels likepathMatcher, pathRule
   * and routeRule, an errorService specified at a higher level in theUrlMap
   * will be used. IfUrlMap.defaultCustomErrorResponsePolicy contains one or
   * moreerrorResponseRules[], it must specifyerrorService.
   *
   * If load balancer cannot reach the backendBucket, a simple Not Found Error
   * will be returned, with the original response code (oroverrideResponseCode
   * if configured).
   *
   * errorService is not supported for internal or regionalHTTP/HTTPS load
   * balancers.
   *
   * @var string
   */
  public $errorService;

  /**
   * Specifies rules for returning error responses.
   *
   * In a given policy, if you specify rules for both a range of error codes as
   * well as rules for specific error codes then rules with specific error codes
   * have a higher priority. For example, assume that you configure a rule for
   * 401 (Un-authorized) code, and another for all 4 series error codes (4XX).
   * If the backend service returns a401, then the rule for 401 will be applied.
   * However if the backend service returns a 403, the rule for4xx takes effect.
   *
   * @param CustomErrorResponsePolicyCustomErrorResponseRule[] $errorResponseRules
   */
  public function setErrorResponseRules($errorResponseRules)
  {
    $this->errorResponseRules = $errorResponseRules;
  }
  /**
   * @return CustomErrorResponsePolicyCustomErrorResponseRule[]
   */
  public function getErrorResponseRules()
  {
    return $this->errorResponseRules;
  }
  /**
   * The full or partial URL to the BackendBucket resource that contains the
   * custom error content. Examples are:              - https://www.googleapis.c
   * om/compute/v1/projects/project/global/backendBuckets/myBackendBucket      -
   * compute/v1/projects/project/global/backendBuckets/myBackendBucket      -
   * global/backendBuckets/myBackendBucket
   *
   * If errorService is not specified at lower levels likepathMatcher, pathRule
   * and routeRule, an errorService specified at a higher level in theUrlMap
   * will be used. IfUrlMap.defaultCustomErrorResponsePolicy contains one or
   * moreerrorResponseRules[], it must specifyerrorService.
   *
   * If load balancer cannot reach the backendBucket, a simple Not Found Error
   * will be returned, with the original response code (oroverrideResponseCode
   * if configured).
   *
   * errorService is not supported for internal or regionalHTTP/HTTPS load
   * balancers.
   *
   * @param string $errorService
   */
  public function setErrorService($errorService)
  {
    $this->errorService = $errorService;
  }
  /**
   * @return string
   */
  public function getErrorService()
  {
    return $this->errorService;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomErrorResponsePolicy::class, 'Google_Service_Compute_CustomErrorResponsePolicy');
