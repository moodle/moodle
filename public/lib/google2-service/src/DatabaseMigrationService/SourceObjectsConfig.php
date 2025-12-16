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

namespace Google\Service\DatabaseMigrationService;

class SourceObjectsConfig extends \Google\Collection
{
  /**
   * The type of the objects selection is unknown, indicating that the migration
   * job is at instance level.
   */
  public const OBJECTS_SELECTION_TYPE_OBJECTS_SELECTION_TYPE_UNSPECIFIED = 'OBJECTS_SELECTION_TYPE_UNSPECIFIED';
  /**
   * Migrate all of the objects.
   */
  public const OBJECTS_SELECTION_TYPE_ALL_OBJECTS = 'ALL_OBJECTS';
  /**
   * Migrate specific objects.
   */
  public const OBJECTS_SELECTION_TYPE_SPECIFIED_OBJECTS = 'SPECIFIED_OBJECTS';
  protected $collection_key = 'objectConfigs';
  protected $objectConfigsType = SourceObjectConfig::class;
  protected $objectConfigsDataType = 'array';
  /**
   * Optional. The objects selection type of the migration job.
   *
   * @var string
   */
  public $objectsSelectionType;

  /**
   * Optional. The list of the objects to be migrated.
   *
   * @param SourceObjectConfig[] $objectConfigs
   */
  public function setObjectConfigs($objectConfigs)
  {
    $this->objectConfigs = $objectConfigs;
  }
  /**
   * @return SourceObjectConfig[]
   */
  public function getObjectConfigs()
  {
    return $this->objectConfigs;
  }
  /**
   * Optional. The objects selection type of the migration job.
   *
   * Accepted values: OBJECTS_SELECTION_TYPE_UNSPECIFIED, ALL_OBJECTS,
   * SPECIFIED_OBJECTS
   *
   * @param self::OBJECTS_SELECTION_TYPE_* $objectsSelectionType
   */
  public function setObjectsSelectionType($objectsSelectionType)
  {
    $this->objectsSelectionType = $objectsSelectionType;
  }
  /**
   * @return self::OBJECTS_SELECTION_TYPE_*
   */
  public function getObjectsSelectionType()
  {
    return $this->objectsSelectionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceObjectsConfig::class, 'Google_Service_DatabaseMigrationService_SourceObjectsConfig');
