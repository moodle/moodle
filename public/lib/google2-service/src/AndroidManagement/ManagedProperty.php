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

namespace Google\Service\AndroidManagement;

class ManagedProperty extends \Google\Collection
{
  /**
   * Not used.
   */
  public const TYPE_MANAGED_PROPERTY_TYPE_UNSPECIFIED = 'MANAGED_PROPERTY_TYPE_UNSPECIFIED';
  /**
   * A property of boolean type.
   */
  public const TYPE_BOOL = 'BOOL';
  /**
   * A property of string type.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * A property of integer type.
   */
  public const TYPE_INTEGER = 'INTEGER';
  /**
   * A choice of one item from a set.
   */
  public const TYPE_CHOICE = 'CHOICE';
  /**
   * A choice of multiple items from a set.
   */
  public const TYPE_MULTISELECT = 'MULTISELECT';
  /**
   * A hidden restriction of string type (the default value can be used to pass
   * along information that can't be modified, such as a version code).
   */
  public const TYPE_HIDDEN = 'HIDDEN';
  /**
   * A bundle of properties
   */
  public const TYPE_BUNDLE = 'BUNDLE';
  /**
   * An array of property bundles.
   */
  public const TYPE_BUNDLE_ARRAY = 'BUNDLE_ARRAY';
  protected $collection_key = 'nestedProperties';
  /**
   * The default value of the property. BUNDLE_ARRAY properties don't have a
   * default value.
   *
   * @var array
   */
  public $defaultValue;
  /**
   * A longer description of the property, providing more detail of what it
   * affects. Localized.
   *
   * @var string
   */
  public $description;
  protected $entriesType = ManagedPropertyEntry::class;
  protected $entriesDataType = 'array';
  /**
   * The unique key that the app uses to identify the property, e.g.
   * "com.google.android.gm.fieldname".
   *
   * @var string
   */
  public $key;
  protected $nestedPropertiesType = ManagedProperty::class;
  protected $nestedPropertiesDataType = 'array';
  /**
   * The name of the property. Localized.
   *
   * @var string
   */
  public $title;
  /**
   * The type of the property.
   *
   * @var string
   */
  public $type;

  /**
   * The default value of the property. BUNDLE_ARRAY properties don't have a
   * default value.
   *
   * @param array $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return array
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * A longer description of the property, providing more detail of what it
   * affects. Localized.
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
   * For CHOICE or MULTISELECT properties, the list of possible entries.
   *
   * @param ManagedPropertyEntry[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return ManagedPropertyEntry[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
  /**
   * The unique key that the app uses to identify the property, e.g.
   * "com.google.android.gm.fieldname".
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
   * For BUNDLE_ARRAY properties, the list of nested properties. A BUNDLE_ARRAY
   * property is at most two levels deep.
   *
   * @param ManagedProperty[] $nestedProperties
   */
  public function setNestedProperties($nestedProperties)
  {
    $this->nestedProperties = $nestedProperties;
  }
  /**
   * @return ManagedProperty[]
   */
  public function getNestedProperties()
  {
    return $this->nestedProperties;
  }
  /**
   * The name of the property. Localized.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The type of the property.
   *
   * Accepted values: MANAGED_PROPERTY_TYPE_UNSPECIFIED, BOOL, STRING, INTEGER,
   * CHOICE, MULTISELECT, HIDDEN, BUNDLE, BUNDLE_ARRAY
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedProperty::class, 'Google_Service_AndroidManagement_ManagedProperty');
