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

namespace Google\Service\Logging;

class SourceLocation extends \Google\Model
{
  /**
   * Source file name. Depending on the runtime environment, this might be a
   * simple name or a fully-qualified name.
   *
   * @var string
   */
  public $file;
  /**
   * Human-readable name of the function or method being invoked, with optional
   * context such as the class or package name. This information is used in
   * contexts such as the logs viewer, where a file and line number are less
   * meaningful. The format can vary by language. For example:
   * qual.if.ied.Class.method (Java), dir/package.func (Go), function (Python).
   *
   * @var string
   */
  public $functionName;
  /**
   * Line within the source file.
   *
   * @var string
   */
  public $line;

  /**
   * Source file name. Depending on the runtime environment, this might be a
   * simple name or a fully-qualified name.
   *
   * @param string $file
   */
  public function setFile($file)
  {
    $this->file = $file;
  }
  /**
   * @return string
   */
  public function getFile()
  {
    return $this->file;
  }
  /**
   * Human-readable name of the function or method being invoked, with optional
   * context such as the class or package name. This information is used in
   * contexts such as the logs viewer, where a file and line number are less
   * meaningful. The format can vary by language. For example:
   * qual.if.ied.Class.method (Java), dir/package.func (Go), function (Python).
   *
   * @param string $functionName
   */
  public function setFunctionName($functionName)
  {
    $this->functionName = $functionName;
  }
  /**
   * @return string
   */
  public function getFunctionName()
  {
    return $this->functionName;
  }
  /**
   * Line within the source file.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceLocation::class, 'Google_Service_Logging_SourceLocation');
