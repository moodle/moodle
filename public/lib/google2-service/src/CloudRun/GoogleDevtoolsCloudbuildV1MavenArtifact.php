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

class GoogleDevtoolsCloudbuildV1MavenArtifact extends \Google\Model
{
  /**
   * Maven `artifactId` value used when uploading the artifact to Artifact
   * Registry.
   *
   * @var string
   */
  public $artifactId;
  /**
   * Optional. Path to a folder containing the files to upload to Artifact
   * Registry. This can be either an absolute path, e.g. `/workspace/my-
   * app/target/`, or a relative path from /workspace, e.g. `my-app/target/`.
   * This field is mutually exclusive with the `path` field.
   *
   * @var string
   */
  public $deployFolder;
  /**
   * Maven `groupId` value used when uploading the artifact to Artifact
   * Registry.
   *
   * @var string
   */
  public $groupId;
  /**
   * Optional. Path to an artifact in the build's workspace to be uploaded to
   * Artifact Registry. This can be either an absolute path, e.g. /workspace/my-
   * app/target/my-app-1.0.SNAPSHOT.jar or a relative path from /workspace, e.g.
   * my-app/target/my-app-1.0.SNAPSHOT.jar.
   *
   * @var string
   */
  public $path;
  /**
   * Artifact Registry repository, in the form "https://$REGION-
   * maven.pkg.dev/$PROJECT/$REPOSITORY" Artifact in the workspace specified by
   * path will be uploaded to Artifact Registry with this location as a prefix.
   *
   * @var string
   */
  public $repository;
  /**
   * Maven `version` value used when uploading the artifact to Artifact
   * Registry.
   *
   * @var string
   */
  public $version;

  /**
   * Maven `artifactId` value used when uploading the artifact to Artifact
   * Registry.
   *
   * @param string $artifactId
   */
  public function setArtifactId($artifactId)
  {
    $this->artifactId = $artifactId;
  }
  /**
   * @return string
   */
  public function getArtifactId()
  {
    return $this->artifactId;
  }
  /**
   * Optional. Path to a folder containing the files to upload to Artifact
   * Registry. This can be either an absolute path, e.g. `/workspace/my-
   * app/target/`, or a relative path from /workspace, e.g. `my-app/target/`.
   * This field is mutually exclusive with the `path` field.
   *
   * @param string $deployFolder
   */
  public function setDeployFolder($deployFolder)
  {
    $this->deployFolder = $deployFolder;
  }
  /**
   * @return string
   */
  public function getDeployFolder()
  {
    return $this->deployFolder;
  }
  /**
   * Maven `groupId` value used when uploading the artifact to Artifact
   * Registry.
   *
   * @param string $groupId
   */
  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }
  /**
   * @return string
   */
  public function getGroupId()
  {
    return $this->groupId;
  }
  /**
   * Optional. Path to an artifact in the build's workspace to be uploaded to
   * Artifact Registry. This can be either an absolute path, e.g. /workspace/my-
   * app/target/my-app-1.0.SNAPSHOT.jar or a relative path from /workspace, e.g.
   * my-app/target/my-app-1.0.SNAPSHOT.jar.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Artifact Registry repository, in the form "https://$REGION-
   * maven.pkg.dev/$PROJECT/$REPOSITORY" Artifact in the workspace specified by
   * path will be uploaded to Artifact Registry with this location as a prefix.
   *
   * @param string $repository
   */
  public function setRepository($repository)
  {
    $this->repository = $repository;
  }
  /**
   * @return string
   */
  public function getRepository()
  {
    return $this->repository;
  }
  /**
   * Maven `version` value used when uploading the artifact to Artifact
   * Registry.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1MavenArtifact::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1MavenArtifact');
