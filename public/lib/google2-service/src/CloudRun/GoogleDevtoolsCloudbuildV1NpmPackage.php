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

class GoogleDevtoolsCloudbuildV1NpmPackage extends \Google\Model
{
  /**
   * Optional. Path to the package.json. e.g. workspace/path/to/package Only one
   * of `archive` or `package_path` can be specified.
   *
   * @var string
   */
  public $packagePath;
  /**
   * Artifact Registry repository, in the form "https://$REGION-
   * npm.pkg.dev/$PROJECT/$REPOSITORY" Npm package in the workspace specified by
   * path will be zipped and uploaded to Artifact Registry with this location as
   * a prefix.
   *
   * @var string
   */
  public $repository;

  /**
   * Optional. Path to the package.json. e.g. workspace/path/to/package Only one
   * of `archive` or `package_path` can be specified.
   *
   * @param string $packagePath
   */
  public function setPackagePath($packagePath)
  {
    $this->packagePath = $packagePath;
  }
  /**
   * @return string
   */
  public function getPackagePath()
  {
    return $this->packagePath;
  }
  /**
   * Artifact Registry repository, in the form "https://$REGION-
   * npm.pkg.dev/$PROJECT/$REPOSITORY" Npm package in the workspace specified by
   * path will be zipped and uploaded to Artifact Registry with this location as
   * a prefix.
   *
   * @param string $repository
   */
  public function setRepository($repository)
  {
    $this->repository = $repository;
  }
  /**
   * @return string
   */
  public function getRepository()
  {
    return $this->repository;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1NpmPackage::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1NpmPackage');
