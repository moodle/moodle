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

namespace Google\Service\SecurityCommandCenter;

class CustomModuleValidationError extends \Google\Model
{
  /**
   * A description of the error, suitable for human consumption. Required.
   *
   * @var string
   */
  public $description;
  protected $endType = Position::class;
  protected $endDataType = '';
  /**
   * The path, in RFC 8901 JSON Pointer format, to the field that failed
   * validation. This may be left empty if no specific field is affected.
   *
   * @var string
   */
  public $fieldPath;
  protected $startType = Position::class;
  protected $startDataType = '';

  /**
   * A description of the error, suitable for human consumption. Required.
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
   * The end position of the error in the uploaded text version of the module.
   * This field may be omitted if no specific position applies, or if one could
   * not be computed.
   *
   * @param Position $end
   */
  public function setEnd(Position $end)
  {
    $this->end = $end;
  }
  /**
   * @return Position
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * The path, in RFC 8901 JSON Pointer format, to the field that failed
   * validation. This may be left empty if no specific field is affected.
   *
   * @param string $fieldPath
   */
  public function setFieldPath($fieldPath)
  {
    $this->fieldPath = $fieldPath;
  }
  /**
   * @return string
   */
  public function getFieldPath()
  {
    return $this->fieldPath;
  }
  /**
   * The initial position of the error in the uploaded text version of the
   * module. This field may be omitted if no specific position applies, or if
   * one could not be computed.
   *
   * @param Position $start
   */
  public function setStart(Position $start)
  {
    $this->start = $start;
  }
  /**
   * @return Position
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomModuleValidationError::class, 'Google_Service_SecurityCommandCenter_CustomModuleValidationError');
