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

class ImageManifest extends \Google\Collection
{
  protected $collection_key = 'osFeatures';
  /**
   * Optional. The CPU architecture of the image. Values are provided by the
   * Docker client and are not validated by Artifact Registry. Example values
   * include "amd64", "arm64", "ppc64le", "s390x", "riscv64", "mips64le", etc.
   *
   * @var string
   */
  public $architecture;
  /**
   * Optional. The manifest digest, in the format "sha256:".
   *
   * @var string
   */
  public $digest;
  /**
   * Optional. The media type of the manifest, e.g.,
   * "application/vnd.docker.distribution.manifest.v2+json"
   *
   * @var string
   */
  public $mediaType;
  /**
   * Optional. The operating system of the image. Values are provided by the
   * Docker client and are not validated by Artifact Registry. Example values
   * include "linux", "windows", "darwin", "aix", etc.
   *
   * @var string
   */
  public $os;
  /**
   * Optional. The required OS features for the image, for example on Windows
   * `win32k`.
   *
   * @var string[]
   */
  public $osFeatures;
  /**
   * Optional. The OS version of the image, for example on Windows
   * `10.0.14393.1066`.
   *
   * @var string
   */
  public $osVersion;
  /**
   * Optional. The variant of the CPU in the image, for example `v7` to specify
   * ARMv7 when architecture is `arm`.
   *
   * @var string
   */
  public $variant;

  /**
   * Optional. The CPU architecture of the image. Values are provided by the
   * Docker client and are not validated by Artifact Registry. Example values
   * include "amd64", "arm64", "ppc64le", "s390x", "riscv64", "mips64le", etc.
   *
   * @param string $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return string
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * Optional. The manifest digest, in the format "sha256:".
   *
   * @param string $digest
   */
  public function setDigest($digest)
  {
    $this->digest = $digest;
  }
  /**
   * @return string
   */
  public function getDigest()
  {
    return $this->digest;
  }
  /**
   * Optional. The media type of the manifest, e.g.,
   * "application/vnd.docker.distribution.manifest.v2+json"
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
   * Optional. The operating system of the image. Values are provided by the
   * Docker client and are not validated by Artifact Registry. Example values
   * include "linux", "windows", "darwin", "aix", etc.
   *
   * @param string $os
   */
  public function setOs($os)
  {
    $this->os = $os;
  }
  /**
   * @return string
   */
  public function getOs()
  {
    return $this->os;
  }
  /**
   * Optional. The required OS features for the image, for example on Windows
   * `win32k`.
   *
   * @param string[] $osFeatures
   */
  public function setOsFeatures($osFeatures)
  {
    $this->osFeatures = $osFeatures;
  }
  /**
   * @return string[]
   */
  public function getOsFeatures()
  {
    return $this->osFeatures;
  }
  /**
   * Optional. The OS version of the image, for example on Windows
   * `10.0.14393.1066`.
   *
   * @param string $osVersion
   */
  public function setOsVersion($osVersion)
  {
    $this->osVersion = $osVersion;
  }
  /**
   * @return string
   */
  public function getOsVersion()
  {
    return $this->osVersion;
  }
  /**
   * Optional. The variant of the CPU in the image, for example `v7` to specify
   * ARMv7 when architecture is `arm`.
   *
   * @param string $variant
   */
  public function setVariant($variant)
  {
    $this->variant = $variant;
  }
  /**
   * @return string
   */
  public function getVariant()
  {
    return $this->variant;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageManifest::class, 'Google_Service_ArtifactRegistry_ImageManifest');
