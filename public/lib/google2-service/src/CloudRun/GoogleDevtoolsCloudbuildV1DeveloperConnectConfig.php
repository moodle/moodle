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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1DeveloperConnectConfig extends \Google\Model
{
  /**
   * Required. Directory, relative to the source root, in which to run the
   * build.
   *
   * @var string
   */
  public $dir;
  /**
   * Required. The Developer Connect Git repository link, formatted as
   * `projects/locations/connections/gitRepositoryLink`.
   *
   * @var string
   */
  public $gitRepositoryLink;
  /**
   * Required. The revision to fetch from the Git repository such as a branch, a
   * tag, a commit SHA, or any Git ref.
   *
   * @var string
   */
  public $revision;

  /**
   * Required. Directory, relative to the source root, in which to run the
   * build.
   *
   * @param string $dir
   */
  public function setDir($dir)
  {
    $this->dir = $dir;
  }
  /**
   * @return string
   */
  public function getDir()
  {
    return $this->dir;
  }
  /**
   * Required. The Developer Connect Git repository link, formatted as
   * `projects/locations/connections/gitRepositoryLink`.
   *
   * @param string $gitRepositoryLink
   */
  public function setGitRepositoryLink($gitRepositoryLink)
  {
    $this->gitRepositoryLink = $gitRepositoryLink;
  }
  /**
   * @return string
   */
  public function getGitRepositoryLink()
  {
    return $this->gitRepositoryLink;
  }
  /**
   * Required. The revision to fetch from the Git repository such as a branch, a
   * tag, a commit SHA, or any Git ref.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1DeveloperConnectConfig::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1DeveloperConnectConfig');
