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

namespace Google\Service\Dfareporting;

class FeedField extends \Google\Model
{
  /**
   * The type is unspecified. This is an unused value.
   */
  public const TYPE_TYPE_UNKNOWN = 'TYPE_UNKNOWN';
  /**
   * The field type is text.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * The field type is whole number.
   */
  public const TYPE_LONG = 'LONG';
  /**
   * The field type is image url
   */
  public const TYPE_GPA_SERVED_IMAGE_URL = 'GPA_SERVED_IMAGE_URL';
  /**
   * The field type is asset url.
   */
  public const TYPE_GPA_SERVED_ASSET_URL = 'GPA_SERVED_ASSET_URL';
  /**
   * The field type is the ISO 3166-2 alpha-2 codes. It is two-letter country
   * codes defined in ISO 3166-1 published by the International Organization for
   * Standardization.
   */
  public const TYPE_COUNTRY_CODE_ISO = 'COUNTRY_CODE_ISO';
  /**
   * The field type is decimal.
   */
  public const TYPE_FLOAT = 'FLOAT';
  /**
   * The field type is custom CM360 ad tag parameter.
   */
  public const TYPE_CM360_KEYWORD = 'CM360_KEYWORD';
  /**
   * The field type is CM360 site ID.
   */
  public const TYPE_CM360_SITE_ID = 'CM360_SITE_ID';
  /**
   * The field type is boolean.
   */
  public const TYPE_BOOL = 'BOOL';
  /**
   * The field type is exit url.
   */
  public const TYPE_EXIT_URL = 'EXIT_URL';
  /**
   * The field type is datetime.
   */
  public const TYPE_DATETIME = 'DATETIME';
  /**
   * The field type is CM360 creative ID.
   */
  public const TYPE_CM360_CREATIVE_ID = 'CM360_CREATIVE_ID';
  /**
   * The field type is CM360 placement ID.
   */
  public const TYPE_CM360_PLACEMENT_ID = 'CM360_PLACEMENT_ID';
  /**
   * The field type is CM360 ad ID.
   */
  public const TYPE_CM360_AD_ID = 'CM360_AD_ID';
  /**
   * The field type is CM360 advertiser ID.
   */
  public const TYPE_CM360_ADVERTISER_ID = 'CM360_ADVERTISER_ID';
  /**
   * The field type is CM360 campaign ID.
   */
  public const TYPE_CM360_CAMPAIGN_ID = 'CM360_CAMPAIGN_ID';
  /**
   * The field type is cities.
   */
  public const TYPE_CITY = 'CITY';
  /**
   * The field type is region.
   */
  public const TYPE_REGION = 'REGION';
  /**
   * The field type is postal code.
   */
  public const TYPE_POSTAL_CODE = 'POSTAL_CODE';
  /**
   * The field type is metro code.
   */
  public const TYPE_METRO = 'METRO';
  /**
   * The field type is custom value.
   */
  public const TYPE_CUSTOM_VALUE = 'CUSTOM_VALUE';
  /**
   * The field type is remarketing value.
   */
  public const TYPE_REMARKETING_VALUE = 'REMARKETING_VALUE';
  /**
   * The field type is accurate geographic type.
   */
  public const TYPE_GEO_CANONICAL = 'GEO_CANONICAL';
  /**
   * The field type is weight.
   */
  public const TYPE_WEIGHT = 'WEIGHT';
  /**
   * The field type is a list of values.
   */
  public const TYPE_STRING_LIST = 'STRING_LIST';
  /**
   * The field type is creative dimension.
   */
  public const TYPE_CREATIVE_DIMENSION = 'CREATIVE_DIMENSION';
  /**
   * The field type is CM/DV360 Audience ID.
   */
  public const TYPE_USERLIST_ID = 'USERLIST_ID';
  /**
   * The field type is AssetLibrary directory path.
   */
  public const TYPE_ASSET_LIBRARY_DIRECTORY_HANDLE = 'ASSET_LIBRARY_DIRECTORY_HANDLE';
  /**
   * The field type is AssetLibrary video file path.
   */
  public const TYPE_ASSET_LIBRARY_VIDEO_HANDLE = 'ASSET_LIBRARY_VIDEO_HANDLE';
  /**
   * The field type is AssetLibrary path.
   */
  public const TYPE_ASSET_LIBRARY_HANDLE = 'ASSET_LIBRARY_HANDLE';
  /**
   * The field type is third party served url.
   */
  public const TYPE_THIRD_PARTY_SERVED_URL = 'THIRD_PARTY_SERVED_URL';
  /**
   * The field type is CM dynamic targeting key.
   */
  public const TYPE_CM360_DYNAMIC_TARGETING_KEY = 'CM360_DYNAMIC_TARGETING_KEY';
  /**
   * The field type is DV360 line item ID.
   */
  public const TYPE_DV360_LINE_ITEM_ID = 'DV360_LINE_ITEM_ID';
  /**
   * Optional. The default value of the field.
   *
   * @var string
   */
  public $defaultValue;
  /**
   * Optional. Whether the field is filterable. Could be set as true when the
   * field type is any of the following and is not renderable: - STRING - BOOL -
   * COUNTRY_CODE_ISO - CM360_SITE_ID - CM360_KEYWORD - CM360_CREATIVE_ID -
   * CM360_PLACEMENT_ID - CM360_AD_ID - CM360_ADVERTISER_ID - CM360_CAMPAIGN_ID
   * - CITY - REGION - POSTAL_CODE - METRO - CUSTOM_VALUE - REMARKETING_VALUE -
   * GEO_CANONICAL - STRING_LIST - CREATIVE_DIMENSION - USERLIST_ID -
   * CM360_DYNAMIC_TARGETING_KEY - DV360_LINE_ITEM_ID
   *
   * @var bool
   */
  public $filterable;
  /**
   * Required. The ID of the field. The ID is based on the column index starting
   * from 0, and it should match the column index in the resource link.
   *
   * @var int
   */
  public $id;
  /**
   * Required. The name of the field.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Whether the field is able to display. Could be set as true when
   * the field type is not in any of the following and the field is not
   * filterable: - COUNTRY_CODE_ISO - CITY - REGION - POSTAL_CODE - METRO -
   * GEO_CANONICAL - USERLIST_ID - CONTEXTUAL_KEYWORD -
   * CM360_DYNAMIC_TARGETING_KEY - WEIGHT
   *
   * @var bool
   */
  public $renderable;
  /**
   * Optional. Whether the field is required and should not be empty in the
   * feed. Could be set as true when the field type is any of the following: -
   * GPA_SERVED_IMAGE_URL - GPA_SERVED_ASSET_URL - ASSET_LIBRARY_HANDLE -
   * ASSET_LIBRARY_VIDEO_HANDLE - ASSET_LIBRARY_DIRECTORY_HANDLE
   *
   * @var bool
   */
  public $required;
  /**
   * Required. The type of the field.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The default value of the field.
   *
   * @param string $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return string
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Optional. Whether the field is filterable. Could be set as true when the
   * field type is any of the following and is not renderable: - STRING - BOOL -
   * COUNTRY_CODE_ISO - CM360_SITE_ID - CM360_KEYWORD - CM360_CREATIVE_ID -
   * CM360_PLACEMENT_ID - CM360_AD_ID - CM360_ADVERTISER_ID - CM360_CAMPAIGN_ID
   * - CITY - REGION - POSTAL_CODE - METRO - CUSTOM_VALUE - REMARKETING_VALUE -
   * GEO_CANONICAL - STRING_LIST - CREATIVE_DIMENSION - USERLIST_ID -
   * CM360_DYNAMIC_TARGETING_KEY - DV360_LINE_ITEM_ID
   *
   * @param bool $filterable
   */
  public function setFilterable($filterable)
  {
    $this->filterable = $filterable;
  }
  /**
   * @return bool
   */
  public function getFilterable()
  {
    return $this->filterable;
  }
  /**
   * Required. The ID of the field. The ID is based on the column index starting
   * from 0, and it should match the column index in the resource link.
   *
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. The name of the field.
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
   * Optional. Whether the field is able to display. Could be set as true when
   * the field type is not in any of the following and the field is not
   * filterable: - COUNTRY_CODE_ISO - CITY - REGION - POSTAL_CODE - METRO -
   * GEO_CANONICAL - USERLIST_ID - CONTEXTUAL_KEYWORD -
   * CM360_DYNAMIC_TARGETING_KEY - WEIGHT
   *
   * @param bool $renderable
   */
  public function setRenderable($renderable)
  {
    $this->renderable = $renderable;
  }
  /**
   * @return bool
   */
  public function getRenderable()
  {
    return $this->renderable;
  }
  /**
   * Optional. Whether the field is required and should not be empty in the
   * feed. Could be set as true when the field type is any of the following: -
   * GPA_SERVED_IMAGE_URL - GPA_SERVED_ASSET_URL - ASSET_LIBRARY_HANDLE -
   * ASSET_LIBRARY_VIDEO_HANDLE - ASSET_LIBRARY_DIRECTORY_HANDLE
   *
   * @param bool $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return bool
   */
  public function getRequired()
  {
    return $this->required;
  }
  /**
   * Required. The type of the field.
   *
   * Accepted values: TYPE_UNKNOWN, STRING, LONG, GPA_SERVED_IMAGE_URL,
   * GPA_SERVED_ASSET_URL, COUNTRY_CODE_ISO, FLOAT, CM360_KEYWORD,
   * CM360_SITE_ID, BOOL, EXIT_URL, DATETIME, CM360_CREATIVE_ID,
   * CM360_PLACEMENT_ID, CM360_AD_ID, CM360_ADVERTISER_ID, CM360_CAMPAIGN_ID,
   * CITY, REGION, POSTAL_CODE, METRO, CUSTOM_VALUE, REMARKETING_VALUE,
   * GEO_CANONICAL, WEIGHT, STRING_LIST, CREATIVE_DIMENSION, USERLIST_ID,
   * ASSET_LIBRARY_DIRECTORY_HANDLE, ASSET_LIBRARY_VIDEO_HANDLE,
   * ASSET_LIBRARY_HANDLE, THIRD_PARTY_SERVED_URL, CM360_DYNAMIC_TARGETING_KEY,
   * DV360_LINE_ITEM_ID
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
class_alias(FeedField::class, 'Google_Service_Dfareporting_FeedField');
