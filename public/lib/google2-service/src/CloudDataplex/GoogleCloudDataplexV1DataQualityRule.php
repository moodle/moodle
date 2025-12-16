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

class GoogleCloudDataplexV1DataQualityRule extends \Google\Model
{
  /**
   * Optional. The unnested column which this rule is evaluated against.
   *
   * @var string
   */
  public $column;
  /**
   * Optional. Description of the rule. The maximum length is 1,024 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The dimension a rule belongs to. Results are also aggregated at
   * the dimension level. Custom dimension name is supported with all uppercase
   * letters and maximum length of 30 characters.
   *
   * @var string
   */
  public $dimension;
  /**
   * Optional. Rows with null values will automatically fail a rule, unless
   * ignore_null is true. In that case, such null rows are trivially considered
   * passing.This field is only valid for the following type of rules:
   * RangeExpectation RegexExpectation SetExpectation UniquenessExpectation
   *
   * @var bool
   */
  public $ignoreNull;
  /**
   * Optional. A mutable name for the rule. The name must contain only letters
   * (a-z, A-Z), numbers (0-9), or hyphens (-). The maximum length is 63
   * characters. Must start with a letter. Must end with a number or a letter.
   *
   * @var string
   */
  public $name;
  protected $nonNullExpectationType = GoogleCloudDataplexV1DataQualityRuleNonNullExpectation::class;
  protected $nonNullExpectationDataType = '';
  protected $rangeExpectationType = GoogleCloudDataplexV1DataQualityRuleRangeExpectation::class;
  protected $rangeExpectationDataType = '';
  protected $regexExpectationType = GoogleCloudDataplexV1DataQualityRuleRegexExpectation::class;
  protected $regexExpectationDataType = '';
  protected $rowConditionExpectationType = GoogleCloudDataplexV1DataQualityRuleRowConditionExpectation::class;
  protected $rowConditionExpectationDataType = '';
  protected $setExpectationType = GoogleCloudDataplexV1DataQualityRuleSetExpectation::class;
  protected $setExpectationDataType = '';
  protected $sqlAssertionType = GoogleCloudDataplexV1DataQualityRuleSqlAssertion::class;
  protected $sqlAssertionDataType = '';
  protected $statisticRangeExpectationType = GoogleCloudDataplexV1DataQualityRuleStatisticRangeExpectation::class;
  protected $statisticRangeExpectationDataType = '';
  /**
   * Optional. Whether the Rule is active or suspended. Default is false.
   *
   * @var bool
   */
  public $suspended;
  protected $tableConditionExpectationType = GoogleCloudDataplexV1DataQualityRuleTableConditionExpectation::class;
  protected $tableConditionExpectationDataType = '';
  /**
   * Optional. The minimum ratio of passing_rows / total_rows required to pass
   * this rule, with a range of 0.0, 1.0.0 indicates default value (i.e.
   * 1.0).This field is only valid for row-level type rules.
   *
   * @var 
   */
  public $threshold;
  protected $uniquenessExpectationType = GoogleCloudDataplexV1DataQualityRuleUniquenessExpectation::class;
  protected $uniquenessExpectationDataType = '';

