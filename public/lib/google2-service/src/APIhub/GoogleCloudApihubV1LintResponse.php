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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1LintResponse extends \Google\Collection
{
  /**
   * Linter type unspecified.
   */
  public const LINTER_LINTER_UNSPECIFIED = 'LINTER_UNSPECIFIED';
  /**
   * Linter type spectral.
   */
  public const LINTER_SPECTRAL = 'SPECTRAL';
  /**
   * Linter type other.
   */
  public const LINTER_OTHER = 'OTHER';
  /**
   * Lint state unspecified.
   */
  public const STATE_LINT_STATE_UNSPECIFIED = 'LINT_STATE_UNSPECIFIED';
  /**
   * Linting was completed successfully.
   */
  public const STATE_LINT_STATE_SUCCESS = 'LINT_STATE_SUCCESS';
  /**
   * Linting encountered errors.
   */
  public const STATE_LINT_STATE_ERROR = 'LINT_STATE_ERROR';
  protected $collection_key = 'summary';
  /**
   * Required. Timestamp when the linting response was generated.
   *
   * @var string
   */
  public $createTime;
  protected $issuesType = GoogleCloudApihubV1Issue::class;
  protected $issuesDataType = 'array';
  /**
   * Required. Name of the linter used.
   *
   * @var string
   */
  public $linter;
  /**
   * Required. Name of the linting application.
   *
   * @var string
   */
  public $source;
  /**
   * Required. Lint state represents success or failure for linting.
   *
   * @var string
   */
  public $state;
  protected $summaryType = GoogleCloudApihubV1SummaryEntry::class;
  protected $summaryDataType = 'array';

  /**
   * Required. Timestamp when the linting response was generated.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Array of issues found in the analyzed document.
   *
   * @param GoogleCloudApihubV1Issue[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return GoogleCloudApihubV1Issue[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * Required. Name of the linter used.
   *
   * Accepted values: LINTER_UNSPECIFIED, SPECTRAL, OTHER
   *
   * @param self::LINTER_* $linter
   */
  public function setLinter($linter)
  {
    $this->linter = $linter;
  }
  /**
   * @return self::LINTER_*
   */
  public function getLinter()
  {
    return $this->linter;
  }
  /**
   * Required. Name of the linting application.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Required. Lint state represents success or failure for linting.
   *
   * Accepted values: LINT_STATE_UNSPECIFIED, LINT_STATE_SUCCESS,
   * LINT_STATE_ERROR
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Summary of all issue types and counts for each severity level.
   *
   * @param GoogleCloudApihubV1SummaryEntry[] $summary
   */
  public function setSummary($summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return GoogleCloudApihubV1SummaryEntry[]
   */
  public function getSummary()
  {
    return $this->summary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1LintResponse::class, 'Google_Service_APIhub_GoogleCloudApihubV1LintResponse');
