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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1PathParam extends \Google\Model
{
  /**
   * Unspecified data type
   */
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Boolean data type
   */
  public const DATA_TYPE_BOOL = 'BOOL';
  /**
   * Integer data type
   */
  public const DATA_TYPE_INTEGER = 'INTEGER';
  /**
   * Float data type
   */
  public const DATA_TYPE_FLOAT = 'FLOAT';
  /**
   * String data type
   */
  public const DATA_TYPE_STRING = 'STRING';
  /**
   * UUID data type
   */
  public const DATA_TYPE_UUID = 'UUID';
  /**
   * Optional. Data type of path param
   *
   * @var string
   */
  public $dataType;
  /**
   * Optional. Segment location in the path, 1-indexed
   *
   * @var int
   */
  public $position;

  /**
   * Optional. Data type of path param
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, BOOL, INTEGER, FLOAT, STRING, UUID
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Optional. Segment location in the path, 1-indexed
   *
   * @param int $position
   */
  public function setPosition($position)
  {
    $this->position = $position;
  }
  /**
   * @return int
   */
  public function getPosition()
  {
    return $this->position;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1PathParam::class, 'Google_Service_APIhub_GoogleCloudApihubV1PathParam');
