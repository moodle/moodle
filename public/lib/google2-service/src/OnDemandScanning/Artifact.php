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

namespace Google\Service\OnDemandScanning;

class Artifact extends \Google\Collection
{
  protected $collection_key = 'names';
  /**
   * Hash or checksum value of a binary, or Docker Registry 2.0 digest of a
   * container.
   *
   * @var string
   */
  public $checksum;
  /**
   * Artifact ID, if any; for container images, this will be a URL by digest
   * like `gcr.io/projectID/imagename@sha256:123456`.
   *
   * @var string
   */
  public $id;
  /**
   * Related artifact names. This may be the path to a binary or jar file, or in
   * the case of a container build, the name used to push the container image to
   * Google Container Registry, as presented to `docker push`. Note that a
   * single Artifact ID can have multiple names, for example if two tags are
   * applied to one image.
   *
   * @var string[]
   */
  public $names;

  /**
   * Hash or checksum value of a binary, or Docker Registry 2.0 digest of a
   * container.
   *
   * @param string $checksum
   */
  public function setChecksum($checksum)
  {
    $this->checksum = $checksum;
  }
  /**
   * @return string
   */
  public function getChecksum()
  {
    return $this->checksum;
  }
  /**
   * Artifact ID, if any; for container images, this will be a URL by digest
   * like `gcr.io/projectID/imagename@sha256:123456`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Related artifact names. This may be the path to a binary or jar file, or in
   * the case of a container build, the name used to push the container image to
   * Google Container Registry, as presented to `docker push`. Note that a
   * single Artifact ID can have multiple names, for example if two tags are
   * applied to one image.
   *
   * @param string[] $names
   */
  public function setNames($names)
  {
    $this->names = $names;
  }
  /**
   * @return string[]
   */
  public function getNames()
  {
    return $this->names;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Artifact::class, 'Google_Service_OnDemandScanning_Artifact');
