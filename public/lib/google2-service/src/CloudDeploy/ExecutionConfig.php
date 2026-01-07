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

namespace Google\Service\CloudDeploy;

class ExecutionConfig extends \Google\Collection
{
  protected $collection_key = 'usages';
  /**
   * Optional. Cloud Storage location in which to store execution outputs. This
   * can either be a bucket ("gs://my-bucket") or a path within a bucket
   * ("gs://my-bucket/my-dir"). If unspecified, a default bucket located in the
   * same region will be used.
   *
   * @var string
   */
  public $artifactStorage;
  protected $defaultPoolType = DefaultPool::class;
  protected $defaultPoolDataType = '';
  /**
   * Optional. Execution timeout for a Cloud Build Execution. This must be
   * between 10m and 24h in seconds format. If unspecified, a default timeout of
   * 1h is used.
   *
   * @var string
   */
  public $executionTimeout;
  protected $privatePoolType = PrivatePool::class;
  protected $privatePoolDataType = '';
  /**
   * Optional. Google service account to use for execution. If unspecified, the
   * project execution service account (-compute@developer.gserviceaccount.com)
   * is used.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Required. Usages when this configuration should be applied.
   *
   * @var string[]
   */
  public $usages;
  /**
   * Optional. If true, additional logging will be enabled when running builds
   * in this execution environment.
   *
   * @var bool
   */
  public $verbose;
  /**
   * Optional. The resource name of the `WorkerPool`, with the format
   * `projects/{project}/locations/{location}/workerPools/{worker_pool}`. If
   * this optional field is unspecified, the default Cloud Build pool will be
   * used.
   *
   * @var string
   */
  public $workerPool;

  /**
   * Optional. Cloud Storage location in which to store execution outputs. This
   * can either be a bucket ("gs://my-bucket") or a path within a bucket
   * ("gs://my-bucket/my-dir"). If unspecified, a default bucket located in the
   * same region will be used.
   *
   * @param string $artifactStorage
   */
  public function setArtifactStorage($artifactStorage)
  {
    $this->artifactStorage = $artifactStorage;
  }
  /**
   * @return string
   */
  public function getArtifactStorage()
  {
    return $this->artifactStorage;
  }
  /**
   * Optional. Use default Cloud Build pool.
   *
   * @param DefaultPool $defaultPool
   */
  public function setDefaultPool(DefaultPool $defaultPool)
  {
    $this->defaultPool = $defaultPool;
  }
  /**
   * @return DefaultPool
   */
  public function getDefaultPool()
  {
    return $this->defaultPool;
  }
  /**
   * Optional. Execution timeout for a Cloud Build Execution. This must be
   * between 10m and 24h in seconds format. If unspecified, a default timeout of
   * 1h is used.
   *
   * @param string $executionTimeout
   */
  public function setExecutionTimeout($executionTimeout)
  {
    $this->executionTimeout = $executionTimeout;
  }
  /**
   * @return string
   */
  public function getExecutionTimeout()
  {
    return $this->executionTimeout;
  }
  /**
   * Optional. Use private Cloud Build pool.
   *
   * @param PrivatePool $privatePool
   */
  public function setPrivatePool(PrivatePool $privatePool)
  {
    $this->privatePool = $privatePool;
  }
  /**
   * @return PrivatePool
   */
  public function getPrivatePool()
  {
    return $this->privatePool;
  }
  /**
   * Optional. Google service account to use for execution. If unspecified, the
   * project execution service account (-compute@developer.gserviceaccount.com)
   * is used.
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
   * Required. Usages when this configuration should be applied.
   *
   * @param string[] $usages
   */
  public function setUsages($usages)
  {
    $this->usages = $usages;
  }
  /**
   * @return string[]
   */
  public function getUsages()
  {
    return $this->usages;
  }
  /**
   * Optional. If true, additional logging will be enabled when running builds
   * in this execution environment.
   *
   * @param bool $verbose
   */
  public function setVerbose($verbose)
  {
    $this->verbose = $verbose;
  }
  /**
   * @return bool
   */
  public function getVerbose()
  {
    return $this->verbose;
  }
  /**
   * Optional. The resource name of the `WorkerPool`, with the format
   * `projects/{project}/locations/{location}/workerPools/{worker_pool}`. If
   * this optional field is unspecified, the default Cloud Build pool will be
   * used.
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
class_alias(ExecutionConfig::class, 'Google_Service_CloudDeploy_ExecutionConfig');
