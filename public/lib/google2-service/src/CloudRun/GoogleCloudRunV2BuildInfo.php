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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2BuildInfo extends \Google\Model
{
  /**
   * Output only. Entry point of the function when the image is a Cloud Run
   * function.
   *
   * @var string
   */
  public $functionTarget;
  /**
   * Output only. Source code location of the image.
   *
   * @var string
   */
  public $sourceLocation;

  /**
   * Output only. Entry point of the function when the image is a Cloud Run
   * function.
   *
   * @param string $functionTarget
   */
  public function setFunctionTarget($functionTarget)
  {
    $this->functionTarget = $functionTarget;
  }
  /**
   * @return string
   */
  public function getFunctionTarget()
  {
    return $this->functionTarget;
  }
  /**
   * Output only. Source code location of the image.
   *
   * @param string $sourceLocation
   */
  public function setSourceLocation($sourceLocation)
  {
    $this->sourceLocation = $sourceLocation;
  }
  /**
   * @return string
   */
  public function getSourceLocation()
  {
    return $this->sourceLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2BuildInfo::class, 'Google_Service_CloudRun_GoogleCloudRunV2BuildInfo');
