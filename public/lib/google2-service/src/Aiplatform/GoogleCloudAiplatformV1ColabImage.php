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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ColabImage extends \Google\Model
{
  /**
   * Output only. A human-readable description of the specified colab image
   * release, populated by the system. Example: "Python 3.10", "Latest - current
   * Python 3.11"
   *
   * @var string
   */
  public $description;
  /**
   * Optional. The release name of the NotebookRuntime Colab image, e.g.
   * "py310". If not specified, detault to the latest release.
   *
   * @var string
   */
  public $releaseName;

  /**
   * Output only. A human-readable description of the specified colab image
   * release, populated by the system. Example: "Python 3.10", "Latest - current
   * Python 3.11"
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. The release name of the NotebookRuntime Colab image, e.g.
   * "py310". If not specified, detault to the latest release.
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
class_alias(GoogleCloudAiplatformV1ColabImage::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ColabImage');
