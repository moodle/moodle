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

namespace Google\Service\AndroidEnterprise;

class AppRestrictionsSchemaRestriction extends \Google\Collection
{
  /**
   * A restriction of boolean type.
   */
  public const RESTRICTION_TYPE_bool = 'bool';
  /**
   * A restriction of string type.
   */
  public const RESTRICTION_TYPE_string = 'string';
  /**
   * A restriction of integer type.
   */
  public const RESTRICTION_TYPE_integer = 'integer';
  /**
   * A choice of one item from a set.
   */
  public const RESTRICTION_TYPE_choice = 'choice';
  /**
   * A choice of multiple items from a set.
   */
  public const RESTRICTION_TYPE_multiselect = 'multiselect';
  /**
   * A hidden restriction of string type (the default value can be used to pass
   * along information that cannot be modified, such as a version code).
   */
  public const RESTRICTION_TYPE_hidden = 'hidden';
  /**
   * [M+ devices only] A bundle of restrictions
   */
  public const RESTRICTION_TYPE_bundle = 'bundle';
  /**
   * [M+ devices only] An array of restriction bundles
   */
  public const RESTRICTION_TYPE_bundleArray = 'bundleArray';
  protected $collection_key = 'nestedRestriction';
  protected $defaultValueType = AppRestrictionsSchemaRestrictionRestrictionValue::class;
  protected $defaultValueDataType = '';
  /**
   * A longer description of the restriction, giving more detail of what it
   * affects.
   *
   * @var string
   */
  public $description;
  /**
   * For choice or multiselect restrictions, the list of possible entries'
   * human-readable names.
   *
   * @var string[]
   */
  public $entry;
  /**
   * For choice or multiselect restrictions, the list of possible entries'
   * machine-readable values. These values should be used in the configuration,
   * either as a single string value for a choice restriction or in a
   * stringArray for a multiselect restriction.
   *
   * @var string[]
   */
  public $entryValue;
  /**
   * The unique key that the product uses to identify the restriction, e.g.
   * "com.google.android.gm.fieldname".
   *
   * @var string
   */
  public $key;
  protected $nestedRestrictionType = AppRestrictionsSchemaRestriction::class;
  protected $nestedRestrictionDataType = 'array';
  /**
   * The type of the restriction.
   *
   * @var string
   */
  public $restrictionType;
  /**
   * The name of the restriction.
   *
   * @var string
   */
  public $title;

  /**
   * The default value of the restriction. bundle and bundleArray restrictions
   * never have a default value.
   *
   * @param AppRestrictionsSchemaRestrictionRestrictionValue $defaultValue
   */
  public function setDefaultValue(AppRestrictionsSchemaRestrictionRestrictionValue $defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return AppRestrictionsSchemaRestrictionRestrictionValue
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * A longer description of the restriction, giving more detail of what it
   * affects.
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
   * For choice or multiselect restrictions, the list of possible entries'
   * human-readable names.
   *
   * @param string[] $entry
   */
  public function setEntry($entry)
  {
    $this->entry = $entry;
  }
  /**
   * @return string[]
   */
  public function getEntry()
  {
    return $this->entry;
  }
  /**
   * For choice or multiselect restrictions, the list of possible entries'
   * machine-readable values. These values should be used in the configuration,
   * either as a single string value for a choice restriction or in a
   * stringArray for a multiselect restriction.
   *
   * @param string[] $entryValue
   */
  public function setEntryValue($entryValue)
  {
    $this->entryValue = $entryValue;
  }
  /**
   * @return string[]
   */
  public function getEntryValue()
  {
    return $this->entryValue;
  }
  /**
   * The unique key that the product uses to identify the restriction, e.g.
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
   * For bundle or bundleArray restrictions, the list of nested restrictions. A
   * bundle restriction is always nested within a bundleArray restriction, and a
   * bundleArray restriction is at most two levels deep.
   *
   * @param AppRestrictionsSchemaRestriction[] $nestedRestriction
   */
  public function setNestedRestriction($nestedRestriction)
  {
    $this->nestedRestriction = $nestedRestriction;
  }
  /**
   * @return AppRestrictionsSchemaRestriction[]
   */
  public function getNestedRestriction()
  {
    return $this->nestedRestriction;
  }
  /**
   * The type of the restriction.
   *
   * Accepted values: bool, string, integer, choice, multiselect, hidden,
   * bundle, bundleArray
   *
   * @param self::RESTRICTION_TYPE_* $restrictionType
   */
  public function setRestrictionType($restrictionType)
  {
    $this->restrictionType = $restrictionType;
  }
  /**
   * @return self::RESTRICTION_TYPE_*
   */
  public function getRestrictionType()
  {
    return $this->restrictionType;
  }
  /**
   * The name of the restriction.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppRestrictionsSchemaRestriction::class, 'Google_Service_AndroidEnterprise_AppRestrictionsSchemaRestriction');
