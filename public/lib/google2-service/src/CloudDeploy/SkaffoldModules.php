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

class SkaffoldModules extends \Google\Collection
{
  protected $collection_key = 'configs';
  /**
   * Optional. The Skaffold Config modules to use from the specified source.
   *
   * @var string[]
   */
  public $configs;
  protected $gitType = SkaffoldGitSource::class;
  protected $gitDataType = '';
  protected $googleCloudBuildRepoType = SkaffoldGCBRepoSource::class;
  protected $googleCloudBuildRepoDataType = '';
  protected $googleCloudStorageType = SkaffoldGCSSource::class;
  protected $googleCloudStorageDataType = '';

  /**
   * Optional. The Skaffold Config modules to use from the specified source.
   *
   * @param string[] $configs
   */
  public function setConfigs($configs)
  {
    $this->configs = $configs;
  }
  /**
   * @return string[]
   */
  public function getConfigs()
  {
    return $this->configs;
  }
  /**
   * Optional. Remote git repository containing the Skaffold Config modules.
   *
   * @param SkaffoldGitSource $git
   */
  public function setGit(SkaffoldGitSource $git)
  {
    $this->git = $git;
  }
  /**
   * @return SkaffoldGitSource
   */
  public function getGit()
  {
    return $this->git;
  }
  /**
   * Optional. Cloud Build V2 repository containing the Skaffold Config modules.
   *
   * @param SkaffoldGCBRepoSource $googleCloudBuildRepo
   */
  public function setGoogleCloudBuildRepo(SkaffoldGCBRepoSource $googleCloudBuildRepo)
  {
    $this->googleCloudBuildRepo = $googleCloudBuildRepo;
  }
  /**
   * @return SkaffoldGCBRepoSource
   */
  public function getGoogleCloudBuildRepo()
  {
    return $this->googleCloudBuildRepo;
  }
  /**
   * Optional. Cloud Storage bucket containing the Skaffold Config modules.
   *
   * @param SkaffoldGCSSource $googleCloudStorage
   */
  public function setGoogleCloudStorage(SkaffoldGCSSource $googleCloudStorage)
  {
    $this->googleCloudStorage = $googleCloudStorage;
  }
  /**
   * @return SkaffoldGCSSource
   */
  public function getGoogleCloudStorage()
  {
    return $this->googleCloudStorage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SkaffoldModules::class, 'Google_Service_CloudDeploy_SkaffoldModules');
