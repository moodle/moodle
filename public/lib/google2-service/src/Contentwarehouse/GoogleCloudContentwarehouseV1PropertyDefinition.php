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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1PropertyDefinition extends \Google\Collection
{
  /**
   * No importance specified. Default medium importance.
   */
  public const RETRIEVAL_IMPORTANCE_RETRIEVAL_IMPORTANCE_UNSPECIFIED = 'RETRIEVAL_IMPORTANCE_UNSPECIFIED';
  /**
   * Highest importance.
   */
  public const RETRIEVAL_IMPORTANCE_HIGHEST = 'HIGHEST';
  /**
   * Higher importance.
   */
  public const RETRIEVAL_IMPORTANCE_HIGHER = 'HIGHER';
  /**
   * High importance.
   */
  public const RETRIEVAL_IMPORTANCE_HIGH = 'HIGH';
  /**
   * Medium importance.
   */
  public const RETRIEVAL_IMPORTANCE_MEDIUM = 'MEDIUM';
  /**
   * Low importance (negative).
   */
  public const RETRIEVAL_IMPORTANCE_LOW = 'LOW';
  /**
   * Lowest importance (negative).
   */
  public const RETRIEVAL_IMPORTANCE_LOWEST = 'LOWEST';
  protected $collection_key = 'schemaSources';
  protected $dateTimeTypeOptionsType = GoogleCloudContentwarehouseV1DateTimeTypeOptions::class;
  protected $dateTimeTypeOptionsDataType = '';
  /**
   * The display-name for the property, used for front-end.
   *
   * @var string
   */
  public $displayName;
  protected $enumTypeOptionsType = GoogleCloudContentwarehouseV1EnumTypeOptions::class;
  protected $enumTypeOptionsDataType = '';
  protected $floatTypeOptionsType = GoogleCloudContentwarehouseV1FloatTypeOptions::class;
  protected $floatTypeOptionsDataType = '';
  protected $integerTypeOptionsType = GoogleCloudContentwarehouseV1IntegerTypeOptions::class;
  protected $integerTypeOptionsDataType = '';
  /**
   * Whether the property can be filtered. If this is a sub-property, all the
   * parent properties must be marked filterable.
   *
   * @var bool
   */
  public $isFilterable;
  /**
   * Whether the property is user supplied metadata. This out-of-the box
   * placeholder setting can be used to tag derived properties. Its value and
   * interpretation logic should be implemented by API user.
   *
   * @var bool
   */
  public $isMetadata;
  /**
   * Whether the property can have multiple values.
   *
   * @var bool
   */
  public $isRepeatable;
  /**
   * Whether the property is mandatory. Default is 'false', i.e. populating
   * property value can be skipped. If 'true' then user must populate the value
   * for this property.
   *
   * @var bool
   */
  public $isRequired;
  /**
   * Indicates that the property should be included in a global search.
   *
   * @var bool
   */
  public $isSearchable;
  protected $mapTypeOptionsType = GoogleCloudContentwarehouseV1MapTypeOptions::class;
  protected $mapTypeOptionsDataType = '';
  /**
   * Required. The name of the metadata property. Must be unique within a
   * document schema and is case insensitive. Names must be non-blank, start
   * with a letter, and can contain alphanumeric characters and: /, :, -, _, and
   * .
   *
   * @var string
   */
  public $name;
  protected $propertyTypeOptionsType = GoogleCloudContentwarehouseV1PropertyTypeOptions::class;
  protected $propertyTypeOptionsDataType = '';
  /**
   * The retrieval importance of the property during search.
   *
   * @var string
   */
  public $retrievalImportance;
  protected $schemaSourcesType = GoogleCloudContentwarehouseV1PropertyDefinitionSchemaSource::class;
  protected $schemaSourcesDataType = 'array';
  protected $textTypeOptionsType = GoogleCloudContentwarehouseV1TextTypeOptions::class;
  protected $textTypeOptionsDataType = '';
  protected $timestampTypeOptionsType = GoogleCloudContentwarehouseV1TimestampTypeOptions::class;
  protected $timestampTypeOptionsDataType = '';

  /**
   * Date time property. It is not supported by CMEK compliant deployment.
   *
   * @param GoogleCloudContentwarehouseV1DateTimeTypeOptions $dateTimeTypeOptions
   */
  public function setDateTimeTypeOptions(GoogleCloudContentwarehouseV1DateTimeTypeOptions $dateTimeTypeOptions)
  {
    $this->dateTimeTypeOptions = $dateTimeTypeOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1DateTimeTypeOptions
   */
  public function getDateTimeTypeOptions()
  {
    return $this->dateTimeTypeOptions;
  }
  /**
   * The display-name for the property, used for front-end.
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
   * Enum/categorical property.
   *
   * @param GoogleCloudContentwarehouseV1EnumTypeOptions $enumTypeOptions
   */
  public function setEnumTypeOptions(GoogleCloudContentwarehouseV1EnumTypeOptions $enumTypeOptions)
  {
    $this->enumTypeOptions = $enumTypeOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1EnumTypeOptions
   */
  public function getEnumTypeOptions()
  {
    return $this->enumTypeOptions;
  }
  /**
   * Float property.
   *
   * @param GoogleCloudContentwarehouseV1FloatTypeOptions $floatTypeOptions
   */
  public function setFloatTypeOptions(GoogleCloudContentwarehouseV1FloatTypeOptions $floatTypeOptions)
  {
    $this->floatTypeOptions = $floatTypeOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1FloatTypeOptions
   */
  public function getFloatTypeOptions()
  {
    return $this->floatTypeOptions;
  }
  /**
   * Integer property.
   *
   * @param GoogleCloudContentwarehouseV1IntegerTypeOptions $integerTypeOptions
   */
  public function setIntegerTypeOptions(GoogleCloudContentwarehouseV1IntegerTypeOptions $integerTypeOptions)
  {
    $this->integerTypeOptions = $integerTypeOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1IntegerTypeOptions
   */
  public function getIntegerTypeOptions()
  {
    return $this->integerTypeOptions;
  }
  /**
   * Whether the property can be filtered. If this is a sub-property, all the
   * parent properties must be marked filterable.
   *
   * @param bool $isFilterable
   */
  public function setIsFilterable($isFilterable)
  {
    $this->isFilterable = $isFilterable;
  }
  /**
   * @return bool
   */
  public function getIsFilterable()
  {
    return $this->isFilterable;
  }
  /**
   * Whether the property is user supplied metadata. This out-of-the box
   * placeholder setting can be used to tag derived properties. Its value and
   * interpretation logic should be implemented by API user.
   *
   * @param bool $isMetadata
   */
  public function setIsMetadata($isMetadata)
  {
    $this->isMetadata = $isMetadata;
  }
  /**
   * @return bool
   */
  public function getIsMetadata()
  {
    return $this->isMetadata;
  }
  /**
   * Whether the property can have multiple values.
   *
   * @param bool $isRepeatable
   */
  public function setIsRepeatable($isRepeatable)
  {
    $this->isRepeatable = $isRepeatable;
  }
  /**
   * @return bool
   */
  public function getIsRepeatable()
  {
    return $this->isRepeatable;
  }
  /**
   * Whether the property is mandatory. Default is 'false', i.e. populating
   * property value can be skipped. If 'true' then user must populate the value
   * for this property.
   *
   * @param bool $isRequired
   */
  public function setIsRequired($isRequired)
  {
    $this->isRequired = $isRequired;
  }
  /**
   * @return bool
   */
  public function getIsRequired()
  {
    return $this->isRequired;
  }
  /**
   * Indicates that the property should be included in a global search.
   *
   * @param bool $isSearchable
   */
  public function setIsSearchable($isSearchable)
  {
    $this->isSearchable = $isSearchable;
  }
  /**
   * @return bool
   */
  public function getIsSearchable()
  {
    return $this->isSearchable;
  }
  /**
   * Map property.
   *
   * @param GoogleCloudContentwarehouseV1MapTypeOptions $mapTypeOptions
   */
  public function setMapTypeOptions(GoogleCloudContentwarehouseV1MapTypeOptions $mapTypeOptions)
  {
    $this->mapTypeOptions = $mapTypeOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1MapTypeOptions
   */
  public function getMapTypeOptions()
  {
    return $this->mapTypeOptions;
  }
  /**
   * Required. The name of the metadata property. Must be unique within a
   * document schema and is case insensitive. Names must be non-blank, start
   * with a letter, and can contain alphanumeric characters and: /, :, -, _, and
   * .
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
   * Nested structured data property.
   *
   * @param GoogleCloudContentwarehouseV1PropertyTypeOptions $propertyTypeOptions
   */
  public function setPropertyTypeOptions(GoogleCloudContentwarehouseV1PropertyTypeOptions $propertyTypeOptions)
  {
    $this->propertyTypeOptions = $propertyTypeOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1PropertyTypeOptions
   */
  public function getPropertyTypeOptions()
  {
    return $this->propertyTypeOptions;
  }
  /**
   * The retrieval importance of the property during search.
   *
   * Accepted values: RETRIEVAL_IMPORTANCE_UNSPECIFIED, HIGHEST, HIGHER, HIGH,
   * MEDIUM, LOW, LOWEST
   *
   * @param self::RETRIEVAL_IMPORTANCE_* $retrievalImportance
   */
  public function setRetrievalImportance($retrievalImportance)
  {
    $this->retrievalImportance = $retrievalImportance;
  }
  /**
   * @return self::RETRIEVAL_IMPORTANCE_*
   */
  public function getRetrievalImportance()
  {
    return $this->retrievalImportance;
  }
  /**
   * The mapping information between this property to another schema source.
   *
   * @param GoogleCloudContentwarehouseV1PropertyDefinitionSchemaSource[] $schemaSources
   */
  public function setSchemaSources($schemaSources)
  {
    $this->schemaSources = $schemaSources;
  }
  /**
   * @return GoogleCloudContentwarehouseV1PropertyDefinitionSchemaSource[]
   */
  public function getSchemaSources()
  {
    return $this->schemaSources;
  }
  /**
   * Text/string property.
   *
   * @param GoogleCloudContentwarehouseV1TextTypeOptions $textTypeOptions
   */
  public function setTextTypeOptions(GoogleCloudContentwarehouseV1TextTypeOptions $textTypeOptions)
  {
    $this->textTypeOptions = $textTypeOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1TextTypeOptions
   */
  public function getTextTypeOptions()
  {
    return $this->textTypeOptions;
  }
  /**
   * Timestamp property. It is not supported by CMEK compliant deployment.
   *
   * @param GoogleCloudContentwarehouseV1TimestampTypeOptions $timestampTypeOptions
   */
  public function setTimestampTypeOptions(GoogleCloudContentwarehouseV1TimestampTypeOptions $timestampTypeOptions)
  {
    $this->timestampTypeOptions = $timestampTypeOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1TimestampTypeOptions
   */
  public function getTimestampTypeOptions()
  {
    return $this->timestampTypeOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1PropertyDefinition::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1PropertyDefinition');
