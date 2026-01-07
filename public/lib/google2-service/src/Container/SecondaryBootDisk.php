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

namespace Google\Service\Container;

class SecondaryBootDisk extends \Google\Model
{
  /**
   * MODE_UNSPECIFIED is when mode is not set.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * CONTAINER_IMAGE_CACHE is for using the secondary boot disk as a container
   * image cache.
   */
  public const MODE_CONTAINER_IMAGE_CACHE = 'CONTAINER_IMAGE_CACHE';
  /**
   * Fully-qualified resource ID for an existing disk image.
   *
   * @var string
   */
  public $diskImage;
  /**
   * Disk mode (container image cache, etc.)
   *
   * @var string
   */
  public $mode;

  /**
   * Fully-qualified resource ID for an existing disk image.
   *
   * @param string $diskImage
   */
  public function setDiskImage($diskImage)
  {
    $this->diskImage = $diskImage;
  }
  /**
   * @return string
   */
  public function getDiskImage()
  {
    return $this->diskImage;
  }
  /**
   * Disk mode (container image cache, etc.)
   *
   * Accepted values: MODE_UNSPECIFIED, CONTAINER_IMAGE_CACHE
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecondaryBootDisk::class, 'Google_Service_Container_SecondaryBootDisk');
