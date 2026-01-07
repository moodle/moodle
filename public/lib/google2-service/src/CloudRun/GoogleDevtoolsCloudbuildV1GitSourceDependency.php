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

class GoogleDevtoolsCloudbuildV1GitSourceDependency extends \Google\Model
{
  /**
   * Optional. How much history should be fetched for the build (default 1, -1
   * for all history).
   *
   * @var string
   */
  public $depth;
  /**
   * Required. Where should the files be placed on the worker.
   *
   * @var string
   */
  public $destPath;
  /**
   * Optional. True if submodules should be fetched too (default false).
   *
   * @var bool
   */
  public $recurseSubmodules;
  protected $repositoryType = GoogleDevtoolsCloudbuildV1GitSourceRepository::class;
  protected $repositoryDataType = '';
  /**
   * Required. The revision that we will fetch the repo at.
   *
   * @var string
   */
  public $revision;

  /**
   * Optional. How much history should be fetched for the build (default 1, -1
   * for all history).
   *
   * @param string $depth
   */
  public function setDepth($depth)
  {
    $this->depth = $depth;
  }
  /**
   * @return string
   */
  public function getDepth()
  {
    return $this->depth;
  }
  /**
   * Required. Where should the files be placed on the worker.
   *
   * @param string $destPath
   */
  public function setDestPath($destPath)
  {
    $this->destPath = $destPath;
  }
  /**
   * @return string
   */
  public function getDestPath()
  {
    return $this->destPath;
  }
  /**
   * Optional. True if submodules should be fetched too (default false).
   *
   * @param bool $recurseSubmodules
   */
  public function setRecurseSubmodules($recurseSubmodules)
  {
    $this->recurseSubmodules = $recurseSubmodules;
  }
  /**
   * @return bool
   */
  public function getRecurseSubmodules()
  {
    return $this->recurseSubmodules;
  }
  /**
   * Required. The kind of repo (url or dev connect).
   *
   * @param GoogleDevtoolsCloudbuildV1GitSourceRepository $repository
   */
  public function setRepository(GoogleDevtoolsCloudbuildV1GitSourceRepository $repository)
  {
    $this->repository = $repository;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1GitSourceRepository
   */
  public function getRepository()
  {
    return $this->repository;
  }
  /**
   * Required. The revision that we will fetch the repo at.
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
class_alias(GoogleDevtoolsCloudbuildV1GitSourceDependency::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1GitSourceDependency');
