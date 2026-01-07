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

namespace Google\Service\Testing;

class IosDeviceFile extends \Google\Model
{
  /**
   * The bundle id of the app where this file lives. iOS apps sandbox their own
   * filesystem, so app files must specify which app installed on the device.
   *
   * @var string
   */
  public $bundleId;
  protected $contentType = FileReference::class;
  protected $contentDataType = '';
  /**
   * Location of the file on the device, inside the app's sandboxed filesystem
   *
   * @var string
   */
  public $devicePath;

  /**
   * The bundle id of the app where this file lives. iOS apps sandbox their own
   * filesystem, so app files must specify which app installed on the device.
   *
   * @param string $bundleId
   */
  public function setBundleId($bundleId)
  {
    $this->bundleId = $bundleId;
  }
  /**
   * @return string
   */
  public function getBundleId()
  {
    return $this->bundleId;
  }
  /**
   * The source file
   *
   * @param FileReference $content
   */
  public function setContent(FileReference $content)
  {
    $this->content = $content;
  }
  /**
   * @return FileReference
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Location of the file on the device, inside the app's sandboxed filesystem
   *
   * @param string $devicePath
   */
  public function setDevicePath($devicePath)
  {
    $this->devicePath = $devicePath;
  }
  /**
   * @return string
   */
  public function getDevicePath()
  {
    return $this->devicePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IosDeviceFile::class, 'Google_Service_Testing_IosDeviceFile');
