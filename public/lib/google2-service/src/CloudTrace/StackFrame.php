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

namespace Google\Service\CloudTrace;

class StackFrame extends \Google\Model
{
  /**
   * The column number where the function call appears, if available. This is
   * important in JavaScript because of its anonymous functions.
   *
   * @var string
   */
  public $columnNumber;
  protected $fileNameType = TruncatableString::class;
  protected $fileNameDataType = '';
  protected $functionNameType = TruncatableString::class;
  protected $functionNameDataType = '';
  /**
   * The line number in `file_name` where the function call appears.
   *
   * @var string
   */
  public $lineNumber;
  protected $loadModuleType = Module::class;
  protected $loadModuleDataType = '';
  protected $originalFunctionNameType = TruncatableString::class;
  protected $originalFunctionNameDataType = '';
  protected $sourceVersionType = TruncatableString::class;
  protected $sourceVersionDataType = '';

  /**
   * The column number where the function call appears, if available. This is
   * important in JavaScript because of its anonymous functions.
   *
   * @param string $columnNumber
   */
  public function setColumnNumber($columnNumber)
  {
    $this->columnNumber = $columnNumber;
  }
  /**
   * @return string
   */
  public function getColumnNumber()
  {
    return $this->columnNumber;
  }
  /**
   * The name of the source file where the function call appears (up to 256
   * bytes).
   *
   * @param TruncatableString $fileName
   */
  public function setFileName(TruncatableString $fileName)
  {
    $this->fileName = $fileName;
  }
  /**
   * @return TruncatableString
   */
  public function getFileName()
  {
    return $this->fileName;
  }
  /**
   * The fully-qualified name that uniquely identifies the function or method
   * that is active in this frame (up to 1024 bytes).
   *
   * @param TruncatableString $functionName
   */
  public function setFunctionName(TruncatableString $functionName)
  {
    $this->functionName = $functionName;
  }
  /**
   * @return TruncatableString
   */
  public function getFunctionName()
  {
    return $this->functionName;
  }
  /**
   * The line number in `file_name` where the function call appears.
   *
   * @param string $lineNumber
   */
  public function setLineNumber($lineNumber)
  {
    $this->lineNumber = $lineNumber;
  }
  /**
   * @return string
   */
  public function getLineNumber()
  {
    return $this->lineNumber;
  }
  /**
   * The binary module from where the code was loaded.
   *
   * @param Module $loadModule
   */
  public function setLoadModule(Module $loadModule)
  {
    $this->loadModule = $loadModule;
  }
  /**
   * @return Module
   */
  public function getLoadModule()
  {
    return $this->loadModule;
  }
  /**
   * An un-mangled function name, if `function_name` is mangled. To get
   * information about name mangling, run [this
   * search](https://www.google.com/search?q=cxx+name+mangling). The name can be
   * fully-qualified (up to 1024 bytes).
   *
   * @param TruncatableString $originalFunctionName
   */
  public function setOriginalFunctionName(TruncatableString $originalFunctionName)
  {
    $this->originalFunctionName = $originalFunctionName;
  }
  /**
   * @return TruncatableString
   */
  public function getOriginalFunctionName()
  {
    return $this->originalFunctionName;
  }
  /**
   * The version of the deployed source code (up to 128 bytes).
   *
   * @param TruncatableString $sourceVersion
   */
  public function setSourceVersion(TruncatableString $sourceVersion)
  {
    $this->sourceVersion = $sourceVersion;
  }
  /**
   * @return TruncatableString
   */
  public function getSourceVersion()
  {
    return $this->sourceVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StackFrame::class, 'Google_Service_CloudTrace_StackFrame');
