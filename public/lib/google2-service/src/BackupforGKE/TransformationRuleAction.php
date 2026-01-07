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

namespace Google\Service\BackupforGKE;

class TransformationRuleAction extends \Google\Model
{
  /**
   * Unspecified operation
   */
  public const OP_OP_UNSPECIFIED = 'OP_UNSPECIFIED';
  /**
   * The "remove" operation removes the value at the target location.
   */
  public const OP_REMOVE = 'REMOVE';
  /**
   * The "move" operation removes the value at a specified location and adds it
   * to the target location.
   */
  public const OP_MOVE = 'MOVE';
  /**
   * The "copy" operation copies the value at a specified location to the target
   * location.
   */
  public const OP_COPY = 'COPY';
  /**
   * The "add" operation performs one of the following functions, depending upon
   * what the target location references: 1. If the target location specifies an
   * array index, a new value is inserted into the array at the specified index.
   * 2. If the target location specifies an object member that does not already
   * exist, a new member is added to the object. 3. If the target location
   * specifies an object member that does exist, that member's value is
   * replaced.
   */
  public const OP_ADD = 'ADD';
  /**
   * The "test" operation tests that a value at the target location is equal to
   * a specified value.
   */
  public const OP_TEST = 'TEST';
  /**
   * The "replace" operation replaces the value at the target location with a
   * new value. The operation object MUST contain a "value" member whose content
   * specifies the replacement value.
   */
  public const OP_REPLACE = 'REPLACE';
  /**
   * Optional. A string containing a JSON Pointer value that references the
   * location in the target document to move the value from.
   *
   * @var string
   */
  public $fromPath;
  /**
   * Required. op specifies the operation to perform.
   *
   * @var string
   */
  public $op;
  /**
   * Optional. A string containing a JSON-Pointer value that references a
   * location within the target document where the operation is performed.
   *
   * @var string
   */
  public $path;
  /**
   * Optional. A string that specifies the desired value in string format to use
   * for transformation.
   *
   * @var string
   */
  public $value;

  /**
   * Optional. A string containing a JSON Pointer value that references the
   * location in the target document to move the value from.
   *
   * @param string $fromPath
   */
  public function setFromPath($fromPath)
  {
    $this->fromPath = $fromPath;
  }
  /**
   * @return string
   */
  public function getFromPath()
  {
    return $this->fromPath;
  }
  /**
   * Required. op specifies the operation to perform.
   *
   * Accepted values: OP_UNSPECIFIED, REMOVE, MOVE, COPY, ADD, TEST, REPLACE
   *
   * @param self::OP_* $op
   */
  public function setOp($op)
  {
    $this->op = $op;
  }
  /**
   * @return self::OP_*
   */
  public function getOp()
  {
    return $this->op;
  }
  /**
   * Optional. A string containing a JSON-Pointer value that references a
   * location within the target document where the operation is performed.
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
   * Optional. A string that specifies the desired value in string format to use
   * for transformation.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransformationRuleAction::class, 'Google_Service_BackupforGKE_TransformationRuleAction');
