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

namespace Google\Service\CloudDeploy;

class SkaffoldGitSource extends \Google\Model
{
  /**
   * Optional. Relative path from the repository root to the Skaffold file.
   *
   * @var string
   */
  public $path;
  /**
   * Optional. Git branch or tag to use when cloning the repository.
   *
   * @var string
   */
  public $ref;
  /**
   * Required. Git repository the package should be cloned from.
   *
   * @var string
   */
  public $repo;

  /**
   * Optional. Relative path from the repository root to the Skaffold file.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Optional. Git branch or tag to use when cloning the repository.
   *
   * @param string $ref
   */
  public function setRef($ref)
  {
    $this->ref = $ref;
  }
  /**
   * @return string
   */
  public function getRef()
  {
    return $this->ref;
  }
  /**
   * Required. Git repository the package should be cloned from.
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
class_alias(SkaffoldGitSource::class, 'Google_Service_CloudDeploy_SkaffoldGitSource');
