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

namespace Google\Service\CloudTalentSolution;

class CustomRankingInfo extends \Google\Model
{
  /**
   * Default value if the importance level isn't specified.
   */
  public const IMPORTANCE_LEVEL_IMPORTANCE_LEVEL_UNSPECIFIED = 'IMPORTANCE_LEVEL_UNSPECIFIED';
  /**
   * The given ranking expression is of None importance, existing relevance
   * score (determined by API algorithm) dominates job's final ranking position.
   */
  public const IMPORTANCE_LEVEL_NONE = 'NONE';
  /**
   * The given ranking expression is of Low importance in terms of job's final
   * ranking position compared to existing relevance score (determined by API
   * algorithm).
   */
  public const IMPORTANCE_LEVEL_LOW = 'LOW';
  /**
   * The given ranking expression is of Mild importance in terms of job's final
   * ranking position compared to existing relevance score (determined by API
   * algorithm).
   */
  public const IMPORTANCE_LEVEL_MILD = 'MILD';
  /**
   * The given ranking expression is of Medium importance in terms of job's
   * final ranking position compared to existing relevance score (determined by
   * API algorithm).
   */
  public const IMPORTANCE_LEVEL_MEDIUM = 'MEDIUM';
  /**
   * The given ranking expression is of High importance in terms of job's final
   * ranking position compared to existing relevance score (determined by API
   * algorithm).
   */
  public const IMPORTANCE_LEVEL_HIGH = 'HIGH';
  /**
   * The given ranking expression is of Extreme importance, and dominates job's
   * final ranking position with existing relevance score (determined by API
   * algorithm) ignored.
   */
  public const IMPORTANCE_LEVEL_EXTREME = 'EXTREME';
  /**
   * Required. Controls over how important the score of
   * CustomRankingInfo.ranking_expression gets applied to job's final ranking
   * position. An error is thrown if not specified.
   *
   * @var string
   */
  public $importanceLevel;
  /**
   * Required. Controls over how job documents get ranked on top of existing
   * relevance score (determined by API algorithm). A combination of the ranking
   * expression and relevance score is used to determine job's final ranking
   * position. The syntax for this expression is a subset of Google SQL syntax.
   * Supported operators are: +, -, *, /, where the left and right side of the
   * operator is either a numeric Job.custom_attributes key, integer/double
   * value or an expression that can be evaluated to a number. Parenthesis are
   * supported to adjust calculation precedence. The expression must be < 200
   * characters in length. The expression is considered invalid for a job if the
   * expression references custom attributes that are not populated on the job
   * or if the expression results in a divide by zero. If an expression is
   * invalid for a job, that job is demoted to the end of the results. Sample
   * ranking expression (year + 25) * 0.25 - (freshness / 0.5)
   *
   * @var string
   */
  public $rankingExpression;

  /**
   * Required. Controls over how important the score of
   * CustomRankingInfo.ranking_expression gets applied to job's final ranking
   * position. An error is thrown if not specified.
   *
   * Accepted values: IMPORTANCE_LEVEL_UNSPECIFIED, NONE, LOW, MILD, MEDIUM,
   * HIGH, EXTREME
   *
   * @param self::IMPORTANCE_LEVEL_* $importanceLevel
   */
  public function setImportanceLevel($importanceLevel)
  {
    $this->importanceLevel = $importanceLevel;
  }
  /**
   * @return self::IMPORTANCE_LEVEL_*
   */
  public function getImportanceLevel()
  {
    return $this->importanceLevel;
  }
  /**
   * Required. Controls over how job documents get ranked on top of existing
   * relevance score (determined by API algorithm). A combination of the ranking
   * expression and relevance score is used to determine job's final ranking
   * position. The syntax for this expression is a subset of Google SQL syntax.
   * Supported operators are: +, -, *, /, where the left and right side of the
   * operator is either a numeric Job.custom_attributes key, integer/double
   * value or an expression that can be evaluated to a number. Parenthesis are
   * supported to adjust calculation precedence. The expression must be < 200
   * characters in length. The expression is considered invalid for a job if the
   * expression references custom attributes that are not populated on the job
   * or if the expression results in a divide by zero. If an expression is
   * invalid for a job, that job is demoted to the end of the results. Sample
   * ranking expression (year + 25) * 0.25 - (freshness / 0.5)
   *
   * @param string $rankingExpression
   */
  public function setRankingExpression($rankingExpression)
  {
    $this->rankingExpression = $rankingExpression;
  }
  /**
   * @return string
   */
  public function getRankingExpression()
  {
    return $this->rankingExpression;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomRankingInfo::class, 'Google_Service_CloudTalentSolution_CustomRankingInfo');
