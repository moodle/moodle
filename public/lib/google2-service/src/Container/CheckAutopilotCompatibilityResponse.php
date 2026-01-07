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

namespace Google\Service\Container;

class CheckAutopilotCompatibilityResponse extends \Google\Collection
{
  protected $collection_key = 'issues';
  protected $issuesType = AutopilotCompatibilityIssue::class;
  protected $issuesDataType = 'array';
  /**
   * The summary of the autopilot compatibility response.
   *
   * @var string
   */
  public $summary;

  /**
   * The list of issues for the given operation.
   *
   * @param AutopilotCompatibilityIssue[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return AutopilotCompatibilityIssue[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * The summary of the autopilot compatibility response.
   *
   * @param string $summary
   */
  public function setSummary($summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return string
   */
  public function getSummary()
  {
    return $this->summary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckAutopilotCompatibilityResponse::class, 'Google_Service_Container_CheckAutopilotCompatibilityResponse');
