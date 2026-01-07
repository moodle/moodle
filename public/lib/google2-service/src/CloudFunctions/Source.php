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

namespace Google\Service\CloudFunctions;

class Source extends \Google\Model
{
  /**
   * If provided, get the source from GitHub repository. This option is valid
   * only for GCF 1st Gen function. Example: https://github.comblob//
   *
   * @var string
   */
  public $gitUri;
  protected $repoSourceType = RepoSource::class;
  protected $repoSourceDataType = '';
  protected $storageSourceType = StorageSource::class;
  protected $storageSourceDataType = '';

  /**
   * If provided, get the source from GitHub repository. This option is valid
   * only for GCF 1st Gen function. Example: https://github.comblob//
   *
   * @param string $gitUri
   */
  public function setGitUri($gitUri)
  {
    $this->gitUri = $gitUri;
  }
  /**
   * @return string
   */
  public function getGitUri()
  {
    return $this->gitUri;
  }
  /**
   * If provided, get the source from this location in a Cloud Source
   * Repository.
   *
   * @param RepoSource $repoSource
   */
  public function setRepoSource(RepoSource $repoSource)
  {
    $this->repoSource = $repoSource;
  }
  /**
   * @return RepoSource
   */
  public function getRepoSource()
  {
    return $this->repoSource;
  }
  /**
   * If provided, get the source from this location in Google Cloud Storage.
   *
   * @param StorageSource $storageSource
   */
  public function setStorageSource(StorageSource $storageSource)
  {
    $this->storageSource = $storageSource;
  }
  /**
   * @return StorageSource
   */
  public function getStorageSource()
  {
    return $this->storageSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Source::class, 'Google_Service_CloudFunctions_Source');
