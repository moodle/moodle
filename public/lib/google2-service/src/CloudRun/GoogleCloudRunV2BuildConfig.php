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

class GoogleCloudRunV2BuildConfig extends \Google\Model
{
  /**
   * Optional. The base image used to build the function.
   *
   * @var string
   */
  public $baseImage;
  /**
   * Optional. Sets whether the function will receive automatic base image
   * updates.
   *
   * @var bool
   */
  public $enableAutomaticUpdates;
  /**
   * Optional. User-provided build-time environment variables for the function
   *
   * @var string[]
   */
  public $environmentVariables;
  /**
   * Optional. The name of the function (as defined in source code) that will be
   * executed. Defaults to the resource name suffix, if not specified. For
   * backward compatibility, if function with given name is not found, then the
   * system will try to use function named "function".
   *
   * @var string
   */
  public $functionTarget;
  /**
   * Optional. Artifact Registry URI to store the built image.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Output only. The Cloud Build name of the latest successful deployment of
   * the function.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Service account to be used for building the container. The format
   * of this field is
   * `projects/{projectId}/serviceAccounts/{serviceAccountEmail}`.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * The Cloud Storage bucket URI where the function source code is located.
   *
   * @var string
   */
  public $sourceLocation;
  /**
   * Optional. Name of the Cloud Build Custom Worker Pool that should be used to
   * build the Cloud Run function. The format of this field is
   * `projects/{project}/locations/{region}/workerPools/{workerPool}` where
   * `{project}` and `{region}` are the project id and region respectively where
   * the worker pool is defined and `{workerPool}` is the short name of the
   * worker pool.
   *
   * @var string
   */
  public $workerPool;

  /**
   * Optional. The base image used to build the function.
   *
   * @param string $baseImage
   */
  public function setBaseImage($baseImage)
  {
    $this->baseImage = $baseImage;
  }
  /**
   * @return string
   */
  public function getBaseImage()
  {
    return $this->baseImage;
  }
  /**
   * Optional. Sets whether the function will receive automatic base image
   * updates.
   *
   * @param bool $enableAutomaticUpdates
   */
  public function setEnableAutomaticUpdates($enableAutomaticUpdates)
  {
    $this->enableAutomaticUpdates = $enableAutomaticUpdates;
  }
  /**
   * @return bool
   */
  public function getEnableAutomaticUpdates()
  {
    return $this->enableAutomaticUpdates;
  }
  /**
   * Optional. User-provided build-time environment variables for the function
   *
   * @param string[] $environmentVariables
   */
  public function setEnvironmentVariables($environmentVariables)
  {
    $this->environmentVariables = $environmentVariables;
  }
  /**
   * @return string[]
   */
  public function getEnvironmentVariables()
  {
    return $this->environmentVariables;
  }
  /**
   * Optional. The name of the function (as defined in source code) that will be
   * executed. Defaults to the resource name suffix, if not specified. For
   * backward compatibility, if function with given name is not found, then the
   * system will try to use function named "function".
   *
   * @param string $functionTarget
   */
  public function setFunctionTarget($functionTarget)
  {
    $this->functionTarget = $functionTarget;
  }
  /**
   * @return string
   */
  public function getFunctionTarget()
  {
    return $this->functionTarget;
  }
  /**
   * Optional. Artifact Registry URI to store the built image.
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
  /**
   * Output only. The Cloud Build name of the latest successful deployment of
   * the function.
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
   * Optional. Service account to be used for building the container. The format
   * of this field is
   * `projects/{projectId}/serviceAccounts/{serviceAccountEmail}`.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * The Cloud Storage bucket URI where the function source code is located.
   *
   * @param string $sourceLocation
   */
  public function setSourceLocation($sourceLocation)
  {
    $this->sourceLocation = $sourceLocation;
  }
  /**
   * @return string
   */
  public function getSourceLocation()
  {
    return $this->sourceLocation;
  }
  /**
   * Optional. Name of the Cloud Build Custom Worker Pool that should be used to
   * build the Cloud Run function. The format of this field is
   * `projects/{project}/locations/{region}/workerPools/{workerPool}` where
   * `{project}` and `{region}` are the project id and region respectively where
   * the worker pool is defined and `{workerPool}` is the short name of the
   * worker pool.
   *
   * @param string $workerPool
   */
  public function setWorkerPool($workerPool)
  {
    $this->workerPool = $workerPool;
  }
  /**
   * @return string
   */
  public function getWorkerPool()
  {
    return $this->workerPool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2BuildConfig::class, 'Google_Service_CloudRun_GoogleCloudRunV2BuildConfig');
