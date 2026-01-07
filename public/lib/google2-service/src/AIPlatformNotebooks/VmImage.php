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

class VmImage extends \Google\Model
{
  /**
   * Optional. Use this VM image family to find the image; the newest image in
   * this family will be used.
   *
   * @var string
   */
  public $family;
  /**
   * Optional. Use VM image name to find the image.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The name of the Google Cloud project that this VM image belongs
   * to. Format: `{project_id}`
   *
   * @var string
   */
  public $project;

  /**
   * Optional. Use this VM image family to find the image; the newest image in
   * this family will be used.
   *
   * @param string $family
   */
  public function setFamily($family)
  {
    $this->family = $family;
  }
  /**
   * @return string
   */
  public function getFamily()
  {
    return $this->family;
  }
  /**
   * Optional. Use VM image name to find the image.
   *
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
   * Required. The name of the Google Cloud project that this VM image belongs
   * to. Format: `{project_id}`
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmImage::class, 'Google_Service_AIPlatformNotebooks_VmImage');
