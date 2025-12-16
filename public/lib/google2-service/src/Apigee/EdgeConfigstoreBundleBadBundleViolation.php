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

namespace Google\Service\Apigee;

class EdgeConfigstoreBundleBadBundleViolation extends \Google\Model
{
  /**
   * A description of why the bundle is invalid and how to fix it.
   *
   * @var string
   */
  public $description;
  /**
   * The filename (including relative path from the bundle root) in which the
   * error occurred.
   *
   * @var string
   */
  public $filename;

  /**
   * A description of why the bundle is invalid and how to fix it.
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
   * The filename (including relative path from the bundle root) in which the
   * error occurred.
   *
   * @param string $filename
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }
  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EdgeConfigstoreBundleBadBundleViolation::class, 'Google_Service_Apigee_EdgeConfigstoreBundleBadBundleViolation');
