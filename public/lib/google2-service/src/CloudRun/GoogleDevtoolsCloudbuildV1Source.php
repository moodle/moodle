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

class GoogleDevtoolsCloudbuildV1Source extends \Google\Model
{
  protected $connectedRepositoryType = GoogleDevtoolsCloudbuildV1ConnectedRepository::class;
  protected $connectedRepositoryDataType = '';
  protected $developerConnectConfigType = GoogleDevtoolsCloudbuildV1DeveloperConnectConfig::class;
  protected $developerConnectConfigDataType = '';
  protected $gitSourceType = GoogleDevtoolsCloudbuildV1GitSource::class;
  protected $gitSourceDataType = '';
  protected $repoSourceType = GoogleDevtoolsCloudbuildV1RepoSource::class;
  protected $repoSourceDataType = '';
  protected $storageSourceType = GoogleDevtoolsCloudbuildV1StorageSource::class;
  protected $storageSourceDataType = '';
  protected $storageSourceManifestType = GoogleDevtoolsCloudbuildV1StorageSourceManifest::class;
  protected $storageSourceManifestDataType = '';

  /**
   * Optional. If provided, get the source from this 2nd-gen Google Cloud Build
   * repository resource.
   *
   * @param GoogleDevtoolsCloudbuildV1ConnectedRepository $connectedRepository
   */
  public function setConnectedRepository(GoogleDevtoolsCloudbuildV1ConnectedRepository $connectedRepository)
  {
    $this->connectedRepository = $connectedRepository;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1ConnectedRepository
   */
  public function getConnectedRepository()
  {
    return $this->connectedRepository;
  }
  /**
   * If provided, get the source from this Developer Connect config.
   *
   * @param GoogleDevtoolsCloudbuildV1DeveloperConnectConfig $developerConnectConfig
   */
  public function setDeveloperConnectConfig(GoogleDevtoolsCloudbuildV1DeveloperConnectConfig $developerConnectConfig)
  {
    $this->developerConnectConfig = $developerConnectConfig;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1DeveloperConnectConfig
   */
  public function getDeveloperConnectConfig()
  {
    return $this->developerConnectConfig;
  }
  /**
   * If provided, get the source from this Git repository.
   *
   * @param GoogleDevtoolsCloudbuildV1GitSource $gitSource
   */
  public function setGitSource(GoogleDevtoolsCloudbuildV1GitSource $gitSource)
  {
    $this->gitSource = $gitSource;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1GitSource
   */
  public function getGitSource()
  {
    return $this->gitSource;
  }
  /**
   * If provided, get the source from this location in a Cloud Source
   * Repository.
   *
   * @param GoogleDevtoolsCloudbuildV1RepoSource $repoSource
   */
  public function setRepoSource(GoogleDevtoolsCloudbuildV1RepoSource $repoSource)
  {
    $this->repoSource = $repoSource;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1RepoSource
   */
  public function getRepoSource()
  {
    return $this->repoSource;
  }
  /**
   * If provided, get the source from this location in Cloud Storage.
   *
   * @param GoogleDevtoolsCloudbuildV1StorageSource $storageSource
   */
  public function setStorageSource(GoogleDevtoolsCloudbuildV1StorageSource $storageSource)
  {
    $this->storageSource = $storageSource;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1StorageSource
   */
  public function getStorageSource()
  {
    return $this->storageSource;
  }
  /**
   * If provided, get the source from this manifest in Cloud Storage. This
   * feature is in Preview; see description
   * [here](https://github.com/GoogleCloudPlatform/cloud-
   * builders/tree/master/gcs-fetcher).
   *
   * @param GoogleDevtoolsCloudbuildV1StorageSourceManifest $storageSourceManifest
   */
  public function setStorageSourceManifest(GoogleDevtoolsCloudbuildV1StorageSourceManifest $storageSourceManifest)
  {
    $this->storageSourceManifest = $storageSourceManifest;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1StorageSourceManifest
   */
  public function getStorageSourceManifest()
  {
    return $this->storageSourceManifest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1Source::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1Source');
