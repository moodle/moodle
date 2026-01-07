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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoFunctionType extends \Google\Model
{
  protected $baseFunctionType = EnterpriseCrmEventbusProtoBaseFunction::class;
  protected $baseFunctionDataType = '';
  protected $booleanArrayFunctionType = EnterpriseCrmEventbusProtoBooleanArrayFunction::class;
  protected $booleanArrayFunctionDataType = '';
  protected $booleanFunctionType = EnterpriseCrmEventbusProtoBooleanFunction::class;
  protected $booleanFunctionDataType = '';
  protected $doubleArrayFunctionType = EnterpriseCrmEventbusProtoDoubleArrayFunction::class;
  protected $doubleArrayFunctionDataType = '';
  protected $doubleFunctionType = EnterpriseCrmEventbusProtoDoubleFunction::class;
  protected $doubleFunctionDataType = '';
  protected $intArrayFunctionType = EnterpriseCrmEventbusProtoIntArrayFunction::class;
  protected $intArrayFunctionDataType = '';
  protected $intFunctionType = EnterpriseCrmEventbusProtoIntFunction::class;
  protected $intFunctionDataType = '';
  protected $jsonFunctionType = EnterpriseCrmEventbusProtoJsonFunction::class;
  protected $jsonFunctionDataType = '';
  protected $protoArrayFunctionType = EnterpriseCrmEventbusProtoProtoArrayFunction::class;
  protected $protoArrayFunctionDataType = '';
  protected $protoFunctionType = EnterpriseCrmEventbusProtoProtoFunction::class;
  protected $protoFunctionDataType = '';
  protected $stringArrayFunctionType = EnterpriseCrmEventbusProtoStringArrayFunction::class;
  protected $stringArrayFunctionDataType = '';
  protected $stringFunctionType = EnterpriseCrmEventbusProtoStringFunction::class;
  protected $stringFunctionDataType = '';

  /**
   * LINT.IfChange
   *
   * @param EnterpriseCrmEventbusProtoBaseFunction $baseFunction
   */
  public function setBaseFunction(EnterpriseCrmEventbusProtoBaseFunction $baseFunction)
  {
    $this->baseFunction = $baseFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoBaseFunction
   */
  public function getBaseFunction()
  {
    return $this->baseFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoBooleanArrayFunction $booleanArrayFunction
   */
  public function setBooleanArrayFunction(EnterpriseCrmEventbusProtoBooleanArrayFunction $booleanArrayFunction)
  {
    $this->booleanArrayFunction = $booleanArrayFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoBooleanArrayFunction
   */
  public function getBooleanArrayFunction()
  {
    return $this->booleanArrayFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoBooleanFunction $booleanFunction
   */
  public function setBooleanFunction(EnterpriseCrmEventbusProtoBooleanFunction $booleanFunction)
  {
    $this->booleanFunction = $booleanFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoBooleanFunction
   */
  public function getBooleanFunction()
  {
    return $this->booleanFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoDoubleArrayFunction $doubleArrayFunction
   */
  public function setDoubleArrayFunction(EnterpriseCrmEventbusProtoDoubleArrayFunction $doubleArrayFunction)
  {
    $this->doubleArrayFunction = $doubleArrayFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoDoubleArrayFunction
   */
  public function getDoubleArrayFunction()
  {
    return $this->doubleArrayFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoDoubleFunction $doubleFunction
   */
  public function setDoubleFunction(EnterpriseCrmEventbusProtoDoubleFunction $doubleFunction)
  {
    $this->doubleFunction = $doubleFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoDoubleFunction
   */
  public function getDoubleFunction()
  {
    return $this->doubleFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoIntArrayFunction $intArrayFunction
   */
  public function setIntArrayFunction(EnterpriseCrmEventbusProtoIntArrayFunction $intArrayFunction)
  {
    $this->intArrayFunction = $intArrayFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoIntArrayFunction
   */
  public function getIntArrayFunction()
  {
    return $this->intArrayFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoIntFunction $intFunction
   */
  public function setIntFunction(EnterpriseCrmEventbusProtoIntFunction $intFunction)
  {
    $this->intFunction = $intFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoIntFunction
   */
  public function getIntFunction()
  {
    return $this->intFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoJsonFunction $jsonFunction
   */
  public function setJsonFunction(EnterpriseCrmEventbusProtoJsonFunction $jsonFunction)
  {
    $this->jsonFunction = $jsonFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoJsonFunction
   */
  public function getJsonFunction()
  {
    return $this->jsonFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoProtoArrayFunction $protoArrayFunction
   */
  public function setProtoArrayFunction(EnterpriseCrmEventbusProtoProtoArrayFunction $protoArrayFunction)
  {
    $this->protoArrayFunction = $protoArrayFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoProtoArrayFunction
   */
  public function getProtoArrayFunction()
  {
    return $this->protoArrayFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoProtoFunction $protoFunction
   */
  public function setProtoFunction(EnterpriseCrmEventbusProtoProtoFunction $protoFunction)
  {
    $this->protoFunction = $protoFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoProtoFunction
   */
  public function getProtoFunction()
  {
    return $this->protoFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoStringArrayFunction $stringArrayFunction
   */
  public function setStringArrayFunction(EnterpriseCrmEventbusProtoStringArrayFunction $stringArrayFunction)
  {
    $this->stringArrayFunction = $stringArrayFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoStringArrayFunction
   */
  public function getStringArrayFunction()
  {
    return $this->stringArrayFunction;
  }
  /**
   * @param EnterpriseCrmEventbusProtoStringFunction $stringFunction
   */
  public function setStringFunction(EnterpriseCrmEventbusProtoStringFunction $stringFunction)
  {
    $this->stringFunction = $stringFunction;
  }
  /**
   * @return EnterpriseCrmEventbusProtoStringFunction
   */
  public function getStringFunction()
  {
    return $this->stringFunction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoFunctionType::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoFunctionType');
