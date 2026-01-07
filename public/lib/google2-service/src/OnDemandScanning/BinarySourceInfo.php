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

class BinarySourceInfo extends \Google\Model
{
  protected $binaryVersionType = PackageVersion::class;
  protected $binaryVersionDataType = '';
  protected $sourceVersionType = PackageVersion::class;
  protected $sourceVersionDataType = '';

  /**
   * The binary package. This is significant when the source is different than
   * the binary itself. Historically if they've differed, we've stored the name
   * of the source and its version in the package/version fields, but we should
   * also store the binary package info, as that's what's actually installed.
   *
   * @param PackageVersion $binaryVersion
   */
  public function setBinaryVersion(PackageVersion $binaryVersion)
  {
    $this->binaryVersion = $binaryVersion;
  }
  /**
   * @return PackageVersion
   */
  public function getBinaryVersion()
  {
    return $this->binaryVersion;
  }
  /**
   * The source package. Similar to the above, this is significant when the
   * source is different than the binary itself. Since the top-level
   * package/version fields are based on an if/else, we need a separate field
   * for both binary and source if we want to know definitively where the data
   * is coming from.
   *
   * @param PackageVersion $sourceVersion
   */
  public function setSourceVersion(PackageVersion $sourceVersion)
  {
    $this->sourceVersion = $sourceVersion;
  }
  /**
   * @return PackageVersion
   */
  public function getSourceVersion()
  {
    return $this->sourceVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BinarySourceInfo::class, 'Google_Service_OnDemandScanning_BinarySourceInfo');
