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

class Step extends \Google\Collection
{
  /**
   * Default enum type; should not be used.
   */
  public const ON_ERROR_ON_ERROR_TYPE_UNSPECIFIED = 'ON_ERROR_TYPE_UNSPECIFIED';
  /**
   * StopAndFail indicates exit if the step/task exits with non-zero exit code
   */
  public const ON_ERROR_STOP_AND_FAIL = 'STOP_AND_FAIL';
  /**
   * Continue indicates continue executing the rest of the steps/tasks
   * irrespective of the exit code
   */
  public const ON_ERROR_CONTINUE = 'CONTINUE';
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
   * Name of the container specified as a DNS_LABEL.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. OnError defines the exiting behavior on error can be set to [
   * continue | stopAndFail ]
   *
   * @var string
   */
  public $onError;
  protected $paramsType = Param::class;
  protected $paramsDataType = 'array';
  protected $refType = StepRef::class;
  protected $refDataType = '';
  /**
   * The contents of an executable file to execute.
   *
   * @var string
   */
  public $script;
  protected $securityContextType = SecurityContext::class;
  protected $securityContextDataType = '';
  /**
   * Time after which the Step times out. Defaults to never.
   *
   * @var string
   */
  public $timeout;
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
   * Name of the container specified as a DNS_LABEL.
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
   * Optional. OnError defines the exiting behavior on error can be set to [
   * continue | stopAndFail ]
   *
   * Accepted values: ON_ERROR_TYPE_UNSPECIFIED, STOP_AND_FAIL, CONTINUE
   *
   * @param self::ON_ERROR_* $onError
   */
  public function setOnError($onError)
  {
    $this->onError = $onError;
  }
  /**
   * @return self::ON_ERROR_*
   */
  public function getOnError()
  {
    return $this->onError;
  }
  /**
   * Optional. Optional parameters passed to the StepAction.
   *
   * @param Param[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return Param[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Optional. Optional reference to a remote StepAction.
   *
   * @param StepRef $ref
   */
  public function setRef(StepRef $ref)
  {
    $this->ref = $ref;
  }
  /**
   * @return StepRef
   */
  public function getRef()
  {
    return $this->ref;
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
   * Optional. SecurityContext defines the security options the Step should be
   * run with. If set, the fields of SecurityContext override the equivalent
   * fields of PodSecurityContext. More info:
   * https://kubernetes.io/docs/tasks/configure-pod-container/security-context/
   * +optional
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
   * Time after which the Step times out. Defaults to never.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
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
class_alias(Step::class, 'Google_Service_CloudBuild_Step');
