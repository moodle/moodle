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

class ExportArtifactRequest extends \Google\Model
{
  /**
   * The Cloud Storage path to export the artifact to. Should start with the
   * bucket name, and optionally have a directory path. Examples: `dst_bucket`,
   * `dst_bucket/sub_dir`. Existing objects with the same path will be
   * overwritten.
   *
   * @var string
   */
  public $gcsPath;
  /**
   * The artifact tag to export. Format:projects/{project}/locations/{location}/
   * repositories/{repository}/packages/{package}/tags/{tag}
   *
   * @var string
   */
  public $sourceTag;
  /**
   * The artifact version to export. Format: projects/{project}/locations/{locat
   * ion}/repositories/{repository}/packages/{package}/versions/{version}
   *
   * @var string
   */
  public $sourceVersion;

  /**
   * The Cloud Storage path to export the artifact to. Should start with the
   * bucket name, and optionally have a directory path. Examples: `dst_bucket`,
   * `dst_bucket/sub_dir`. Existing objects with the same path will be
   * overwritten.
   *
   * @param string $gcsPath
   */
  public function setGcsPath($gcsPath)
  {
    $this->gcsPath = $gcsPath;
  }
  /**
   * @return string
   */
  public function getGcsPath()
  {
    return $this->gcsPath;
  }
  /**
   * The artifact tag to export. Format:projects/{project}/locations/{location}/
   * repositories/{repository}/packages/{package}/tags/{tag}
   *
   * @param string $sourceTag
   */
  public function setSourceTag($sourceTag)
  {
    $this->sourceTag = $sourceTag;
  }
  /**
   * @return string
   */
  public function getSourceTag()
  {
    return $this->sourceTag;
  }
  /**
   * The artifact version to export. Format: projects/{project}/locations/{locat
   * ion}/repositories/{repository}/packages/{package}/versions/{version}
   *
   * @param string $sourceVersion
   */
  public function setSourceVersion($sourceVersion)
  {
    $this->sourceVersion = $sourceVersion;
  }
  /**
   * @return string
   */
  public function getSourceVersion()
  {
    return $this->sourceVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportArtifactRequest::class, 'Google_Service_ArtifactRegistry_ExportArtifactRequest');
