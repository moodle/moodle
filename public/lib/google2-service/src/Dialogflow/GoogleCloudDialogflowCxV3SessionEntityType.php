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

class GoogleCloudDialogflowCxV3SessionEntityType extends \Google\Collection
{
  /**
   * Not specified. This value should be never used.
   */
  public const ENTITY_OVERRIDE_MODE_ENTITY_OVERRIDE_MODE_UNSPECIFIED = 'ENTITY_OVERRIDE_MODE_UNSPECIFIED';
  /**
   * The collection of session entities overrides the collection of entities in
   * the corresponding custom entity type.
   */
  public const ENTITY_OVERRIDE_MODE_ENTITY_OVERRIDE_MODE_OVERRIDE = 'ENTITY_OVERRIDE_MODE_OVERRIDE';
  /**
   * The collection of session entities extends the collection of entities in
   * the corresponding custom entity type. Note: Even in this override mode
   * calls to `ListSessionEntityTypes`, `GetSessionEntityType`,
   * `CreateSessionEntityType` and `UpdateSessionEntityType` only return the
   * additional entities added in this session entity type. If you want to get
   * the supplemented list, please call EntityTypes.GetEntityType on the custom
   * entity type and merge.
   */
  public const ENTITY_OVERRIDE_MODE_ENTITY_OVERRIDE_MODE_SUPPLEMENT = 'ENTITY_OVERRIDE_MODE_SUPPLEMENT';
  protected $collection_key = 'entities';
  protected $entitiesType = GoogleCloudDialogflowCxV3EntityTypeEntity::class;
  protected $entitiesDataType = 'array';
  /**
   * Required. Indicates whether the additional data should override or
   * supplement the custom entity type definition.
   *
   * @var string
   */
  public $entityOverrideMode;
  /**
   * Required. The unique identifier of the session entity type. Format:
   * `projects//locations//agents//sessions//entityTypes/` or
   * `projects//locations//agents//environments//sessions//entityTypes/`. If
   * `Environment ID` is not specified, we assume default 'draft' environment.
   *
   * @var string
   */
  public $name;

  /**
   * Required. The collection of entities to override or supplement the custom
   * entity type.
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
   * Required. Indicates whether the additional data should override or
   * supplement the custom entity type definition.
   *
   * Accepted values: ENTITY_OVERRIDE_MODE_UNSPECIFIED,
   * ENTITY_OVERRIDE_MODE_OVERRIDE, ENTITY_OVERRIDE_MODE_SUPPLEMENT
   *
   * @param self::ENTITY_OVERRIDE_MODE_* $entityOverrideMode
   */
  public function setEntityOverrideMode($entityOverrideMode)
  {
    $this->entityOverrideMode = $entityOverrideMode;
  }
  /**
   * @return self::ENTITY_OVERRIDE_MODE_*
   */
  public function getEntityOverrideMode()
  {
    return $this->entityOverrideMode;
  }
  /**
   * Required. The unique identifier of the session entity type. Format:
   * `projects//locations//agents//sessions//entityTypes/` or
   * `projects//locations//agents//environments//sessions//entityTypes/`. If
   * `Environment ID` is not specified, we assume default 'draft' environment.
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
class_alias(GoogleCloudDialogflowCxV3SessionEntityType::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3SessionEntityType');
