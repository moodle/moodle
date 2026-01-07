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

namespace Google\Service\Clouderrorreporting;

class SourceLocation extends \Google\Model
{
  /**
   * The source code filename, which can include a truncated relative path, or a
   * full path from a production machine.
   *
   * @var string
   */
  public $filePath;
  /**
   * Human-readable name of a function or method. The value can include optional
   * context like the class or package name. For example,
   * `my.package.MyClass.method` in case of Java.
   *
   * @var string
   */
  public $functionName;
  /**
   * 1-based. 0 indicates that the line number is unknown.
   *
   * @var int
   */
  public $lineNumber;

  /**
   * The source code filename, which can include a truncated relative path, or a
   * full path from a production machine.
   *
   * @param string $filePath
   */
  public function setFilePath($filePath)
  {
    $this->filePath = $filePath;
  }
  /**
   * @return string
   */
  public function getFilePath()
  {
    return $this->filePath;
  }
  /**
   * Human-readable name of a function or method. The value can include optional
   * context like the class or package name. For example,
   * `my.package.MyClass.method` in case of Java.
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
   * 1-based. 0 indicates that the line number is unknown.
   *
   * @param int $lineNumber
   */
  public function setLineNumber($lineNumber)
  {
    $this->lineNumber = $lineNumber;
  }
  /**
   * @return int
   */
  public function getLineNumber()
  {
    return $this->lineNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceLocation::class, 'Google_Service_Clouderrorreporting_SourceLocation');
