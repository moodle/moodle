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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1EntityType extends \Google\Collection
{
  /**
   * Auto expansion disabled for the entity.
   */
  public const AUTO_EXPANSION_MODE_AUTO_EXPANSION_MODE_UNSPECIFIED = 'AUTO_EXPANSION_MODE_UNSPECIFIED';
  /**
   * Allows an agent to recognize values that have not been explicitly listed in
   * the entity.
   */
  public const AUTO_EXPANSION_MODE_AUTO_EXPANSION_MODE_DEFAULT = 'AUTO_EXPANSION_MODE_DEFAULT';
  /**
   * Not specified. This value should be never used.
   */
  public const KIND_KIND_UNSPECIFIED = 'KIND_UNSPECIFIED';
  /**
   * Map entity types allow mapping of a group of synonyms to a reference value.
   */
  public const KIND_KIND_MAP = 'KIND_MAP';
  /**
   * List entity types contain a set of entries that do not map to reference
   * values. However, list entity types can contain references to other entity
   * types (with or without aliases).
   */
  public const KIND_KIND_LIST = 'KIND_LIST';
  /**
   * Regexp entity types allow to specify regular expressions in entries values.
   */
  public const KIND_KIND_REGEXP = 'KIND_REGEXP';
  protected $collection_key = 'entities';
  /**
   * Optional. Indicates whether the entity type can be automatically expanded.
   *
   * @var string
   */
  public $autoExpansionMode;
  /**
   * Required. The name of the entity type.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Enables fuzzy entity extraction during classification.
   *
   * @var bool
   */
  public $enableFuzzyExtraction;
  protected $entitiesType = GoogleCloudDialogflowV2beta1EntityTypeEntity::class;
  protected $entitiesDataType = 'array';
  /**
   * Required. Indicates the kind of entity type.
   *
   * @var string
   */
  public $kind;
  /**
   * The unique identifier of the entity type. Required for
   * EntityTypes.UpdateEntityType and EntityTypes.BatchUpdateEntityTypes
   * methods. Supported formats: - `projects//agent/entityTypes/` -
   * `projects//locations//agent/entityTypes/`
   *
   * @var string
   */
  public $name;

  /**
   * Optional. Indicates whether the entity type can be automatically expanded.
   *
   * Accepted values: AUTO_EXPANSION_MODE_UNSPECIFIED,
   * AUTO_EXPANSION_MODE_DEFAULT
   *
   * @param self::AUTO_EXPANSION_MODE_* $autoExpansionMode
   */
  public function setAutoExpansionMode($autoExpansionMode)
  {
    $this->autoExpansionMode = $autoExpansionMode;
  }
  /**
   * @return self::AUTO_EXPANSION_MODE_*
   */
  public function getAutoExpansionMode()
  {
    return $this->autoExpansionMode;
  }
  /**
   * Required. The name of the entity type.
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
   * Optional. Enables fuzzy entity extraction during classification.
   *
   * @param bool $enableFuzzyExtraction
   */
  public function setEnableFuzzyExtraction($enableFuzzyExtraction)
  {
    $this->enableFuzzyExtraction = $enableFuzzyExtraction;
  }
  /**
   * @return bool
   */
  public function getEnableFuzzyExtraction()
  {
    return $this->enableFuzzyExtraction;
  }
  /**
   * Optional. The collection of entity entries associated with the entity type.
   *
   * @param GoogleCloudDialogflowV2beta1EntityTypeEntity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1EntityTypeEntity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * Required. Indicates the kind of entity type.
   *
   * Accepted values: KIND_UNSPECIFIED, KIND_MAP, KIND_LIST, KIND_REGEXP
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The unique identifier of the entity type. Required for
   * EntityTypes.UpdateEntityType and EntityTypes.BatchUpdateEntityTypes
   * methods. Supported formats: - `projects//agent/entityTypes/` -
   * `projects//locations//agent/entityTypes/`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1EntityType::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1EntityType');
