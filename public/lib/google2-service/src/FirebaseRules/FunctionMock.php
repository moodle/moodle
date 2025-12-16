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

namespace Google\Service\FirebaseRules;

class FunctionMock extends \Google\Collection
{
  protected $collection_key = 'args';
  protected $argsType = Arg::class;
  protected $argsDataType = 'array';
  /**
   * The name of the function. The function name must match one provided by a
   * service declaration.
   *
   * @var string
   */
  public $function;
  protected $resultType = Result::class;
  protected $resultDataType = '';

  /**
   * The list of `Arg` values to match. The order in which the arguments are
   * provided is the order in which they must appear in the function invocation.
   *
   * @param Arg[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return Arg[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * The name of the function. The function name must match one provided by a
   * service declaration.
   *
   * @param string $function
   */
  public function setFunction($function)
  {
    $this->function = $function;
  }
  /**
   * @return string
   */
  public function getFunction()
  {
    return $this->function;
  }
  /**
   * The mock result of the function call.
   *
   * @param Result $result
   */
  public function setResult(Result $result)
  {
    $this->result = $result;
  }
  /**
   * @return Result
   */
  public function getResult()
  {
    return $this->result;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FunctionMock::class, 'Google_Service_FirebaseRules_FunctionMock');
