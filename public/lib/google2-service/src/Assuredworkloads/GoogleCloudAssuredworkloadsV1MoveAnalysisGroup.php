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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1MoveAnalysisGroup extends \Google\Model
{
  protected $analysisResultType = GoogleCloudAssuredworkloadsV1MoveAnalysisResult::class;
  protected $analysisResultDataType = '';
  /**
   * Name of the analysis group.
   *
   * @var string
   */
  public $displayName;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';

  /**
   * Result of a successful analysis.
   *
   * @param GoogleCloudAssuredworkloadsV1MoveAnalysisResult $analysisResult
   */
  public function setAnalysisResult(GoogleCloudAssuredworkloadsV1MoveAnalysisResult $analysisResult)
  {
    $this->analysisResult = $analysisResult;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1MoveAnalysisResult
   */
  public function getAnalysisResult()
  {
    return $this->analysisResult;
  }
  /**
   * Name of the analysis group.
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
   * Error details for a failed analysis.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1MoveAnalysisGroup::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1MoveAnalysisGroup');
