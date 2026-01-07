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

namespace Google\Service\AnalyticsHub;

class Routine extends \Google\Model
{
  /**
   * Default value.
   */
  public const ROUTINE_TYPE_ROUTINE_TYPE_UNSPECIFIED = 'ROUTINE_TYPE_UNSPECIFIED';
  /**
   * Non-built-in persistent TVF.
   */
  public const ROUTINE_TYPE_TABLE_VALUED_FUNCTION = 'TABLE_VALUED_FUNCTION';
  /**
   * Optional. The definition body of the routine.
   *
   * @var string
   */
  public $definitionBody;
  /**
   * Required. The type of routine.
   *
   * @var string
   */
  public $routineType;

  /**
   * Optional. The definition body of the routine.
   *
   * @param string $definitionBody
   */
  public function setDefinitionBody($definitionBody)
  {
    $this->definitionBody = $definitionBody;
  }
  /**
   * @return string
   */
  public function getDefinitionBody()
  {
    return $this->definitionBody;
  }
  /**
   * Required. The type of routine.
   *
   * Accepted values: ROUTINE_TYPE_UNSPECIFIED, TABLE_VALUED_FUNCTION
   *
   * @param self::ROUTINE_TYPE_* $routineType
   */
  public function setRoutineType($routineType)
  {
    $this->routineType = $routineType;
  }
  /**
   * @return self::ROUTINE_TYPE_*
   */
  public function getRoutineType()
  {
    return $this->routineType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Routine::class, 'Google_Service_AnalyticsHub_Routine');
