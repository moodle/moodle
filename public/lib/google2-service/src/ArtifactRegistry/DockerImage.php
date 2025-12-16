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

class DockerImage extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * ArtifactType of this image, e.g. "application/vnd.example+type". If the
   * `subject_digest` is set and no `artifact_type` is given, the `media_type`
   * will be considered as the `artifact_type`. This field is returned as the
   * `metadata.artifactType` field in the Version resource.
   *
   * @var string
   */
  public $artifactType;
  /**
   * The time this image was built. This field is returned as the
   * 'metadata.buildTime' field in the Version resource. The build time is
   * returned to the client as an RFC 3339 string, which can be easily used with
   * the JavaScript Date constructor.
   *
   * @var string
   */
  public $buildTime;
  protected $imageManifestsType = ImageManifest::class;
  protected $imageManifestsDataType = 'array';
  /**
   * Calculated size of the image. This field is returned as the
   * 'metadata.imageSizeBytes' field in the Version resource.
   *
   * @var string
   */
  public $imageSizeBytes;
  /**
   * Media type of this image, e.g.
   * "application/vnd.docker.distribution.manifest.v2+json". This field is
   * returned as the 'metadata.mediaType' field in the Version resource.
   *
   * @var string
   */
  public $mediaType;
  /**
   * Required. registry_location, project_id, repository_name and image id forms
   * a unique image name:`projects//locations//repositories//dockerImages/`. For
   * example, "projects/test-project/locations/us-west4/repositories/test-
   * repo/dockerImages/ nginx@sha256:e9954c1fc875017be1c3e36eca16be2d9e9bccc4bf0
   * 72163515467d6a823c7cf", where "us-west4" is the registry_location, "test-
   * project" is the project_id, "test-repo" is the repository_name and "nginx@s
   * ha256:e9954c1fc875017be1c3e36eca16be2d9e9bccc4bf072163515467d6a823c7cf" is
   * the image's digest.
   *
   * @var string
   */
  public $name;
  /**
   * Tags attached to this image.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Output only. The time when the docker image was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Time the image was uploaded.
   *
   * @var string
   */
  public $uploadTime;
  /**
   * Required. URL to access the image. Example: us-west4-docker.pkg.dev/test-
   * project/test-repo/nginx@sha256:e9954c1fc875017be1c3e36eca16be2d9e9bccc4bf07
   * 2163515467d6a823c7cf
   *
   * @var string
   */
  public $uri;

  /**
   * ArtifactType of this image, e.g. "application/vnd.example+type". If the
   * `subject_digest` is set and no `artifact_type` is given, the `media_type`
   * will be considered as the `artifact_type`. This field is returned as the
   * `metadata.artifactType` field in the Version resource.
   *
   * @param string $artifactType
   */
  public function setArtifactType($artifactType)
  {
    $this->artifactType = $artifactType;
  }
  /**
   * @return string
   */
  public function getArtifactType()
  {
    return $this->artifactType;
  }
  /**
   * The time this image was built. This field is returned as the
   * 'metadata.buildTime' field in the Version resource. The build time is
   * returned to the client as an RFC 3339 string, which can be easily used with
   * the JavaScript Date constructor.
   *
   * @param string $buildTime
   */
  public function setBuildTime($buildTime)
  {
    $this->buildTime = $buildTime;
  }
  /**
   * @return string
   */
  public function getBuildTime()
  {
    return $this->buildTime;
  }
  /**
   * Optional. For multi-arch images (manifest lists), this field contains the
   * list of image manifests.
   *
   * @param ImageManifest[] $imageManifests
   */
  public function setImageManifests($imageManifests)
  {
    $this->imageManifests = $imageManifests;
  }
  /**
   * @return ImageManifest[]
   */
  public function getImageManifests()
  {
    return $this->imageManifests;
  }
  /**
   * Calculated size of the image. This field is returned as the
   * 'metadata.imageSizeBytes' field in the Version resource.
   *
   * @param string $imageSizeBytes
   */
  public function setImageSizeBytes($imageSizeBytes)
  {
    $this->imageSizeBytes = $imageSizeBytes;
  }
  /**
   * @return string
   */
  public function getImageSizeBytes()
  {
    return $this->imageSizeBytes;
  }
  /**
   * Media type of this image, e.g.
   * "application/vnd.docker.distribution.manifest.v2+json". This field is
   * returned as the 'metadata.mediaType' field in the Version resource.
   *
   * @param string $mediaType
   */
  public function setMediaType($mediaType)
  {
    $this->mediaType = $mediaType;
  }
  /**
   * @return string
   */
  public function getMediaType()
  {
    return $this->mediaType;
  }
  /**
   * Required. registry_location, project_id, repository_name and image id forms
   * a unique image name:`projects//locations//repositories//dockerImages/`. For
   * example, "projects/test-project/locations/us-west4/repositories/test-
   * repo/dockerImages/ nginx@sha256:e9954c1fc875017be1c3e36eca16be2d9e9bccc4bf0
   * 72163515467d6a823c7cf", where "us-west4" is the registry_location, "test-
   * project" is the project_id, "test-repo" is the repository_name and "nginx@s
   * ha256:e9954c1fc875017be1c3e36eca16be2d9e9bccc4bf072163515467d6a823c7cf" is
   * the image's digest.
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
   * Tags attached to this image.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Output only. The time when the docker image was last updated.
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
   * Time the image was uploaded.
   *
   * @param string $uploadTime
   */
  public function setUploadTime($uploadTime)
  {
    $this->uploadTime = $uploadTime;
  }
  /**
   * @return string
   */
  public function getUploadTime()
  {
    return $this->uploadTime;
  }
  /**
   * Required. URL to access the image. Example: us-west4-docker.pkg.dev/test-
   * project/test-repo/nginx@sha256:e9954c1fc875017be1c3e36eca16be2d9e9bccc4bf07
   * 2163515467d6a823c7cf
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DockerImage::class, 'Google_Service_ArtifactRegistry_DockerImage');
