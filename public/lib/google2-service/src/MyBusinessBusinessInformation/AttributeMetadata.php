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

namespace Google\Service\MyBusinessBusinessInformation;

class AttributeMetadata extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const VALUE_TYPE_ATTRIBUTE_VALUE_TYPE_UNSPECIFIED = 'ATTRIBUTE_VALUE_TYPE_UNSPECIFIED';
  /**
   * The values for this attribute are boolean values.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * The attribute has a predetermined list of available values that can be
   * used. Metadata for this attribute will list these values.
   */
  public const VALUE_TYPE_ENUM = 'ENUM';
  /**
   * The values for this attribute are URLs.
   */
  public const VALUE_TYPE_URL = 'URL';
  /**
   * The attribute value is an enum with multiple possible values that can be
   * explicitly set or unset.
   */
  public const VALUE_TYPE_REPEATED_ENUM = 'REPEATED_ENUM';
  protected $collection_key = 'valueMetadata';
  /**
   * If true, the attribute is deprecated and should no longer be used. If
   * deprecated, updating this attribute will not result in an error, but
   * updates will not be saved. At some point after being deprecated, the
   * attribute will be removed entirely and it will become an error.
   *
   * @var bool
   */
  public $deprecated;
  /**
   * The localized display name for the attribute, if available; otherwise, the
   * English display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The localized display name of the group that contains this attribute, if
   * available; otherwise, the English group name. Related attributes are
   * collected into a group and should be displayed together under the heading
   * given here.
   *
   * @var string
   */
  public $groupDisplayName;
  /**
   * The unique identifier for the attribute.
   *
   * @var string
   */
  public $parent;
  /**
   * If true, the attribute supports multiple values. If false, only a single
   * value should be provided.
   *
   * @var bool
   */
  public $repeatable;
  protected $valueMetadataType = AttributeValueMetadata::class;
  protected $valueMetadataDataType = 'array';
  /**
   * The value type for the attribute. Values set and retrieved should be
   * expected to be of this type.
   *
   * @var string
   */
  public $valueType;

  /**
   * If true, the attribute is deprecated and should no longer be used. If
   * deprecated, updating this attribute will not result in an error, but
   * updates will not be saved. At some point after being deprecated, the
   * attribute will be removed entirely and it will become an error.
   *
   * @param bool $deprecated
   */
  public function setDeprecated($deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return bool
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * The localized display name for the attribute, if available; otherwise, the
   * English display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The localized display name of the group that contains this attribute, if
   * available; otherwise, the English group name. Related attributes are
   * collected into a group and should be displayed together under the heading
   * given here.
   *
   * @param string $groupDisplayName
   */
  public function setGroupDisplayName($groupDisplayName)
  {
    $this->groupDisplayName = $groupDisplayName;
  }
  /**
   * @return string
   */
  public function getGroupDisplayName()
  {
    return $this->groupDisplayName;
  }
  /**
   * The unique identifier for the attribute.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * If true, the attribute supports multiple values. If false, only a single
   * value should be provided.
   *
   * @param bool $repeatable
   */
  public function setRepeatable($repeatable)
  {
    $this->repeatable = $repeatable;
  }
  /**
   * @return bool
   */
  public function getRepeatable()
  {
    return $this->repeatable;
  }
  /**
   * For some types of attributes (for example, enums), a list of supported
   * values and corresponding display names for those values is provided.
   *
   * @param AttributeValueMetadata[] $valueMetadata
   */
  public function setValueMetadata($valueMetadata)
  {
    $this->valueMetadata = $valueMetadata;
  }
  /**
   * @return AttributeValueMetadata[]
   */
  public function getValueMetadata()
  {
    return $this->valueMetadata;
  }
  /**
   * The value type for the attribute. Values set and retrieved should be
   * expected to be of this type.
   *
   * Accepted values: ATTRIBUTE_VALUE_TYPE_UNSPECIFIED, BOOL, ENUM, URL,
   * REPEATED_ENUM
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttributeMetadata::class, 'Google_Service_MyBusinessBusinessInformation_AttributeMetadata');
