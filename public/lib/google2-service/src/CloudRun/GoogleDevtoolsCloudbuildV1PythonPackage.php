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

class GoogleDevtoolsCloudbuildV1PythonPackage extends \Google\Collection
{
  protected $collection_key = 'paths';
  /**
   * Path globs used to match files in the build's workspace. For Python/ Twine,
   * this is usually `dist`, and sometimes additionally an `.asc` file.
   *
   * @var string[]
   */
  public $paths;
  /**
   * Artifact Registry repository, in the form "https://$REGION-
   * python.pkg.dev/$PROJECT/$REPOSITORY" Files in the workspace matching any
   * path pattern will be uploaded to Artifact Registry with this location as a
   * prefix.
   *
   * @var string
   */
  public $repository;

  /**
   * Path globs used to match files in the build's workspace. For Python/ Twine,
   * this is usually `dist`, and sometimes additionally an `.asc` file.
   *
   * @param string[] $paths
   */
  public function setPaths($paths)
  {
    $this->paths = $paths;
  }
  /**
   * @return string[]
   */
  public function getPaths()
  {
    return $this->paths;
  }
  /**
   * Artifact Registry repository, in the form "https://$REGION-
   * python.pkg.dev/$PROJECT/$REPOSITORY" Files in the workspace matching any
   * path pattern will be uploaded to Artifact Registry with this location as a
   * prefix.
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
class_alias(GoogleDevtoolsCloudbuildV1PythonPackage::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1PythonPackage');
