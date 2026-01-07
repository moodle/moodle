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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonWebpageInfo extends \Google\Collection
{
  protected $collection_key = 'conditions';
  protected $conditionsType = GoogleAdsSearchads360V0CommonWebpageConditionInfo::class;
  protected $conditionsDataType = 'array';
  /**
   * Website criteria coverage percentage. This is the computed percentage of
   * website coverage based on the website target, negative website target and
   * negative keywords in the ad group and campaign. For instance, when coverage
   * returns as 1, it indicates it has 100% coverage. This field is read-only.
   *
   * @var 
   */
  public $coveragePercentage;
  /**
   * The name of the criterion that is defined by this parameter. The name value
   * will be used for identifying, sorting and filtering criteria with this type
   * of parameters. This field is required for CREATE operations and is
   * prohibited on UPDATE operations.
   *
   * @var string
   */
  public $criterionName;

  /**
   * Conditions, or logical expressions, for webpage targeting. The list of
   * webpage targeting conditions are and-ed together when evaluated for
   * targeting. An empty list of conditions indicates all pages of the
   * campaign's website are targeted. This field is required for CREATE
   * operations and is prohibited on UPDATE operations.
   *
   * @param GoogleAdsSearchads360V0CommonWebpageConditionInfo[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonWebpageConditionInfo[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  public function setCoveragePercentage($coveragePercentage)
  {
    $this->coveragePercentage = $coveragePercentage;
  }
  public function getCoveragePercentage()
  {
    return $this->coveragePercentage;
  }
  /**
   * The name of the criterion that is defined by this parameter. The name value
   * will be used for identifying, sorting and filtering criteria with this type
   * of parameters. This field is required for CREATE operations and is
   * prohibited on UPDATE operations.
   *
   * @param string $criterionName
   */
  public function setCriterionName($criterionName)
  {
    $this->criterionName = $criterionName;
  }
  /**
   * @return string
   */
  public function getCriterionName()
  {
    return $this->criterionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonWebpageInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonWebpageInfo');
