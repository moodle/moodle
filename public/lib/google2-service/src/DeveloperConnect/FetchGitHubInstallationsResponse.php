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

namespace Google\Service\DeveloperConnect;

class FetchGitHubInstallationsResponse extends \Google\Collection
{
  protected $collection_key = 'installations';
  protected $installationsType = Installation::class;
  protected $installationsDataType = 'array';

  /**
   * List of installations available to the OAuth user (for github.com) or all
   * the installations (for GitHub enterprise).
   *
   * @param Installation[] $installations
   */
  public function setInstallations($installations)
  {
    $this->installations = $installations;
  }
  /**
   * @return Installation[]
   */
  public function getInstallations()
  {
    return $this->installations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchGitHubInstallationsResponse::class, 'Google_Service_DeveloperConnect_FetchGitHubInstallationsResponse');
