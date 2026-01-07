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

class GoogleCloudRunV2ContainerOverride extends \Google\Collection
{
  protected $collection_key = 'env';
  /**
   * Optional. Arguments to the entrypoint. Will replace existing args for
   * override.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. True if the intention is to clear out existing args list.
   *
   * @var bool
   */
  public $clearArgs;
  protected $envType = GoogleCloudRunV2EnvVar::class;
  protected $envDataType = 'array';
  /**
   * The name of the container specified as a DNS_LABEL.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. Arguments to the entrypoint. Will replace existing args for
   * override.
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
   * Optional. True if the intention is to clear out existing args list.
   *
   * @param bool $clearArgs
   */
  public function setClearArgs($clearArgs)
  {
    $this->clearArgs = $clearArgs;
  }
  /**
   * @return bool
   */
  public function getClearArgs()
  {
    return $this->clearArgs;
  }
  /**
   * List of environment variables to set in the container. Will be merged with
   * existing env for override.
   *
   * @param GoogleCloudRunV2EnvVar[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return GoogleCloudRunV2EnvVar[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * The name of the container specified as a DNS_LABEL.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2ContainerOverride::class, 'Google_Service_CloudRun_GoogleCloudRunV2ContainerOverride');
