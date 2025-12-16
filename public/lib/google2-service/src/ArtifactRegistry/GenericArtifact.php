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

class GenericArtifact extends \Google\Model
{
  /**
   * Output only. The time when the Generic module is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Resource name of the generic artifact. project, location, repository,
   * package_id and version_id create a unique generic artifact. i.e.
   * "projects/test-project/locations/us-west4/repositories/test-repo/
   * genericArtifacts/package_id:version_id"
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time when the Generic module is updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The version of the generic artifact.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. The time when the Generic module is created.
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
   * Resource name of the generic artifact. project, location, repository,
   * package_id and version_id create a unique generic artifact. i.e.
   * "projects/test-project/locations/us-west4/repositories/test-repo/
   * genericArtifacts/package_id:version_id"
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
   * Output only. The time when the Generic module is updated.
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
   * The version of the generic artifact.
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
class_alias(GenericArtifact::class, 'Google_Service_ArtifactRegistry_GenericArtifact');
