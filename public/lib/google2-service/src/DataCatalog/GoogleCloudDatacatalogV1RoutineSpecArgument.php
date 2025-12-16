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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1RoutineSpecArgument extends \Google\Model
{
  /**
   * Unspecified mode.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * The argument is input-only.
   */
  public const MODE_IN = 'IN';
  /**
   * The argument is output-only.
   */
  public const MODE_OUT = 'OUT';
  /**
   * The argument is both an input and an output.
   */
  public const MODE_INOUT = 'INOUT';
  /**
   * Specifies whether the argument is input or output.
   *
   * @var string
   */
  public $mode;
  /**
   * The name of the argument. A return argument of a function might not have a
   * name.
   *
   * @var string
   */
  public $name;
  /**
   * Type of the argument. The exact value depends on the source system and the
   * language.
   *
   * @var string
   */
  public $type;

  /**
   * Specifies whether the argument is input or output.
   *
   * Accepted values: MODE_UNSPECIFIED, IN, OUT, INOUT
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * The name of the argument. A return argument of a function might not have a
   * name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Type of the argument. The exact value depends on the source system and the
   * language.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1RoutineSpecArgument::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1RoutineSpecArgument');
