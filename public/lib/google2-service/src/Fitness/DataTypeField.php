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

namespace Google\Service\Fitness;

class DataTypeField extends \Google\Model
{
  public const FORMAT_integer = 'integer';
  public const FORMAT_floatPoint = 'floatPoint';
  public const FORMAT_string = 'string';
  public const FORMAT_map = 'map';
  public const FORMAT_integerList = 'integerList';
  public const FORMAT_floatList = 'floatList';
  public const FORMAT_blob = 'blob';
  /**
   * The different supported formats for each field in a data type.
   *
   * @var string
   */
  public $format;
  /**
   * Defines the name and format of data. Unlike data type names, field names
   * are not namespaced, and only need to be unique within the data type.
   *
   * @var string
   */
  public $name;
  /**
   * @var bool
   */
  public $optional;

  /**
   * The different supported formats for each field in a data type.
   *
   * Accepted values: integer, floatPoint, string, map, integerList, floatList,
   * blob
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Defines the name and format of data. Unlike data type names, field names
   * are not namespaced, and only need to be unique within the data type.
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
   * @param bool $optional
   */
  public function setOptional($optional)
  {
    $this->optional = $optional;
  }
  /**
   * @return bool
   */
  public function getOptional()
  {
    return $this->optional;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataTypeField::class, 'Google_Service_Fitness_DataTypeField');
