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

class GeneratedAssetPackSlice extends \Google\Model
{
  /**
   * Download ID, which uniquely identifies the APK to download. Should be
   * supplied to `generatedapks.download` method.
   *
   * @var string
   */
  public $downloadId;
  /**
   * Name of the module that this asset slice belongs to.
   *
   * @var string
   */
  public $moduleName;
  /**
   * Asset slice ID.
   *
   * @var string
   */
  public $sliceId;
  /**
   * Asset module version.
   *
   * @var string
   */
  public $version;

  /**
   * Download ID, which uniquely identifies the APK to download. Should be
   * supplied to `generatedapks.download` method.
   *
   * @param string $downloadId
   */
  public function setDownloadId($downloadId)
  {
    $this->downloadId = $downloadId;
  }
  /**
   * @return string
   */
  public function getDownloadId()
  {
    return $this->downloadId;
  }
  /**
   * Name of the module that this asset slice belongs to.
   *
   * @param string $moduleName
   */
  public function setModuleName($moduleName)
  {
    $this->moduleName = $moduleName;
  }
  /**
   * @return string
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }
  /**
   * Asset slice ID.
   *
   * @param string $sliceId
   */
  public function setSliceId($sliceId)
  {
    $this->sliceId = $sliceId;
  }
  /**
   * @return string
   */
  public function getSliceId()
  {
    return $this->sliceId;
  }
  /**
   * Asset module version.
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
class_alias(GeneratedAssetPackSlice::class, 'Google_Service_AndroidPublisher_GeneratedAssetPackSlice');
