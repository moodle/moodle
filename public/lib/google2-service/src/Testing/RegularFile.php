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

class RegularFile extends \Google\Model
{
  protected $contentType = FileReference::class;
  protected $contentDataType = '';
  /**
   * Required. Where to put the content on the device. Must be an absolute,
   * allowlisted path. If the file exists, it will be replaced. The following
   * device-side directories and any of their subdirectories are allowlisted:
   * ${EXTERNAL_STORAGE}, /sdcard ${ANDROID_DATA}/local/tmp, or /data/local/tmp
   * Specifying a path outside of these directory trees is invalid. The paths
   * /sdcard and /data will be made available and treated as implicit path
   * substitutions. E.g. if /sdcard on a particular device does not map to
   * external storage, the system will replace it with the external storage path
   * prefix for that device and copy the file there. It is strongly advised to
   * use the Environment API in app and test code to access files on the device
   * in a portable way.
   *
   * @var string
   */
  public $devicePath;

  /**
   * Required. The source file.
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
   * Required. Where to put the content on the device. Must be an absolute,
   * allowlisted path. If the file exists, it will be replaced. The following
   * device-side directories and any of their subdirectories are allowlisted:
   * ${EXTERNAL_STORAGE}, /sdcard ${ANDROID_DATA}/local/tmp, or /data/local/tmp
   * Specifying a path outside of these directory trees is invalid. The paths
   * /sdcard and /data will be made available and treated as implicit path
   * substitutions. E.g. if /sdcard on a particular device does not map to
   * external storage, the system will replace it with the external storage path
   * prefix for that device and copy the file there. It is strongly advised to
   * use the Environment API in app and test code to access files on the device
   * in a portable way.
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
class_alias(RegularFile::class, 'Google_Service_Testing_RegularFile');
