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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ContainerSpec extends \Google\Collection
{
  protected $collection_key = 'env';
  /**
   * The arguments to be passed when starting the container.
   *
   * @var string[]
   */
  public $args;
  /**
   * The command to be invoked when the container is started. It overrides the
   * entrypoint instruction in Dockerfile when provided.
   *
   * @var string[]
   */
  public $command;
  protected $envType = GoogleCloudAiplatformV1EnvVar::class;
  protected $envDataType = 'array';
  /**
   * Required. The URI of a container image in the Container Registry that is to
   * be run on each worker replica.
   *
   * @var string
   */
  public $imageUri;

  /**
   * The arguments to be passed when starting the container.
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
   * The command to be invoked when the container is started. It overrides the
   * entrypoint instruction in Dockerfile when provided.
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
   * Environment variables to be passed to the container. Maximum limit is 100.
   *
   * @param GoogleCloudAiplatformV1EnvVar[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return GoogleCloudAiplatformV1EnvVar[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Required. The URI of a container image in the Container Registry that is to
   * be run on each worker replica.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ContainerSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ContainerSpec');
