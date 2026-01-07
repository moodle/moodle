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

class IamPolicyAnalysis extends \Google\Collection
{
  protected $collection_key = 'nonCriticalErrors';
  protected $analysisQueryType = IamPolicyAnalysisQuery::class;
  protected $analysisQueryDataType = '';
  protected $analysisResultsType = IamPolicyAnalysisResult::class;
  protected $analysisResultsDataType = 'array';
  /**
   * Represents whether all entries in the analysis_results have been fully
   * explored to answer the query.
   *
   * @var bool
   */
  public $fullyExplored;
  protected $nonCriticalErrorsType = IamPolicyAnalysisState::class;
  protected $nonCriticalErrorsDataType = 'array';

  /**
   * The analysis query.
   *
   * @param IamPolicyAnalysisQuery $analysisQuery
   */
  public function setAnalysisQuery(IamPolicyAnalysisQuery $analysisQuery)
  {
    $this->analysisQuery = $analysisQuery;
  }
  /**
   * @return IamPolicyAnalysisQuery
   */
  public function getAnalysisQuery()
  {
    return $this->analysisQuery;
  }
  /**
   * A list of IamPolicyAnalysisResult that matches the analysis query, or empty
   * if no result is found.
   *
   * @param IamPolicyAnalysisResult[] $analysisResults
   */
  public function setAnalysisResults($analysisResults)
  {
    $this->analysisResults = $analysisResults;
  }
  /**
   * @return IamPolicyAnalysisResult[]
   */
  public function getAnalysisResults()
  {
    return $this->analysisResults;
  }
  /**
   * Represents whether all entries in the analysis_results have been fully
   * explored to answer the query.
   *
   * @param bool $fullyExplored
   */
  public function setFullyExplored($fullyExplored)
  {
    $this->fullyExplored = $fullyExplored;
  }
  /**
   * @return bool
   */
  public function getFullyExplored()
  {
    return $this->fullyExplored;
  }
  /**
   * A list of non-critical errors happened during the query handling.
   *
   * @param IamPolicyAnalysisState[] $nonCriticalErrors
   */
  public function setNonCriticalErrors($nonCriticalErrors)
  {
    $this->nonCriticalErrors = $nonCriticalErrors;
  }
  /**
   * @return IamPolicyAnalysisState[]
   */
  public function getNonCriticalErrors()
  {
    return $this->nonCriticalErrors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IamPolicyAnalysis::class, 'Google_Service_CloudAsset_IamPolicyAnalysis');
