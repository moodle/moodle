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

namespace Google\Service\Compute;

class Screenshot extends \Google\Model
{
  /**
   * [Output Only] The Base64-encoded screenshot data.
   *
   * @var string
   */
  public $contents;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#screenshot
   * for the screenshots.
   *
   * @var string
   */
  public $kind;

  /**
   * [Output Only] The Base64-encoded screenshot data.
   *
   * @param string $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return string
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always compute#screenshot
   * for the screenshots.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Screenshot::class, 'Google_Service_Compute_Screenshot');
