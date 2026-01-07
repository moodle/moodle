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

class Version extends \Google\Collection
{
  protected $collection_key = 'relatedTags';
  /**
   * Optional. Client specified annotations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * The time when the version was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the version, as specified in its metadata.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Repository-specific Metadata stored against this version. The
   * fields returned are defined by the underlying repository-specific resource.
   * Currently, the resources could be: DockerImage MavenArtifact
   *
   * @var array[]
   */
  public $metadata;
  /**
   * The name of the version, for example: `projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1/versions/art1`. If the package or
   * version ID parts contain slashes, the slashes are escaped.
   *
   * @var string
   */
  public $name;
  protected $relatedTagsType = Tag::class;
  protected $relatedTagsDataType = 'array';
  /**
   * The time when the version was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Client specified annotations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * The time when the version was created.
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
   * Optional. Description of the version, as specified in its metadata.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Repository-specific Metadata stored against this version. The
   * fields returned are defined by the underlying repository-specific resource.
   * Currently, the resources could be: DockerImage MavenArtifact
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The name of the version, for example: `projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1/versions/art1`. If the package or
   * version ID parts contain slashes, the slashes are escaped.
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
   * Output only. A list of related tags. Will contain up to 100 tags that
   * reference this version.
   *
   * @param Tag[] $relatedTags
   */
  public function setRelatedTags($relatedTags)
  {
    $this->relatedTags = $relatedTags;
  }
  /**
   * @return Tag[]
   */
  public function getRelatedTags()
  {
    return $this->relatedTags;
  }
  /**
   * The time when the version was last updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Version::class, 'Google_Service_ArtifactRegistry_Version');
