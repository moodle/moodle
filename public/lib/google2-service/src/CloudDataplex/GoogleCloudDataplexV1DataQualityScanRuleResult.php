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

class GoogleCloudDataplexV1DataQualityScanRuleResult extends \Google\Model
{
  /**
   * An unspecified evaluation type.
   */
  public const EVALUTION_TYPE_EVALUATION_TYPE_UNSPECIFIED = 'EVALUATION_TYPE_UNSPECIFIED';
  /**
   * The rule evaluation is done at per row level.
   */
  public const EVALUTION_TYPE_PER_ROW = 'PER_ROW';
  /**
   * The rule evaluation is done for an aggregate of rows.
   */
  public const EVALUTION_TYPE_AGGREGATE = 'AGGREGATE';
  /**
   * An unspecified result.
   */
  public const RESULT_RESULT_UNSPECIFIED = 'RESULT_UNSPECIFIED';
  /**
   * The data quality rule passed.
   */
  public const RESULT_PASSED = 'PASSED';
  /**
   * The data quality rule failed.
   */
  public const RESULT_FAILED = 'FAILED';
  /**
   * An unspecified rule type.
   */
  public const RULE_TYPE_RULE_TYPE_UNSPECIFIED = 'RULE_TYPE_UNSPECIFIED';
  /**
   * See DataQualityRule.NonNullExpectation.
   */
  public const RULE_TYPE_NON_NULL_EXPECTATION = 'NON_NULL_EXPECTATION';
  /**
   * See DataQualityRule.RangeExpectation.
   */
  public const RULE_TYPE_RANGE_EXPECTATION = 'RANGE_EXPECTATION';
  /**
   * See DataQualityRule.RegexExpectation.
   */
  public const RULE_TYPE_REGEX_EXPECTATION = 'REGEX_EXPECTATION';
  /**
   * See DataQualityRule.RowConditionExpectation.
   */
  public const RULE_TYPE_ROW_CONDITION_EXPECTATION = 'ROW_CONDITION_EXPECTATION';
  /**
   * See DataQualityRule.SetExpectation.
   */
  public const RULE_TYPE_SET_EXPECTATION = 'SET_EXPECTATION';
  /**
   * See DataQualityRule.StatisticRangeExpectation.
   */
  public const RULE_TYPE_STATISTIC_RANGE_EXPECTATION = 'STATISTIC_RANGE_EXPECTATION';
  /**
   * See DataQualityRule.TableConditionExpectation.
   */
  public const RULE_TYPE_TABLE_CONDITION_EXPECTATION = 'TABLE_CONDITION_EXPECTATION';
  /**
   * See DataQualityRule.UniquenessExpectation.
   */
  public const RULE_TYPE_UNIQUENESS_EXPECTATION = 'UNIQUENESS_EXPECTATION';
  /**
   * See DataQualityRule.SqlAssertion.
   */
  public const RULE_TYPE_SQL_ASSERTION = 'SQL_ASSERTION';
  /**
   * The number of rows returned by the SQL statement in a SQL assertion rule.
   * This field is only valid for SQL assertion rules.
   *
   * @var string
   */
  public $assertionRowCount;
  /**
   * The column which this rule is evaluated against.
   *
   * @var string
   */
  public $column;
  /**
   * The data source of the data scan (e.g. BigQuery table name).
   *
   * @var string
   */
  public $dataSource;
  /**
   * The number of rows evaluated against the data quality rule. This field is
   * only valid for rules of PER_ROW evaluation type.
   *
   * @var string
   */
  public $evaluatedRowCount;
  /**
   * The evaluation type of the data quality rule.
   *
   * @var string
   */
  public $evalutionType;
  /**
   * Identifier of the specific data scan job this log entry is for.
   *
   * @var string
   */
  public $jobId;
  /**
   * The number of rows with null values in the specified column.
   *
   * @var string
   */
  public $nullRowCount;
  /**
   * The number of rows which passed a rule evaluation. This field is only valid
   * for rules of PER_ROW evaluation type.
   *
   * @var string
   */
  public $passedRowCount;
  /**
   * The result of the data quality rule.
   *
   * @var string
   */
  public $result;
  /**
   * The dimension of the data quality rule.
   *
   * @var string
   */
  public $ruleDimension;
  /**
   * The name of the data quality rule.
   *
   * @var string
   */
  public $ruleName;
  /**
   * The type of the data quality rule.
   *
   * @var string
   */
  public $ruleType;
  /**
   * The passing threshold (0.0, 100.0) of the data quality rule.
   *
   * @var 
   */
  public $thresholdPercent;

