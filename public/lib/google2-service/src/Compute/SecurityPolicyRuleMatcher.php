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

class SecurityPolicyRuleMatcher extends \Google\Model
{
  /**
   * Matches the source IP address of a request to the IP ranges supplied in
   * config.
   */
  public const VERSIONED_EXPR_SRC_IPS_V1 = 'SRC_IPS_V1';
  protected $configType = SecurityPolicyRuleMatcherConfig::class;
  protected $configDataType = '';
  protected $exprType = Expr::class;
  protected $exprDataType = '';
  protected $exprOptionsType = SecurityPolicyRuleMatcherExprOptions::class;
  protected $exprOptionsDataType = '';
  /**
   * Preconfigured versioned expression. If this field is specified, config must
   * also be specified. Available preconfigured expressions along with their
   * requirements are: SRC_IPS_V1 - must specify the corresponding src_ip_range
   * field in config.
   *
   * @var string
   */
  public $versionedExpr;

  /**
   * The configuration options available when specifying versioned_expr. This
   * field must be specified if versioned_expr is specified and cannot be
   * specified if versioned_expr is not specified.
   *
   * @param SecurityPolicyRuleMatcherConfig $config
   */
  public function setConfig(SecurityPolicyRuleMatcherConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return SecurityPolicyRuleMatcherConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * User defined CEVAL expression. A CEVAL expression is used to specify match
   * criteria such as origin.ip, source.region_code and contents in the request
   * header. Expressions containing `evaluateThreatIntelligence` require a Cloud
   * Armor Enterprise subscription and are not supported in Edge Policies nor in
   * Regional Policies. Expressions containing
   * `evaluatePreconfiguredExpr('sourceiplist-*')` require a Cloud Armor
   * Enterprise subscription and are only supported in Global Security Policies.
   *
   * @param Expr $expr
   */
  public function setExpr(Expr $expr)
  {
    $this->expr = $expr;
  }
  /**
   * @return Expr
   */
  public function getExpr()
  {
    return $this->expr;
  }
  /**
   * The configuration options available when specifying a user defined CEVAL
   * expression (i.e., 'expr').
   *
   * @param SecurityPolicyRuleMatcherExprOptions $exprOptions
   */
  public function setExprOptions(SecurityPolicyRuleMatcherExprOptions $exprOptions)
  {
    $this->exprOptions = $exprOptions;
  }
  /**
   * @return SecurityPolicyRuleMatcherExprOptions
   */
  public function getExprOptions()
  {
    return $this->exprOptions;
  }
  /**
   * Preconfigured versioned expression. If this field is specified, config must
   * also be specified. Available preconfigured expressions along with their
   * requirements are: SRC_IPS_V1 - must specify the corresponding src_ip_range
   * field in config.
   *
   * Accepted values: SRC_IPS_V1
   *
   * @param self::VERSIONED_EXPR_* $versionedExpr
   */
  public function setVersionedExpr($versionedExpr)
  {
    $this->versionedExpr = $versionedExpr;
  }
  /**
   * @return self::VERSIONED_EXPR_*
   */
  public function getVersionedExpr()
  {
    return $this->versionedExpr;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRuleMatcher::class, 'Google_Service_Compute_SecurityPolicyRuleMatcher');
