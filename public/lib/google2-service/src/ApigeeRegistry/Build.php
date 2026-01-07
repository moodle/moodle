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

namespace Google\Service\ApigeeRegistry;

class Build extends \Google\Model
{
  /**
   * Output only. Commit ID of the latest commit in the build.
   *
   * @var string
   */
  public $commitId;
  /**
   * Output only. Commit time of the latest commit in the build.
   *
   * @var string
   */
  public $commitTime;
  /**
   * Output only. Path of the open source repository:
   * github.com/apigee/registry.
   *
   * @var string
   */
  public $repo;

  /**
   * Output only. Commit ID of the latest commit in the build.
   *
   * @param string $commitId
   */
  public function setCommitId($commitId)
  {
    $this->commitId = $commitId;
  }
  /**
   * @return string
   */
  public function getCommitId()
  {
    return $this->commitId;
  }
  /**
   * Output only. Commit time of the latest commit in the build.
   *
   * @param string $commitTime
   */
  public function setCommitTime($commitTime)
  {
    $this->commitTime = $commitTime;
  }
  /**
   * @return string
   */
  public function getCommitTime()
  {
    return $this->commitTime;
  }
  /**
   * Output only. Path of the open source repository:
   * github.com/apigee/registry.
   *
   * @param string $repo
   */
  public function setRepo($repo)
  {
    $this->repo = $repo;
  }
  /**
   * @return string
   */
  public function getRepo()
  {
    return $this->repo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Build::class, 'Google_Service_ApigeeRegistry_Build');
