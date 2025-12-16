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

class SecurityPolicyRulePreconfiguredWafConfigExclusion extends \Google\Collection
{
  protected $collection_key = 'targetRuleIds';
  protected $requestCookiesToExcludeType = SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams::class;
  protected $requestCookiesToExcludeDataType = 'array';
  protected $requestHeadersToExcludeType = SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams::class;
  protected $requestHeadersToExcludeDataType = 'array';
  protected $requestQueryParamsToExcludeType = SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams::class;
  protected $requestQueryParamsToExcludeDataType = 'array';
  protected $requestUrisToExcludeType = SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams::class;
  protected $requestUrisToExcludeDataType = 'array';
  /**
   * A list of target rule IDs under the WAF rule set to apply the preconfigured
   * WAF exclusion. If omitted, it refers to all the rule IDs under the WAF rule
   * set.
   *
   * @var string[]
   */
  public $targetRuleIds;
  /**
   * Target WAF rule set to apply the preconfigured WAF exclusion.
   *
   * @var string
   */
  public $targetRuleSet;

  /**
   * A list of request cookie names whose value will be excluded from inspection
   * during preconfigured WAF evaluation.
   *
   * @param SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams[] $requestCookiesToExclude
   */
  public function setRequestCookiesToExclude($requestCookiesToExclude)
  {
    $this->requestCookiesToExclude = $requestCookiesToExclude;
  }
  /**
   * @return SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams[]
   */
  public function getRequestCookiesToExclude()
  {
    return $this->requestCookiesToExclude;
  }
  /**
   * A list of request header names whose value will be excluded from inspection
   * during preconfigured WAF evaluation.
   *
   * @param SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams[] $requestHeadersToExclude
   */
  public function setRequestHeadersToExclude($requestHeadersToExclude)
  {
    $this->requestHeadersToExclude = $requestHeadersToExclude;
  }
  /**
   * @return SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams[]
   */
  public function getRequestHeadersToExclude()
  {
    return $this->requestHeadersToExclude;
  }
  /**
   * A list of request query parameter names whose value will be excluded from
   * inspection during preconfigured WAF evaluation. Note that the parameter can
   * be in the query string or in the POST body.
   *
   * @param SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams[] $requestQueryParamsToExclude
   */
  public function setRequestQueryParamsToExclude($requestQueryParamsToExclude)
  {
    $this->requestQueryParamsToExclude = $requestQueryParamsToExclude;
  }
  /**
   * @return SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams[]
   */
  public function getRequestQueryParamsToExclude()
  {
    return $this->requestQueryParamsToExclude;
  }
  /**
   * A list of request URIs from the request line to be excluded from inspection
   * during preconfigured WAF evaluation. When specifying this field, the query
   * or fragment part should be excluded.
   *
   * @param SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams[] $requestUrisToExclude
   */
  public function setRequestUrisToExclude($requestUrisToExclude)
  {
    $this->requestUrisToExclude = $requestUrisToExclude;
  }
  /**
   * @return SecurityPolicyRulePreconfiguredWafConfigExclusionFieldParams[]
   */
  public function getRequestUrisToExclude()
  {
    return $this->requestUrisToExclude;
  }
  /**
   * A list of target rule IDs under the WAF rule set to apply the preconfigured
   * WAF exclusion. If omitted, it refers to all the rule IDs under the WAF rule
   * set.
   *
   * @param string[] $targetRuleIds
   */
  public function setTargetRuleIds($targetRuleIds)
  {
    $this->targetRuleIds = $targetRuleIds;
  }
  /**
   * @return string[]
   */
  public function getTargetRuleIds()
  {
    return $this->targetRuleIds;
  }
  /**
   * Target WAF rule set to apply the preconfigured WAF exclusion.
   *
   * @param string $targetRuleSet
   */
  public function setTargetRuleSet($targetRuleSet)
  {
    $this->targetRuleSet = $targetRuleSet;
  }
  /**
   * @return string
   */
  public function getTargetRuleSet()
  {
    return $this->targetRuleSet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRulePreconfiguredWafConfigExclusion::class, 'Google_Service_Compute_SecurityPolicyRulePreconfiguredWafConfigExclusion');
