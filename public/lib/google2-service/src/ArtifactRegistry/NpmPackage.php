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

namespace Google\Service\ArtifactRegistry;

class NpmPackage extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * Output only. Time the package was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. registry_location, project_id, repository_name and npm_package
   * forms a unique package For example, "projects/test-project/locations/us-
   * west4/repositories/test-repo/npmPackages/ npm_test:1.0.0", where "us-west4"
   * is the registry_location, "test-project" is the project_id, "test-repo" is
   * the repository_name and npm_test:1.0.0" is the npm package.
   *
   * @var string
   */
  public $name;
  /**
   * Package for the artifact.
   *
   * @var string
   */
  public $packageName;
  /**
   * Tags attached to this package.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Output only. Time the package was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Version of this package.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Time the package was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. registry_location, project_id, repository_name and npm_package
   * forms a unique package For example, "projects/test-project/locations/us-
   * west4/repositories/test-repo/npmPackages/ npm_test:1.0.0", where "us-west4"
   * is the registry_location, "test-project" is the project_id, "test-repo" is
   * the repository_name and npm_test:1.0.0" is the npm package.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Package for the artifact.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * Tags attached to this package.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Output only. Time the package was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Version of this package.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NpmPackage::class, 'Google_Service_ArtifactRegistry_NpmPackage');
