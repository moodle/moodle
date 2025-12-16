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

namespace Google\Service\CloudAsset;

class GoogleCloudAssetV1Rule extends \Google\Model
{
  /**
   * Setting this to true means that all values are allowed. This field can be
   * set only in Policies for list constraints.
   *
   * @var bool
   */
  public $allowAll;
  protected $conditionType = Expr::class;
  protected $conditionDataType = '';
  protected $conditionEvaluationType = ConditionEvaluation::class;
  protected $conditionEvaluationDataType = '';
  /**
   * Setting this to true means that all values are denied. This field can be
   * set only in Policies for list constraints.
   *
   * @var bool
   */
  public $denyAll;
  /**
   * If `true`, then the `Policy` is enforced. If `false`, then any
   * configuration is acceptable. This field can be set only in Policies for
   * boolean constraints.
   *
   * @var bool
   */
  public $enforce;
  protected $valuesType = GoogleCloudAssetV1StringValues::class;
  protected $valuesDataType = '';

  /**
   * Setting this to true means that all values are allowed. This field can be
   * set only in Policies for list constraints.
   *
   * @param bool $allowAll
   */
  public function setAllowAll($allowAll)
  {
    $this->allowAll = $allowAll;
  }
  /**
   * @return bool
   */
  public function getAllowAll()
  {
    return $this->allowAll;
  }
  /**
   * The evaluating condition for this rule.
   *
   * @param Expr $condition
   */
  public function setCondition(Expr $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return Expr
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * The condition evaluation result for this rule. Only populated if it meets
   * all the following criteria: * There is a condition defined for this rule. *
   * This rule is within AnalyzeOrgPolicyGovernedContainersResponse.GovernedCont
   * ainer.consolidated_policy, or
   * AnalyzeOrgPolicyGovernedAssetsResponse.GovernedAsset.consolidated_policy
   * when the AnalyzeOrgPolicyGovernedAssetsResponse.GovernedAsset has
   * AnalyzeOrgPolicyGovernedAssetsResponse.GovernedAsset.governed_resource.
   *
   * @param ConditionEvaluation $conditionEvaluation
   */
  public function setConditionEvaluation(ConditionEvaluation $conditionEvaluation)
  {
    $this->conditionEvaluation = $conditionEvaluation;
  }
  /**
   * @return ConditionEvaluation
   */
  public function getConditionEvaluation()
  {
    return $this->conditionEvaluation;
  }
  /**
   * Setting this to true means that all values are denied. This field can be
   * set only in Policies for list constraints.
   *
   * @param bool $denyAll
   */
  public function setDenyAll($denyAll)
  {
    $this->denyAll = $denyAll;
  }
  /**
   * @return bool
   */
  public function getDenyAll()
  {
    return $this->denyAll;
  }
  /**
   * If `true`, then the `Policy` is enforced. If `false`, then any
   * configuration is acceptable. This field can be set only in Policies for
   * boolean constraints.
   *
   * @param bool $enforce
   */
  public function setEnforce($enforce)
  {
    $this->enforce = $enforce;
  }
  /**
   * @return bool
   */
  public function getEnforce()
  {
    return $this->enforce;
  }
  /**
   * List of values to be used for this policy rule. This field can be set only
   * in policies for list constraints.
   *
   * @param GoogleCloudAssetV1StringValues $values
   */
  public function setValues(GoogleCloudAssetV1StringValues $values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudAssetV1StringValues
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1Rule::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1Rule');
