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

namespace Google\Service\CloudHealthcare;

class Type extends \Google\Collection
{
  /**
   * Not a primitive.
   */
  public const PRIMITIVE_PRIMITIVE_UNSPECIFIED = 'PRIMITIVE_UNSPECIFIED';
  /**
   * String primitive.
   */
  public const PRIMITIVE_STRING = 'STRING';
  /**
   * Element that can have unschematized children.
   */
  public const PRIMITIVE_VARIES = 'VARIES';
  /**
   * Like STRING, but all delimiters below this element are ignored.
   */
  public const PRIMITIVE_UNESCAPED_STRING = 'UNESCAPED_STRING';
  protected $collection_key = 'fields';
  protected $fieldsType = Field::class;
  protected $fieldsDataType = 'array';
  /**
   * The name of this type. This would be the segment or datatype name. For
   * example, "PID" or "XPN".
   *
   * @var string
   */
  public $name;
  /**
   * If this is a primitive type then this field is the type of the primitive
   * For example, STRING. Leave unspecified for composite types.
   *
   * @var string
   */
  public $primitive;

  /**
   * The (sub) fields this type has (if not primitive).
   *
   * @param Field[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return Field[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * The name of this type. This would be the segment or datatype name. For
   * example, "PID" or "XPN".
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
   * If this is a primitive type then this field is the type of the primitive
   * For example, STRING. Leave unspecified for composite types.
   *
   * Accepted values: PRIMITIVE_UNSPECIFIED, STRING, VARIES, UNESCAPED_STRING
   *
   * @param self::PRIMITIVE_* $primitive
   */
  public function setPrimitive($primitive)
  {
    $this->primitive = $primitive;
  }
  /**
   * @return self::PRIMITIVE_*
   */
  public function getPrimitive()
  {
    return $this->primitive;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Type::class, 'Google_Service_CloudHealthcare_Type');
