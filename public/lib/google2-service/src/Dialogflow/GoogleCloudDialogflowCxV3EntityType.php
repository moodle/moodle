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

class GoogleCloudDialogflowCxV3EntityType extends \Google\Collection
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
   * Map entity types allow mapping of a group of synonyms to a canonical value.
   */
  public const KIND_KIND_MAP = 'KIND_MAP';
  /**
   * List entity types contain a set of entries that do not map to canonical
   * values. However, list entity types can contain references to other entity
   * types (with or without aliases).
   */
  public const KIND_KIND_LIST = 'KIND_LIST';
  /**
   * Regexp entity types allow to specify regular expressions in entries values.
   */
  public const KIND_KIND_REGEXP = 'KIND_REGEXP';
  protected $collection_key = 'excludedPhrases';
  /**
   * Indicates whether the entity type can be automatically expanded.
   *
   * @var string
   */
  public $autoExpansionMode;
  /**
   * Required. The human-readable name of the entity type, unique within the
   * agent.
   *
   * @var string
   */
  public $displayName;
  /**
   * Enables fuzzy entity extraction during classification.
   *
   * @var bool
   */
  public $enableFuzzyExtraction;
  protected $entitiesType = GoogleCloudDialogflowCxV3EntityTypeEntity::class;
  protected $entitiesDataType = 'array';
  protected $excludedPhrasesType = GoogleCloudDialogflowCxV3EntityTypeExcludedPhrase::class;
  protected $excludedPhrasesDataType = 'array';
  /**
   * Required. Indicates the kind of entity type.
   *
   * @var string
   */
  public $kind;
  /**
   * The unique identifier of the entity type. Required for
   * EntityTypes.UpdateEntityType. Format:
   * `projects//locations//agents//entityTypes/`.
   *
   * @var string
   */
  public $name;
  /**
   * Indicates whether parameters of the entity type should be redacted in log.
   * If redaction is enabled, page parameters and intent parameters referring to
   * the entity type will be replaced by parameter name when logging.
   *
   * @var bool
   */
  public $redact;

  /**
   * Indicates whether the entity type can be automatically expanded.
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
   * Required. The human-readable name of the entity type, unique within the
   * agent.
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
   * Enables fuzzy entity extraction during classification.
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
   * The collection of entity entries associated with the entity type.
   *
   * @param GoogleCloudDialogflowCxV3EntityTypeEntity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return GoogleCloudDialogflowCxV3EntityTypeEntity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * Collection of exceptional words and phrases that shouldn't be matched. For
   * example, if you have a size entity type with entry `giant`(an adjective),
   * you might consider adding `giants`(a noun) as an exclusion. If the kind of
   * entity type is `KIND_MAP`, then the phrases specified by entities and
   * excluded phrases should be mutually exclusive.
   *
   * @param GoogleCloudDialogflowCxV3EntityTypeExcludedPhrase[] $excludedPhrases
   */
  public function setExcludedPhrases($excludedPhrases)
  {
    $this->excludedPhrases = $excludedPhrases;
  }
  /**
   * @return GoogleCloudDialogflowCxV3EntityTypeExcludedPhrase[]
   */
  public function getExcludedPhrases()
  {
    return $this->excludedPhrases;
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
   * EntityTypes.UpdateEntityType. Format:
   * `projects//locations//agents//entityTypes/`.
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
   * Indicates whether parameters of the entity type should be redacted in log.
   * If redaction is enabled, page parameters and intent parameters referring to
   * the entity type will be replaced by parameter name when logging.
   *
   * @param bool $redact
   */
  public function setRedact($redact)
  {
    $this->redact = $redact;
  }
  /**
   * @return bool
   */
  public function getRedact()
  {
    return $this->redact;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3EntityType::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3EntityType');
