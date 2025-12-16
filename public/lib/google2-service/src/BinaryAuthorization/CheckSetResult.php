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

namespace Google\Service\BinaryAuthorization;

class CheckSetResult extends \Google\Model
{
  protected $allowlistResultType = AllowlistResult::class;
  protected $allowlistResultDataType = '';
  protected $checkResultsType = CheckResults::class;
  protected $checkResultsDataType = '';
  /**
   * The name of the check set.
   *
   * @var string
   */
  public $displayName;
  /**
   * Explanation of this check set result. Only populated if no checks were
   * evaluated.
   *
   * @var string
   */
  public $explanation;
  /**
   * The index of the check set.
   *
   * @var string
   */
  public $index;
  protected $scopeType = Scope::class;
  protected $scopeDataType = '';

  /**
   * If the image was exempted by an allow_pattern in the check set, contains
   * the pattern that the image name matched.
   *
   * @param AllowlistResult $allowlistResult
   */
  public function setAllowlistResult(AllowlistResult $allowlistResult)
  {
    $this->allowlistResult = $allowlistResult;
  }
  /**
   * @return AllowlistResult
   */
  public function getAllowlistResult()
  {
    return $this->allowlistResult;
  }
  /**
   * If checks were evaluated, contains the results of evaluating each check.
   *
   * @param CheckResults $checkResults
   */
  public function setCheckResults(CheckResults $checkResults)
  {
    $this->checkResults = $checkResults;
  }
  /**
   * @return CheckResults
   */
  public function getCheckResults()
  {
    return $this->checkResults;
  }
  /**
   * The name of the check set.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Explanation of this check set result. Only populated if no checks were
   * evaluated.
   *
   * @param string $explanation
   */
  public function setExplanation($explanation)
  {
    $this->explanation = $explanation;
  }
  /**
   * @return string
   */
  public function getExplanation()
  {
    return $this->explanation;
  }
  /**
   * The index of the check set.
   *
   * @param string $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * The scope of the check set.
   *
   * @param Scope $scope
   */
  public function setScope(Scope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return Scope
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckSetResult::class, 'Google_Service_BinaryAuthorization_CheckSetResult');
