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

namespace Google\Service\ContainerAnalysis;

class ContaineranalysisGoogleDevtoolsCloudbuildV1SourceProvenance extends \Google\Model
{
  protected $fileHashesType = ContaineranalysisGoogleDevtoolsCloudbuildV1FileHashes::class;
  protected $fileHashesDataType = 'map';
  protected $resolvedConnectedRepositoryType = ContaineranalysisGoogleDevtoolsCloudbuildV1ConnectedRepository::class;
  protected $resolvedConnectedRepositoryDataType = '';
  protected $resolvedGitSourceType = ContaineranalysisGoogleDevtoolsCloudbuildV1GitSource::class;
  protected $resolvedGitSourceDataType = '';
  protected $resolvedRepoSourceType = ContaineranalysisGoogleDevtoolsCloudbuildV1RepoSource::class;
  protected $resolvedRepoSourceDataType = '';
  protected $resolvedStorageSourceType = ContaineranalysisGoogleDevtoolsCloudbuildV1StorageSource::class;
  protected $resolvedStorageSourceDataType = '';
  protected $resolvedStorageSourceManifestType = ContaineranalysisGoogleDevtoolsCloudbuildV1StorageSourceManifest::class;
  protected $resolvedStorageSourceManifestDataType = '';

  /**
   * Output only. Hash(es) of the build source, which can be used to verify that
   * the original source integrity was maintained in the build. Note that
   * `FileHashes` will only be populated if `BuildOptions` has requested a
   * `SourceProvenanceHash`. The keys to this map are file paths used as build
   * source and the values contain the hash values for those files. If the build
   * source came in a single package such as a gzipped tarfile (`.tar.gz`), the
   * `FileHash` will be for the single path to that file.
   *
   * @param ContaineranalysisGoogleDevtoolsCloudbuildV1FileHashes[] $fileHashes
   */
  public function setFileHashes($fileHashes)
  {
    $this->fileHashes = $fileHashes;
  }
  /**
   * @return ContaineranalysisGoogleDevtoolsCloudbuildV1FileHashes[]
   */
  public function getFileHashes()
  {
    return $this->fileHashes;
  }
  /**
   * Output only. A copy of the build's `source.connected_repository`, if
   * exists, with any revisions resolved.
   *
   * @param ContaineranalysisGoogleDevtoolsCloudbuildV1ConnectedRepository $resolvedConnectedRepository
   */
  public function setResolvedConnectedRepository(ContaineranalysisGoogleDevtoolsCloudbuildV1ConnectedRepository $resolvedConnectedRepository)
  {
    $this->resolvedConnectedRepository = $resolvedConnectedRepository;
  }
  /**
   * @return ContaineranalysisGoogleDevtoolsCloudbuildV1ConnectedRepository
   */
  public function getResolvedConnectedRepository()
  {
    return $this->resolvedConnectedRepository;
  }
  /**
   * Output only. A copy of the build's `source.git_source`, if exists, with any
   * revisions resolved.
   *
   * @param ContaineranalysisGoogleDevtoolsCloudbuildV1GitSource $resolvedGitSource
   */
  public function setResolvedGitSource(ContaineranalysisGoogleDevtoolsCloudbuildV1GitSource $resolvedGitSource)
  {
    $this->resolvedGitSource = $resolvedGitSource;
  }
  /**
   * @return ContaineranalysisGoogleDevtoolsCloudbuildV1GitSource
   */
  public function getResolvedGitSource()
  {
    return $this->resolvedGitSource;
  }
  /**
   * A copy of the build's `source.repo_source`, if exists, with any revisions
   * resolved.
   *
   * @param ContaineranalysisGoogleDevtoolsCloudbuildV1RepoSource $resolvedRepoSource
   */
  public function setResolvedRepoSource(ContaineranalysisGoogleDevtoolsCloudbuildV1RepoSource $resolvedRepoSource)
  {
    $this->resolvedRepoSource = $resolvedRepoSource;
  }
  /**
   * @return ContaineranalysisGoogleDevtoolsCloudbuildV1RepoSource
   */
  public function getResolvedRepoSource()
  {
    return $this->resolvedRepoSource;
  }
  /**
   * A copy of the build's `source.storage_source`, if exists, with any
   * generations resolved.
   *
   * @param ContaineranalysisGoogleDevtoolsCloudbuildV1StorageSource $resolvedStorageSource
   */
  public function setResolvedStorageSource(ContaineranalysisGoogleDevtoolsCloudbuildV1StorageSource $resolvedStorageSource)
  {
    $this->resolvedStorageSource = $resolvedStorageSource;
  }
  /**
   * @return ContaineranalysisGoogleDevtoolsCloudbuildV1StorageSource
   */
  public function getResolvedStorageSource()
  {
    return $this->resolvedStorageSource;
  }
  /**
   * A copy of the build's `source.storage_source_manifest`, if exists, with any
   * revisions resolved. This feature is in Preview.
   *
   * @param ContaineranalysisGoogleDevtoolsCloudbuildV1StorageSourceManifest $resolvedStorageSourceManifest
   */
  public function setResolvedStorageSourceManifest(ContaineranalysisGoogleDevtoolsCloudbuildV1StorageSourceManifest $resolvedStorageSourceManifest)
  {
    $this->resolvedStorageSourceManifest = $resolvedStorageSourceManifest;
  }
  /**
   * @return ContaineranalysisGoogleDevtoolsCloudbuildV1StorageSourceManifest
   */
  public function getResolvedStorageSourceManifest()
  {
    return $this->resolvedStorageSourceManifest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1SourceProvenance::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1SourceProvenance');
