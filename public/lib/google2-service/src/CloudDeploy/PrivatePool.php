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

class PrivatePool extends \Google\Model
{
  /**
   * Optional. Cloud Storage location where execution outputs should be stored.
   * This can either be a bucket ("gs://my-bucket") or a path within a bucket
   * ("gs://my-bucket/my-dir"). If unspecified, a default bucket located in the
   * same region will be used.
   *
   * @var string
   */
  public $artifactStorage;
  /**
   * Optional. Google service account to use for execution. If unspecified, the
   * project execution service account (-compute@developer.gserviceaccount.com)
   * will be used.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Required. Resource name of the Cloud Build worker pool to use. The format
   * is `projects/{project}/locations/{location}/workerPools/{pool}`.
   *
   * @var string
   */
  public $workerPool;

  /**
   * Optional. Cloud Storage location where execution outputs should be stored.
   * This can either be a bucket ("gs://my-bucket") or a path within a bucket
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
   * Optional. Google service account to use for execution. If unspecified, the
   * project execution service account (-compute@developer.gserviceaccount.com)
   * will be used.
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
   * Required. Resource name of the Cloud Build worker pool to use. The format
   * is `projects/{project}/locations/{location}/workerPools/{pool}`.
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
class_alias(PrivatePool::class, 'Google_Service_CloudDeploy_PrivatePool');
