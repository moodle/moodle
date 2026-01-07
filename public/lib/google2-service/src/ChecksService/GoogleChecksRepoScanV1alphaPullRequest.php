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

namespace Google\Service\ChecksService;

class GoogleChecksRepoScanV1alphaPullRequest extends \Google\Model
{
  /**
   * Required. For PR analysis, we compare against the most recent scan of the
   * base branch to highlight new issues.
   *
   * @var string
   */
  public $baseBranch;
  /**
   * Required. This can be supplied by the user or parsed automatically from
   * predefined CI environment variables.
   *
   * @var string
   */
  public $prNumber;

  /**
   * Required. For PR analysis, we compare against the most recent scan of the
   * base branch to highlight new issues.
   *
   * @param string $baseBranch
   */
  public function setBaseBranch($baseBranch)
  {
    $this->baseBranch = $baseBranch;
  }
  /**
   * @return string
   */
  public function getBaseBranch()
  {
    return $this->baseBranch;
  }
  /**
   * Required. This can be supplied by the user or parsed automatically from
   * predefined CI environment variables.
   *
   * @param string $prNumber
   */
  public function setPrNumber($prNumber)
  {
    $this->prNumber = $prNumber;
  }
  /**
   * @return string
   */
  public function getPrNumber()
  {
    return $this->prNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksRepoScanV1alphaPullRequest::class, 'Google_Service_ChecksService_GoogleChecksRepoScanV1alphaPullRequest');
