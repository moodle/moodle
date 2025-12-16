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

namespace Google\Service\VMMigrationService;

class DiskImageDefaults extends \Google\Model
{
  /**
   * Required. The Image resource used when creating the disk.
   *
   * @var string
   */
  public $sourceImage;

  /**
   * Required. The Image resource used when creating the disk.
   *
   * @param string $sourceImage
   */
  public function setSourceImage($sourceImage)
  {
    $this->sourceImage = $sourceImage;
  }
  /**
   * @return string
   */
  public function getSourceImage()
  {
    return $this->sourceImage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskImageDefaults::class, 'Google_Service_VMMigrationService_DiskImageDefaults');
