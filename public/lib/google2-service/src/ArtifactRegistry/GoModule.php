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

class GoModule extends \Google\Model
{
  /**
   * Output only. The time when the Go module is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The resource name of a Go module.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time when the Go module is updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The version of the Go module. Must be a valid canonical version as defined
   * in https://go.dev/ref/mod#glos-canonical-version.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. The time when the Go module is created.
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
   * The resource name of a Go module.
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
   * Output only. The time when the Go module is updated.
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
   * The version of the Go module. Must be a valid canonical version as defined
   * in https://go.dev/ref/mod#glos-canonical-version.
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
class_alias(GoModule::class, 'Google_Service_ArtifactRegistry_GoModule');