  /**
   * Optional. The unnested column which this rule is evaluated against.
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
   * Optional. Description of the rule. The maximum length is 1,024 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The dimension a rule belongs to. Results are also aggregated at
   * the dimension level. Custom dimension name is supported with all uppercase
   * letters and maximum length of 30 characters.
   *
   * @param string $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return string
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * Optional. Rows with null values will automatically fail a rule, unless
   * ignore_null is true. In that case, such null rows are trivially considered
   * passing.This field is only valid for the following type of rules:
   * RangeExpectation RegexExpectation SetExpectation UniquenessExpectation
   *
   * @param bool $ignoreNull
   */
  public function setIgnoreNull($ignoreNull)
  {
    $this->ignoreNull = $ignoreNull;
  }
  /**
   * @return bool
   */
  public function getIgnoreNull()
  {
    return $this->ignoreNull;
  }
  /**
   * Optional. A mutable name for the rule. The name must contain only letters
   * (a-z, A-Z), numbers (0-9), or hyphens (-). The maximum length is 63
   * characters. Must start with a letter. Must end with a number or a letter.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Row-level rule which evaluates whether each column value is null.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleNonNullExpectation $nonNullExpectation
   */
  public function setNonNullExpectation(GoogleCloudDataplexV1DataQualityRuleNonNullExpectation $nonNullExpectation)
  {
    $this->nonNullExpectation = $nonNullExpectation;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleNonNullExpectation
   */
  public function getNonNullExpectation()
  {
    return $this->nonNullExpectation;
  }
  /**
   * Row-level rule which evaluates whether each column value lies between a
   * specified range.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleRangeExpectation $rangeExpectation
   */
  public function setRangeExpectation(GoogleCloudDataplexV1DataQualityRuleRangeExpectation $rangeExpectation)
  {
    $this->rangeExpectation = $rangeExpectation;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleRangeExpectation
   */
  public function getRangeExpectation()
  {
    return $this->rangeExpectation;
  }
  /**
   * Row-level rule which evaluates whether each column value matches a
   * specified regex.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleRegexExpectation $regexExpectation
   */
  public function setRegexExpectation(GoogleCloudDataplexV1DataQualityRuleRegexExpectation $regexExpectation)
  {
    $this->regexExpectation = $regexExpectation;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleRegexExpectation
   */
  public function getRegexExpectation()
  {
    return $this->regexExpectation;
  }
  /**
   * Row-level rule which evaluates whether each row in a table passes the
   * specified condition.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleRowConditionExpectation $rowConditionExpectation
   */
  public function setRowConditionExpectation(GoogleCloudDataplexV1DataQualityRuleRowConditionExpectation $rowConditionExpectation)
  {
    $this->rowConditionExpectation = $rowConditionExpectation;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleRowConditionExpectation
   */
  public function getRowConditionExpectation()
  {
    return $this->rowConditionExpectation;
  }
  /**
   * Row-level rule which evaluates whether each column value is contained by a
   * specified set.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleSetExpectation $setExpectation
   */
  public function setSetExpectation(GoogleCloudDataplexV1DataQualityRuleSetExpectation $setExpectation)
  {
    $this->setExpectation = $setExpectation;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleSetExpectation
   */
  public function getSetExpectation()
  {
    return $this->setExpectation;
  }
  /**
   * Aggregate rule which evaluates the number of rows returned for the provided
   * statement. If any rows are returned, this rule fails.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleSqlAssertion $sqlAssertion
   */
  public function setSqlAssertion(GoogleCloudDataplexV1DataQualityRuleSqlAssertion $sqlAssertion)
  {
    $this->sqlAssertion = $sqlAssertion;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleSqlAssertion
   */
  public function getSqlAssertion()
  {
    return $this->sqlAssertion;
  }
  /**
   * Aggregate rule which evaluates whether the column aggregate statistic lies
   * between a specified range.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleStatisticRangeExpectation $statisticRangeExpectation
   */
  public function setStatisticRangeExpectation(GoogleCloudDataplexV1DataQualityRuleStatisticRangeExpectation $statisticRangeExpectation)
  {
    $this->statisticRangeExpectation = $statisticRangeExpectation;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleStatisticRangeExpectation
   */
  public function getStatisticRangeExpectation()
  {
    return $this->statisticRangeExpectation;
  }
  /**
   * Optional. Whether the Rule is active or suspended. Default is false.
   *
   * @param bool $suspended
   */
  public function setSuspended($suspended)
  {
    $this->suspended = $suspended;
  }
  /**
   * @return bool
   */
  public function getSuspended()
  {
    return $this->suspended;
  }
  /**
   * Aggregate rule which evaluates whether the provided expression is true for
   * a table.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleTableConditionExpectation $tableConditionExpectation
   */
  public function setTableConditionExpectation(GoogleCloudDataplexV1DataQualityRuleTableConditionExpectation $tableConditionExpectation)
  {
    $this->tableConditionExpectation = $tableConditionExpectation;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleTableConditionExpectation
   */
  public function getTableConditionExpectation()
  {
    return $this->tableConditionExpectation;
  }
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  public function getThreshold()
  {
    return $this->threshold;
  }
  /**
   * Row-level rule which evaluates whether each column value is unique.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleUniquenessExpectation $uniquenessExpectation
   */
  public function setUniquenessExpectation(GoogleCloudDataplexV1DataQualityRuleUniquenessExpectation $uniquenessExpectation)
  {
    $this->uniquenessExpectation = $uniquenessExpectation;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleUniquenessExpectation
   */
  public function getUniquenessExpectation()
  {
    return $this->uniquenessExpectation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualityRule::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualityRule');
