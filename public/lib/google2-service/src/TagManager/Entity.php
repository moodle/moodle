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

namespace Google\Service\TagManager;

class Entity extends \Google\Model
{
  public const CHANGE_STATUS_changeStatusUnspecified = 'changeStatusUnspecified';
  /**
   * The entity has never been changed.
   */
  public const CHANGE_STATUS_none = 'none';
  /**
   * The entity is added to the workspace.
   */
  public const CHANGE_STATUS_added = 'added';
  /**
   * The entity is deleted from the workspace.
   */
  public const CHANGE_STATUS_deleted = 'deleted';
  /**
   * The entity has been updated in the workspace.
   */
  public const CHANGE_STATUS_updated = 'updated';
  protected $builtInVariableType = BuiltInVariable::class;
  protected $builtInVariableDataType = '';
  /**
   * Represents how the entity has been changed in the workspace.
   *
   * @var string
   */
  public $changeStatus;
  protected $clientType = Client::class;
  protected $clientDataType = '';
  protected $customTemplateType = CustomTemplate::class;
  protected $customTemplateDataType = '';
  protected $folderType = Folder::class;
  protected $folderDataType = '';
  protected $gtagConfigType = GtagConfig::class;
  protected $gtagConfigDataType = '';
  protected $tagType = Tag::class;
  protected $tagDataType = '';
  protected $transformationType = Transformation::class;
  protected $transformationDataType = '';
  protected $triggerType = Trigger::class;
  protected $triggerDataType = '';
  protected $variableType = Variable::class;
  protected $variableDataType = '';
  protected $zoneType = Zone::class;
  protected $zoneDataType = '';

  /**
   * The built in variable being represented by the entity.
   *
   * @param BuiltInVariable $builtInVariable
   */
  public function setBuiltInVariable(BuiltInVariable $builtInVariable)
  {
    $this->builtInVariable = $builtInVariable;
  }
  /**
   * @return BuiltInVariable
   */
  public function getBuiltInVariable()
  {
    return $this->builtInVariable;
  }
  /**
   * Represents how the entity has been changed in the workspace.
   *
   * Accepted values: changeStatusUnspecified, none, added, deleted, updated
   *
   * @param self::CHANGE_STATUS_* $changeStatus
   */
  public function setChangeStatus($changeStatus)
  {
    $this->changeStatus = $changeStatus;
  }
  /**
   * @return self::CHANGE_STATUS_*
   */
  public function getChangeStatus()
  {
    return $this->changeStatus;
  }
  /**
   * The client being represented by the entity.
   *
   * @param Client $client
   */
  public function setClient(Client $client)
  {
    $this->client = $client;
  }
  /**
   * @return Client
   */
  public function getClient()
  {
    return $this->client;
  }
  /**
   * The custom template being represented by the entity.
   *
   * @param CustomTemplate $customTemplate
   */
  public function setCustomTemplate(CustomTemplate $customTemplate)
  {
    $this->customTemplate = $customTemplate;
  }
  /**
   * @return CustomTemplate
   */
  public function getCustomTemplate()
  {
    return $this->customTemplate;
  }
  /**
   * The folder being represented by the entity.
   *
   * @param Folder $folder
   */
  public function setFolder(Folder $folder)
  {
    $this->folder = $folder;
  }
  /**
   * @return Folder
   */
  public function getFolder()
  {
    return $this->folder;
  }
  /**
   * The gtag config being represented by the entity.
   *
   * @param GtagConfig $gtagConfig
   */
  public function setGtagConfig(GtagConfig $gtagConfig)
  {
    $this->gtagConfig = $gtagConfig;
  }
  /**
   * @return GtagConfig
   */
  public function getGtagConfig()
  {
    return $this->gtagConfig;
  }
  /**
   * The tag being represented by the entity.
   *
   * @param Tag $tag
   */
  public function setTag(Tag $tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return Tag
   */
  public function getTag()
  {
    return $this->tag;
  }
  /**
   * The transformation being represented by the entity.
   *
   * @param Transformation $transformation
   */
  public function setTransformation(Transformation $transformation)
  {
    $this->transformation = $transformation;
  }
  /**
   * @return Transformation
   */
  public function getTransformation()
  {
    return $this->transformation;
  }
  /**
   * The trigger being represented by the entity.
   *
   * @param Trigger $trigger
   */
  public function setTrigger(Trigger $trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return Trigger
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
  /**
   * The variable being represented by the entity.
   *
   * @param Variable $variable
   */
  public function setVariable(Variable $variable)
  {
    $this->variable = $variable;
  }
  /**
   * @return Variable
   */
  public function getVariable()
  {
    return $this->variable;
  }
  /**
   * The zone being represented by the entity.
   *
   * @param Zone $zone
   */
  public function setZone(Zone $zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return Zone
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entity::class, 'Google_Service_TagManager_Entity');
