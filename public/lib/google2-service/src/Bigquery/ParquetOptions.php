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

class ParquetOptions extends \Google\Model
{
  /**
   * In this mode, the map will have the following schema: struct map_field_name
   * { repeated struct key_value { key value } }.
   */
  public const MAP_TARGET_TYPE_MAP_TARGET_TYPE_UNSPECIFIED = 'MAP_TARGET_TYPE_UNSPECIFIED';
  /**
   * In this mode, the map will have the following schema: repeated struct
   * map_field_name { key value }.
   */
  public const MAP_TARGET_TYPE_ARRAY_OF_STRUCT = 'ARRAY_OF_STRUCT';
  /**
   * Optional. Indicates whether to use schema inference specifically for
   * Parquet LIST logical type.
   *
   * @var bool
   */
  public $enableListInference;
  /**
   * Optional. Indicates whether to infer Parquet ENUM logical type as STRING
   * instead of BYTES by default.
   *
   * @var bool
   */
  public $enumAsString;
  /**
   * Optional. Indicates how to represent a Parquet map if present.
   *
   * @var string
   */
  public $mapTargetType;

  /**
   * Optional. Indicates whether to use schema inference specifically for
   * Parquet LIST logical type.
   *
   * @param bool $enableListInference
   */
  public function setEnableListInference($enableListInference)
  {
    $this->enableListInference = $enableListInference;
  }
  /**
   * @return bool
   */
  public function getEnableListInference()
  {
    return $this->enableListInference;
  }
  /**
   * Optional. Indicates whether to infer Parquet ENUM logical type as STRING
   * instead of BYTES by default.
   *
   * @param bool $enumAsString
   */
  public function setEnumAsString($enumAsString)
  {
    $this->enumAsString = $enumAsString;
  }
  /**
   * @return bool
   */
  public function getEnumAsString()
  {
    return $this->enumAsString;
  }
  /**
   * Optional. Indicates how to represent a Parquet map if present.
   *
   * Accepted values: MAP_TARGET_TYPE_UNSPECIFIED, ARRAY_OF_STRUCT
   *
   * @param self::MAP_TARGET_TYPE_* $mapTargetType
   */
  public function setMapTargetType($mapTargetType)
  {
    $this->mapTargetType = $mapTargetType;
  }
  /**
   * @return self::MAP_TARGET_TYPE_*
   */
  public function getMapTargetType()
  {
    return $this->mapTargetType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParquetOptions::class, 'Google_Service_Bigquery_ParquetOptions');
