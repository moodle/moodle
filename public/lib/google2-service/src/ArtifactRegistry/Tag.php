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

class Tag extends \Google\Model
{
  /**
   * The name of the tag, for example: "projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1/tags/tag1". If the package part
   * contains slashes, the slashes are escaped. The tag part can only have
   * characters in [a-zA-Z0-9\-._~:@], anything else must be URL encoded.
   *
   * @var string
   */
  public $name;
  /**
   * The name of the version the tag refers to, for example:
   * `projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1/versions/sha256:5243811` If the
   * package or version ID parts contain slashes, the slashes are escaped.
   *
   * @var string
   */
  public $version;

  /**
   * The name of the tag, for example: "projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1/tags/tag1". If the package part
   * contains slashes, the slashes are escaped. The tag part can only have
   * characters in [a-zA-Z0-9\-._~:@], anything else must be URL encoded.
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
   * The name of the version the tag refers to, for example:
   * `projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1/versions/sha256:5243811` If the
   * package or version ID parts contain slashes, the slashes are escaped.
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
class_alias(Tag::class, 'Google_Service_ArtifactRegistry_Tag');