  /**
   * The number of rows returned by the SQL statement in a SQL assertion rule.
   * This field is only valid for SQL assertion rules.
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
   * The column which this rule is evaluated against.
   *
   * @param string $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return string
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * The data source of the data scan (e.g. BigQuery table name).
   *
   * @param string $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return string
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * The number of rows evaluated against the data quality rule. This field is
   * only valid for rules of PER_ROW evaluation type.
   *
   * @param string $evaluatedRowCount
   */
  public function setEvaluatedRowCount($evaluatedRowCount)
  {
    $this->evaluatedRowCount = $evaluatedRowCount;
  }
  /**
   * @return string
   */
  public function getEvaluatedRowCount()
  {
    return $this->evaluatedRowCount;
  }
  /**
   * The evaluation type of the data quality rule.
   *
   * Accepted values: EVALUATION_TYPE_UNSPECIFIED, PER_ROW, AGGREGATE
   *
   * @param self::EVALUTION_TYPE_* $evalutionType
   */
  public function setEvalutionType($evalutionType)
  {
    $this->evalutionType = $evalutionType;
  }
  /**
   * @return self::EVALUTION_TYPE_*
   */
  public function getEvalutionType()
  {
    return $this->evalutionType;
  }
  /**
   * Identifier of the specific data scan job this log entry is for.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * The number of rows with null values in the specified column.
   *
   * @param string $nullRowCount
   */
  public function setNullRowCount($nullRowCount)
  {
    $this->nullRowCount = $nullRowCount;
  }
  /**
   * @return string
   */
  public function getNullRowCount()
  {
    return $this->nullRowCount;
  }
  /**
   * The number of rows which passed a rule evaluation. This field is only valid
   * for rules of PER_ROW evaluation type.
   *
   * @param string $passedRowCount
   */
  public function setPassedRowCount($passedRowCount)
  {
    $this->passedRowCount = $passedRowCount;
  }
  /**
   * @return string
   */
  public function getPassedRowCount()
  {
    return $this->passedRowCount;
  }
  /**
   * The result of the data quality rule.
   *
   * Accepted values: RESULT_UNSPECIFIED, PASSED, FAILED
   *
   * @param self::RESULT_* $result
   */
  public function setResult($result)
  {
    $this->result = $result;
  }
  /**
   * @return self::RESULT_*
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * The dimension of the data quality rule.
   *
   * @param string $ruleDimension
   */
  public function setRuleDimension($ruleDimension)
  {
    $this->ruleDimension = $ruleDimension;
  }
  /**
   * @return string
   */
  public function getRuleDimension()
  {
    return $this->ruleDimension;
  }
  /**
   * The name of the data quality rule.
   *
   * @param string $ruleName
   */
  public function setRuleName($ruleName)
  {
    $this->ruleName = $ruleName;
  }
  /**
   * @return string
   */
  public function getRuleName()
  {
    return $this->ruleName;
  }
  /**
   * The type of the data quality rule.
   *
   * Accepted values: RULE_TYPE_UNSPECIFIED, NON_NULL_EXPECTATION,
   * RANGE_EXPECTATION, REGEX_EXPECTATION, ROW_CONDITION_EXPECTATION,
   * SET_EXPECTATION, STATISTIC_RANGE_EXPECTATION, TABLE_CONDITION_EXPECTATION,
   * UNIQUENESS_EXPECTATION, SQL_ASSERTION
   *
   * @param self::RULE_TYPE_* $ruleType
   */
  public function setRuleType($ruleType)
  {
    $this->ruleType = $ruleType;
  }
  /**
   * @return self::RULE_TYPE_*
   */
  public function getRuleType()
  {
    return $this->ruleType;
  }
  public function setThresholdPercent($thresholdPercent)
  {
    $this->thresholdPercent = $thresholdPercent;
  }
  public function getThresholdPercent()
  {
    return $this->thresholdPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualityScanRuleResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualityScanRuleResult');
