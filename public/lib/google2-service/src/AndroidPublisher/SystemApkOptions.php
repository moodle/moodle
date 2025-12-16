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

namespace Google\Service\AndroidPublisher;

class SystemApkOptions extends \Google\Model
{
  /**
   * Whether to use the rotated key for signing the system APK.
   *
   * @var bool
   */
  public $rotated;
  /**
   * Whether system APK was generated with uncompressed dex files.
   *
   * @var bool
   */
  public $uncompressedDexFiles;
  /**
   * Whether system APK was generated with uncompressed native libraries.
   *
   * @var bool
   */
  public $uncompressedNativeLibraries;

  /**
   * Whether to use the rotated key for signing the system APK.
   *
   * @param bool $rotated
   */
  public function setRotated($rotated)
  {
    $this->rotated = $rotated;
  }
  /**
   * @return bool
   */
  public function getRotated()
  {
    return $this->rotated;
  }
  /**
   * Whether system APK was generated with uncompressed dex files.
   *
   * @param bool $uncompressedDexFiles
   */
  public function setUncompressedDexFiles($uncompressedDexFiles)
  {
    $this->uncompressedDexFiles = $uncompressedDexFiles;
  }
  /**
   * @return bool
   */
  public function getUncompressedDexFiles()
  {
    return $this->uncompressedDexFiles;
  }
  /**
   * Whether system APK was generated with uncompressed native libraries.
   *
   * @param bool $uncompressedNativeLibraries
   */
  public function setUncompressedNativeLibraries($uncompressedNativeLibraries)
  {
    $this->uncompressedNativeLibraries = $uncompressedNativeLibraries;
  }
  /**
   * @return bool
   */
  public function getUncompressedNativeLibraries()
  {
    return $this->uncompressedNativeLibraries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SystemApkOptions::class, 'Google_Service_AndroidPublisher_SystemApkOptions');
