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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataQualityRuleResult extends \Google\Model
{
  /**
   * Output only. The number of rows returned by the SQL statement in a SQL
   * assertion rule.This field is only valid for SQL assertion rules.
   *
   * @var string
   */
  public $assertionRowCount;
  /**
   * Output only. The number of rows a rule was evaluated against.This field is
   * only valid for row-level type rules.Evaluated count can be configured to
   * either include all rows (default) - with null rows automatically failing
   * rule evaluation, or exclude null rows from the evaluated_count, by setting
   * ignore_nulls = true.This field is not set for rule SqlAssertion.
   *
   * @var string
   */
  public $evaluatedCount;
  /**
   * Output only. The query to find rows that did not pass this rule.This field
   * is only valid for row-level type rules.
   *
   * @var string
   */
  public $failingRowsQuery;
  /**
   * Output only. The number of rows with null values in the specified column.
   *
   * @var string
   */
  public $nullCount;
  /**
   * Output only. The ratio of passed_count / evaluated_count.This field is only
   * valid for row-level type rules.
   *
   * @var 
   */
  public $passRatio;
  /**
   * Output only. Whether the rule passed or failed.
   *
   * @var bool
   */
  public $passed;
  /**
   * Output only. The number of rows which passed a rule evaluation.This field
   * is only valid for row-level type rules.This field is not set for rule
   * SqlAssertion.
   *
   * @var string
   */
  public $passedCount;
  protected $ruleType = GoogleCloudDataplexV1DataQualityRule::class;
  protected $ruleDataType = '';

  /**
   * Output only. The number of rows returned by the SQL statement in a SQL
   * assertion rule.This field is only valid for SQL assertion rules.
   *
   * @param string $assertionRowCount
   */
  public function setAssertionRowCount($assertionRowCount)
  {
    $this->assertionRowCount = $assertionRowCount;
  }
  /**
   * @return string
   */
  public function getAssertionRowCount()
  {
    return $this->assertionRowCount;
  }
  /**
   * Output only. The number of rows a rule was evaluated against.This field is
   * only valid for row-level type rules.Evaluated count can be configured to
   * either include all rows (default) - with null rows automatically failing
   * rule evaluation, or exclude null rows from the evaluated_count, by setting
   * ignore_nulls = true.This field is not set for rule SqlAssertion.
   *
   * @param string $evaluatedCount
   */
  public function setEvaluatedCount($evaluatedCount)
  {
    $this->evaluatedCount = $evaluatedCount;
  }
  /**
   * @return string
   */
  public function getEvaluatedCount()
  {
    return $this->evaluatedCount;
  }
  /**
   * Output only. The query to find rows that did not pass this rule.This field
   * is only valid for row-level type rules.
   *
   * @param string $failingRowsQuery
   */
  public function setFailingRowsQuery($failingRowsQuery)
  {
    $this->failingRowsQuery = $failingRowsQuery;
  }
  /**
   * @return string
   */
  public function getFailingRowsQuery()
  {
    return $this->failingRowsQuery;
  }
  /**
   * Output only. The number of rows with null values in the specified column.
   *
   * @param string $nullCount
   */
  public function setNullCount($nullCount)
  {
    $this->nullCount = $nullCount;
  }
  /**
   * @return string
   */
  public function getNullCount()
  {
    return $this->nullCount;
  }
  public function setPassRatio($passRatio)
  {
    $this->passRatio = $passRatio;
  }
  public function getPassRatio()
  {
    return $this->passRatio;
  }
  /**
   * Output only. Whether the rule passed or failed.
   *
   * @param bool $passed
   */
  public function setPassed($passed)
  {
    $this->passed = $passed;
  }
  /**
   * @return bool
   */
  public function getPassed()
  {
    return $this->passed;
  }
  /**
   * Output only. The number of rows which passed a rule evaluation.This field
   * is only valid for row-level type rules.This field is not set for rule
   * SqlAssertion.
   *
   * @param string $passedCount
   */
  public function setPassedCount($passedCount)
  {
    $this->passedCount = $passedCount;
  }
  /**
   * @return string
   */
  public function getPassedCount()
  {
    return $this->passedCount;
  }
  /**
   * Output only. The rule specified in the DataQualitySpec, as is.
   *
   * @param GoogleCloudDataplexV1DataQualityRule $rule
   */
  public function setRule(GoogleCloudDataplexV1DataQualityRule $rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRule
   */
  public function getRule()
  {
    return $this->rule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualityRuleResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualityRuleResult');
