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

class GoogleCloudAiplatformV1PythonPackageSpec extends \Google\Collection
{
  protected $collection_key = 'packageUris';
  /**
   * Command line arguments to be passed to the Python task.
   *
   * @var string[]
   */
  public $args;
  protected $envType = GoogleCloudAiplatformV1EnvVar::class;
  protected $envDataType = 'array';
  /**
   * Required. The URI of a container image in Artifact Registry that will run
   * the provided Python package. Vertex AI provides a wide range of executor
   * images with pre-installed packages to meet users' various use cases. See
   * the list of [pre-built containers for
   * training](https://cloud.google.com/vertex-ai/docs/training/pre-built-
   * containers). You must use an image from this list.
   *
   * @var string
   */
  public $executorImageUri;
  /**
   * Required. The Google Cloud Storage location of the Python package files
   * which are the training program and its dependent packages. The maximum
   * number of package URIs is 100.
   *
   * @var string[]
   */
  public $packageUris;
  /**
   * Required. The Python module name to run after installing the packages.
   *
   * @var string
   */
  public $pythonModule;

  /**
   * Command line arguments to be passed to the Python task.
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
   * Environment variables to be passed to the python module. Maximum limit is
   * 100.
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
   * Required. The URI of a container image in Artifact Registry that will run
   * the provided Python package. Vertex AI provides a wide range of executor
   * images with pre-installed packages to meet users' various use cases. See
   * the list of [pre-built containers for
   * training](https://cloud.google.com/vertex-ai/docs/training/pre-built-
   * containers). You must use an image from this list.
   *
   * @param string $executorImageUri
   */
  public function setExecutorImageUri($executorImageUri)
  {
    $this->executorImageUri = $executorImageUri;
  }
  /**
   * @return string
   */
  public function getExecutorImageUri()
  {
    return $this->executorImageUri;
  }
  /**
   * Required. The Google Cloud Storage location of the Python package files
   * which are the training program and its dependent packages. The maximum
   * number of package URIs is 100.
   *
   * @param string[] $packageUris
   */
  public function setPackageUris($packageUris)
  {
    $this->packageUris = $packageUris;
  }
  /**
   * @return string[]
   */
  public function getPackageUris()
  {
    return $this->packageUris;
  }
  /**
   * Required. The Python module name to run after installing the packages.
   *
   * @param string $pythonModule
   */
  public function setPythonModule($pythonModule)
  {
    $this->pythonModule = $pythonModule;
  }
  /**
   * @return string
   */
  public function getPythonModule()
  {
    return $this->pythonModule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PythonPackageSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PythonPackageSpec');
