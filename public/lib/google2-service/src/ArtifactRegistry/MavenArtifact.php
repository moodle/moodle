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

namespace Google\Service\ArtifactRegistry;

class MavenArtifact extends \Google\Model
{
  /**
   * Artifact ID for the artifact.
   *
   * @var string
   */
  public $artifactId;
  /**
   * Output only. Time the artifact was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Group ID for the artifact. Example: com.google.guava
   *
   * @var string
   */
  public $groupId;
  /**
   * Required. registry_location, project_id, repository_name and maven_artifact
   * forms a unique artifact For example, "projects/test-project/locations/us-
   * west4/repositories/test-repo/mavenArtifacts/
   * com.google.guava:guava:31.0-jre", where "us-west4" is the
   * registry_location, "test-project" is the project_id, "test-repo" is the
   * repository_name and "com.google.guava:guava:31.0-jre" is the maven
   * artifact.
   *
   * @var string
   */
  public $name;
  /**
   * Required. URL to access the pom file of the artifact. Example: us-
   * west4-maven.pkg.dev/test-project/test-
   * repo/com/google/guava/guava/31.0/guava-31.0.pom
   *
   * @var string
   */
  public $pomUri;
  /**
   * Output only. Time the artifact was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Version of this artifact.
   *
   * @var string
   */
  public $version;

  /**
   * Artifact ID for the artifact.
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
   * Output only. Time the artifact was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Group ID for the artifact. Example: com.google.guava
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
   * Required. registry_location, project_id, repository_name and maven_artifact
   * forms a unique artifact For example, "projects/test-project/locations/us-
   * west4/repositories/test-repo/mavenArtifacts/
   * com.google.guava:guava:31.0-jre", where "us-west4" is the
   * registry_location, "test-project" is the project_id, "test-repo" is the
   * repository_name and "com.google.guava:guava:31.0-jre" is the maven
   * artifact.
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
   * Required. URL to access the pom file of the artifact. Example: us-
   * west4-maven.pkg.dev/test-project/test-
   * repo/com/google/guava/guava/31.0/guava-31.0.pom
   *
   * @param string $pomUri
   */
  public function setPomUri($pomUri)
  {
    $this->pomUri = $pomUri;
  }
  /**
   * @return string
   */
  public function getPomUri()
  {
    return $this->pomUri;
  }
  /**
   * Output only. Time the artifact was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Version of this artifact.
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
class_alias(MavenArtifact::class, 'Google_Service_ArtifactRegistry_MavenArtifact');
