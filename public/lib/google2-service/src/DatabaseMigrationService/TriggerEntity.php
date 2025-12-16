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

class TriggerEntity extends \Google\Collection
{
  protected $collection_key = 'triggeringEvents';
  /**
   * Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  /**
   * The name of the trigger.
   *
   * @var string
   */
  public $name;
  /**
   * The SQL code which creates the trigger.
   *
   * @var string
   */
  public $sqlCode;
  /**
   * Indicates when the trigger fires, for example BEFORE STATEMENT, AFTER EACH
   * ROW.
   *
   * @var string
   */
  public $triggerType;
  /**
   * The DML, DDL, or database events that fire the trigger, for example INSERT,
   * UPDATE.
   *
   * @var string[]
   */
  public $triggeringEvents;

  /**
   * Custom engine specific features.
   *
   * @param array[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return array[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * The name of the trigger.
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
   * The SQL code which creates the trigger.
   *
   * @param string $sqlCode
   */
  public function setSqlCode($sqlCode)
  {
    $this->sqlCode = $sqlCode;
  }
  /**
   * @return string
   */
  public function getSqlCode()
  {
    return $this->sqlCode;
  }
  /**
   * Indicates when the trigger fires, for example BEFORE STATEMENT, AFTER EACH
   * ROW.
   *
   * @param string $triggerType
   */
  public function setTriggerType($triggerType)
  {
    $this->triggerType = $triggerType;
  }
  /**
   * @return string
   */
  public function getTriggerType()
  {
    return $this->triggerType;
  }
  /**
   * The DML, DDL, or database events that fire the trigger, for example INSERT,
   * UPDATE.
   *
   * @param string[] $triggeringEvents
   */
  public function setTriggeringEvents($triggeringEvents)
  {
    $this->triggeringEvents = $triggeringEvents;
  }
  /**
   * @return string[]
   */
  public function getTriggeringEvents()
  {
    return $this->triggeringEvents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TriggerEntity::class, 'Google_Service_DatabaseMigrationService_TriggerEntity');
