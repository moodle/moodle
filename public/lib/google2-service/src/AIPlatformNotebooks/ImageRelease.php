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

namespace Google\Service\AIPlatformNotebooks;

class ImageRelease extends \Google\Model
{
  /**
   * Output only. The name of the image of the form workbench-instances-
   * vYYYYmmdd--
   *
   * @var string
   */
  public $imageName;
  /**
   * Output only. The release of the image of the form m123
   *
   * @var string
   */
  public $releaseName;

  /**
   * Output only. The name of the image of the form workbench-instances-
   * vYYYYmmdd--
   *
   * @param string $imageName
   */
  public function setImageName($imageName)
  {
    $this->imageName = $imageName;
  }
  /**
   * @return string
   */
  public function getImageName()
  {
    return $this->imageName;
  }
  /**
   * Output only. The release of the image of the form m123
   *
   * @param string $releaseName
   */
  public function setReleaseName($releaseName)
  {
    $this->releaseName = $releaseName;
  }
  /**
   * @return string
   */
  public function getReleaseName()
  {
    return $this->releaseName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageRelease::class, 'Google_Service_AIPlatformNotebooks_ImageRelease');
