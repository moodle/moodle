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

class PackageVersion extends \Google\Collection
{
  protected $collection_key = 'licenses';
  /**
   * The licenses associated with this package. Note that this has to go on the
   * PackageVersion level, because we can have cases with images with the same
   * source having different licences. E.g. in Alpine, musl and musl-utils both
   * have the same origin musl, but have different sets of licenses.
   *
   * @var string[]
   */
  public $licenses;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $version;

  /**
   * The licenses associated with this package. Note that this has to go on the
   * PackageVersion level, because we can have cases with images with the same
   * source having different licences. E.g. in Alpine, musl and musl-utils both
   * have the same origin musl, but have different sets of licenses.
   *
   * @param string[] $licenses
   */
  public function setLicenses($licenses)
  {
    $this->licenses = $licenses;
  }
  /**
   * @return string[]
   */
  public function getLicenses()
  {
    return $this->licenses;
  }
  /**
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
class_alias(PackageVersion::class, 'Google_Service_OnDemandScanning_PackageVersion');
