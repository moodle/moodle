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

namespace Google\Service\Config;

class GitSource extends \Google\Model
{
  /**
   * Optional. Subdirectory inside the repository. Example: 'staging/my-package'
   *
   * @var string
   */
  public $directory;
  /**
   * Optional. Git reference (e.g. branch or tag).
   *
   * @var string
   */
  public $ref;
  /**
   * Optional. Repository URL. Example:
   * 'https://github.com/kubernetes/examples.git'
   *
   * @var string
   */
  public $repo;

  /**
   * Optional. Subdirectory inside the repository. Example: 'staging/my-package'
   *
   * @param string $directory
   */
  public function setDirectory($directory)
  {
    $this->directory = $directory;
  }
  /**
   * @return string
   */
  public function getDirectory()
  {
    return $this->directory;
  }
  /**
   * Optional. Git reference (e.g. branch or tag).
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
   * Optional. Repository URL. Example:
   * 'https://github.com/kubernetes/examples.git'
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
class_alias(GitSource::class, 'Google_Service_Config_GitSource');
