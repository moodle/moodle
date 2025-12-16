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

class Package extends \Google\Model
{
  /**
   * Optional. Client specified annotations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * The time when the package was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The display name of the package.
   *
   * @var string
   */
  public $displayName;
  /**
   * The name of the package, for example: `projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1`. If the package ID part contains
   * slashes, the slashes are escaped.
   *
   * @var string
   */
  public $name;
  /**
   * The time when the package was last updated. This includes publishing a new
   * version of the package.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Client specified annotations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * The time when the package was created.
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
   * The display name of the package.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The name of the package, for example: `projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1`. If the package ID part contains
   * slashes, the slashes are escaped.
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
   * The time when the package was last updated. This includes publishing a new
   * version of the package.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Package::class, 'Google_Service_ArtifactRegistry_Package');
