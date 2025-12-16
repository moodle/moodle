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

namespace Google\Service\SecureSourceManager;

class PushOption extends \Google\Model
{
  /**
   * Optional. Trigger hook for matching branches only. Specified as glob
   * pattern. If empty or *, events for all branches are reported. Examples:
   * main, {main,release*}. See https://pkg.go.dev/github.com/gobwas/glob
   * documentation.
   *
   * @var string
   */
  public $branchFilter;

  /**
   * Optional. Trigger hook for matching branches only. Specified as glob
   * pattern. If empty or *, events for all branches are reported. Examples:
   * main, {main,release*}. See https://pkg.go.dev/github.com/gobwas/glob
   * documentation.
   *
   * @param string $branchFilter
   */
  public function setBranchFilter($branchFilter)
  {
    $this->branchFilter = $branchFilter;
  }
  /**
   * @return string
   */
  public function getBranchFilter()
  {
    return $this->branchFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PushOption::class, 'Google_Service_SecureSourceManager_PushOption');
