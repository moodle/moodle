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

namespace Google\Service\CloudBuild;

class Sidecar extends \Google\Collection
{
  protected $collection_key = 'volumeMounts';
  /**
   * Arguments to the entrypoint.
   *
   * @var string[]
   */
  public $args;
  /**
   * Entrypoint array.
   *
   * @var string[]
   */
  public $command;
  protected $envType = EnvVar::class;
  protected $envDataType = 'array';
  /**
   * Docker image name.
   *
   * @var string
   */
  public $image;
  /**
   * Name of the Sidecar.
   *
   * @var string
   */
  public $name;
  protected $readinessProbeType = Probe::class;
  protected $readinessProbeDataType = '';
  /**
   * The contents of an executable file to execute.
   *
   * @var string
   */
  public $script;
  protected $securityContextType = SecurityContext::class;
  protected $securityContextDataType = '';
  protected $volumeMountsType = VolumeMount::class;
  protected $volumeMountsDataType = 'array';
  /**
   * Container's working directory.
   *
   * @var string
   */
  public $workingDir;

  /**
   * Arguments to the entrypoint.
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
   * Entrypoint array.
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
   * List of environment variables to set in the container.
   *
   * @param EnvVar[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return EnvVar[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Docker image name.
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
   * Name of the Sidecar.
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
   * Optional. Periodic probe of Sidecar service readiness. Container will be
   * removed from service endpoints if the probe fails. Cannot be updated. More
   * info: https://kubernetes.io/docs/concepts/workloads/pods/pod-
   * lifecycle#container-probes +optional
   *
   * @param Probe $readinessProbe
   */
  public function setReadinessProbe(Probe $readinessProbe)
  {
    $this->readinessProbe = $readinessProbe;
  }
  /**
   * @return Probe
   */
  public function getReadinessProbe()
  {
    return $this->readinessProbe;
  }
  /**
   * The contents of an executable file to execute.
   *
   * @param string $script
   */
  public function setScript($script)
  {
    $this->script = $script;
  }
  /**
   * @return string
   */
  public function getScript()
  {
    return $this->script;
  }
  /**
   * Optional. Security options the container should be run with.
   *
   * @param SecurityContext $securityContext
   */
  public function setSecurityContext(SecurityContext $securityContext)
  {
    $this->securityContext = $securityContext;
  }
  /**
   * @return SecurityContext
   */
  public function getSecurityContext()
  {
    return $this->securityContext;
  }
  /**
   * Pod volumes to mount into the container's filesystem.
   *
   * @param VolumeMount[] $volumeMounts
   */
  public function setVolumeMounts($volumeMounts)
  {
    $this->volumeMounts = $volumeMounts;
  }
  /**
   * @return VolumeMount[]
   */
  public function getVolumeMounts()
  {
    return $this->volumeMounts;
  }
  /**
   * Container's working directory.
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
class_alias(Sidecar::class, 'Google_Service_CloudBuild_Sidecar');
