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

namespace Google\Service\Bigquery;

class ForeignTypeInfo extends \Google\Model
{
  /**
   * TypeSystem not specified.
   */
  public const TYPE_SYSTEM_TYPE_SYSTEM_UNSPECIFIED = 'TYPE_SYSTEM_UNSPECIFIED';
  /**
   * Represents Hive data types.
   */
  public const TYPE_SYSTEM_HIVE = 'HIVE';
  /**
   * Required. Specifies the system which defines the foreign data type.
   *
   * @var string
   */
  public $typeSystem;

  /**
   * Required. Specifies the system which defines the foreign data type.
   *
   * Accepted values: TYPE_SYSTEM_UNSPECIFIED, HIVE
   *
   * @param self::TYPE_SYSTEM_* $typeSystem
   */
  public function setTypeSystem($typeSystem)
  {
    $this->typeSystem = $typeSystem;
  }
  /**
   * @return self::TYPE_SYSTEM_*
   */
  public function getTypeSystem()
  {
    return $this->typeSystem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ForeignTypeInfo::class, 'Google_Service_Bigquery_ForeignTypeInfo');
