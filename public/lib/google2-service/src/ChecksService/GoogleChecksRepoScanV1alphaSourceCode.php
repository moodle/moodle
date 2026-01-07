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

namespace Google\Service\ChecksService;

class GoogleChecksRepoScanV1alphaSourceCode extends \Google\Model
{
  /**
   * Required. Source code.
   *
   * @var string
   */
  public $code;
  /**
   * Required. End line number (1-based).
   *
   * @var int
   */
  public $endLine;
  /**
   * Required. Path of the file.
   *
   * @var string
   */
  public $path;
  /**
   * Required. Start line number (1-based).
   *
   * @var int
   */
  public $startLine;

  /**
   * Required. Source code.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Required. End line number (1-based).
   *
   * @param int $endLine
   */
  public function setEndLine($endLine)
  {
    $this->endLine = $endLine;
  }
  /**
   * @return int
   */
  public function getEndLine()
  {
    return $this->endLine;
  }
  /**
   * Required. Path of the file.
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
  /**
   * Required. Start line number (1-based).
   *
   * @param int $startLine
   */
  public function setStartLine($startLine)
  {
    $this->startLine = $startLine;
  }
  /**
   * @return int
   */
  public function getStartLine()
  {
    return $this->startLine;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksRepoScanV1alphaSourceCode::class, 'Google_Service_ChecksService_GoogleChecksRepoScanV1alphaSourceCode');
