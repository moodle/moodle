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

namespace Google\Service\SecureSourceManager;

class Position extends \Google\Model
{
  /**
   * Required. The line number of the comment. Positive value means it's on the
   * new side of the diff, negative value means it's on the old side.
   *
   * @var string
   */
  public $line;
  /**
   * Required. The path of the file.
   *
   * @var string
   */
  public $path;

  /**
   * Required. The line number of the comment. Positive value means it's on the
   * new side of the diff, negative value means it's on the old side.
   *
   * @param string $line
   */
  public function setLine($line)
  {
    $this->line = $line;
  }
  /**
   * @return string
   */
  public function getLine()
  {
    return $this->line;
  }
  /**
   * Required. The path of the file.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Position::class, 'Google_Service_SecureSourceManager_Position');
