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

namespace Google\Service\TagManager;

class Parameter extends \Google\Collection
{
  public const TYPE_typeUnspecified = 'typeUnspecified';
  /**
   * May include variable references.
   */
  public const TYPE_template = 'template';
  public const TYPE_integer = 'integer';
  public const TYPE_boolean = 'boolean';
  public const TYPE_list = 'list';
  public const TYPE_map = 'map';
  public const TYPE_triggerReference = 'triggerReference';
  public const TYPE_tagReference = 'tagReference';
  protected $collection_key = 'map';
  /**
   * Whether or not a reference type parameter is strongly or weakly referenced.
   * Only used by Transformations.
   *
   * @var bool
   */
  public $isWeakReference;
  /**
   * The named key that uniquely identifies a parameter. Required for top-level
   * parameters, as well as map values. Ignored for list values.
   *
   * @var string
   */
  public $key;
  protected $listType = Parameter::class;
  protected $listDataType = 'array';
  protected $mapType = Parameter::class;
  protected $mapDataType = 'array';
  /**
   * The parameter type. Valid values are: - boolean: The value represents a
   * boolean, represented as 'true' or 'false' - integer: The value represents a
   * 64-bit signed integer value, in base 10 - list: A list of parameters should
   * be specified - map: A map of parameters should be specified - template: The
   * value represents any text; this can include variable references (even
   * variable references that might return non-string types) -
   * trigger_reference: The value represents a trigger, represented as the
   * trigger id - tag_reference: The value represents a tag, represented as the
   * tag name
   *
   * @var string
   */
  public $type;
  /**
   * A parameter's value (may contain variable references). as appropriate to
   * the specified type.
   *
   * @var string
   */
  public $value;

  /**
   * Whether or not a reference type parameter is strongly or weakly referenced.
   * Only used by Transformations.
   *
   * @param bool $isWeakReference
   */
  public function setIsWeakReference($isWeakReference)
  {
    $this->isWeakReference = $isWeakReference;
  }
  /**
   * @return bool
   */
  public function getIsWeakReference()
  {
    return $this->isWeakReference;
  }
  /**
   * The named key that uniquely identifies a parameter. Required for top-level
   * parameters, as well as map values. Ignored for list values.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * This list parameter's parameters (keys will be ignored).
   *
   * @param Parameter[] $list
   */
  public function setList($list)
  {
    $this->list = $list;
  }
  /**
   * @return Parameter[]
   */
  public function getList()
  {
    return $this->list;
  }
  /**
   * This map parameter's parameters (must have keys; keys must be unique).
   *
   * @param Parameter[] $map
   */
  public function setMap($map)
  {
    $this->map = $map;
  }
  /**
   * @return Parameter[]
   */
  public function getMap()
  {
    return $this->map;
  }
  /**
   * The parameter type. Valid values are: - boolean: The value represents a
   * boolean, represented as 'true' or 'false' - integer: The value represents a
   * 64-bit signed integer value, in base 10 - list: A list of parameters should
   * be specified - map: A map of parameters should be specified - template: The
   * value represents any text; this can include variable references (even
   * variable references that might return non-string types) -
   * trigger_reference: The value represents a trigger, represented as the
   * trigger id - tag_reference: The value represents a tag, represented as the
   * tag name
   *
   * Accepted values: typeUnspecified, template, integer, boolean, list, map,
   * triggerReference, tagReference
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * A parameter's value (may contain variable references). as appropriate to
   * the specified type.
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
class_alias(Parameter::class, 'Google_Service_TagManager_Parameter');
