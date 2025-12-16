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

namespace Google\Service\ContainerAnalysis;

class ContaineranalysisGoogleDevtoolsCloudbuildV1Volume extends \Google\Model
{
  /**
   * Name of the volume to mount. Volume names must be unique per build step and
   * must be valid names for Docker volumes. Each named volume must be used by
   * at least two build steps.
   *
   * @var string
   */
  public $name;
  /**
   * Path at which to mount the volume. Paths must be absolute and cannot
   * conflict with other volume paths on the same build step or with certain
   * reserved volume paths.
   *
   * @var string
   */
  public $path;

  /**
   * Name of the volume to mount. Volume names must be unique per build step and
   * must be valid names for Docker volumes. Each named volume must be used by
   * at least two build steps.
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
   * Path at which to mount the volume. Paths must be absolute and cannot
   * conflict with other volume paths on the same build step or with certain
   * reserved volume paths.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1Volume::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1Volume');
