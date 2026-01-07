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

namespace Google\Service\CloudWorkstations;

class Container extends \Google\Collection
{
  protected $collection_key = 'command';
  /**
   * Optional. Arguments passed to the entrypoint.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. If set, overrides the default ENTRYPOINT specified by the image.
   *
   * @var string[]
   */
  public $command;
  /**
   * Optional. Environment variables passed to the container's entrypoint.
   *
   * @var string[]
   */
  public $env;
  /**
   * Optional. A Docker container image that defines a custom environment. Cloud
   * Workstations provides a number of [preconfigured
   * images](https://cloud.google.com/workstations/docs/preconfigured-base-
   * images), but you can create your own [custom container
   * images](https://cloud.google.com/workstations/docs/custom-container-
   * images). If using a private image, the `host.gceInstance.serviceAccount`
   * field must be specified in the workstation configuration. If using a custom
   * container image, the service account must have [Artifact Registry
   * Reader](https://cloud.google.com/artifact-registry/docs/access-
   * control#roles) permission to pull the specified image. Otherwise, the image
   * must be publicly accessible.
   *
   * @var string
   */
  public $image;
  /**
   * Optional. If set, overrides the USER specified in the image with the given
   * uid.
   *
   * @var int
   */
  public $runAsUser;
  /**
   * Optional. If set, overrides the default DIR specified by the image.
   *
   * @var string
   */
  public $workingDir;

  /**
   * Optional. Arguments passed to the entrypoint.
   *
   * @param string[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * Optional. If set, overrides the default ENTRYPOINT specified by the image.
   *
   * @param string[] $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string[]
   */
  public function getCommand()
  {
    return $this->command;
  }
  /**
   * Optional. Environment variables passed to the container's entrypoint.
   *
   * @param string[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return string[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Optional. A Docker container image that defines a custom environment. Cloud
   * Workstations provides a number of [preconfigured
   * images](https://cloud.google.com/workstations/docs/preconfigured-base-
   * images), but you can create your own [custom container
   * images](https://cloud.google.com/workstations/docs/custom-container-
   * images). If using a private image, the `host.gceInstance.serviceAccount`
   * field must be specified in the workstation configuration. If using a custom
   * container image, the service account must have [Artifact Registry
   * Reader](https://cloud.google.com/artifact-registry/docs/access-
   * control#roles) permission to pull the specified image. Otherwise, the image
   * must be publicly accessible.
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Optional. If set, overrides the USER specified in the image with the given
   * uid.
   *
   * @param int $runAsUser
   */
  public function setRunAsUser($runAsUser)
  {
    $this->runAsUser = $runAsUser;
  }
  /**
   * @return int
   */
  public function getRunAsUser()
  {
    return $this->runAsUser;
  }
  /**
   * Optional. If set, overrides the default DIR specified by the image.
   *
   * @param string $workingDir
   */
  public function setWorkingDir($workingDir)
  {
    $this->workingDir = $workingDir;
  }
  /**
   * @return string
   */
  public function getWorkingDir()
  {
    return $this->workingDir;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Container::class, 'Google_Service_CloudWorkstations_Container');
