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

class Variable extends \Google\Collection
{
  protected $collection_key = 'parameter';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * For mobile containers only: A list of trigger IDs for disabling conditional
   * variables; the variable is enabled if one of the enabling trigger is true
   * while all the disabling trigger are false. Treated as an unordered set.
   *
   * @var string[]
   */
  public $disablingTriggerId;
  /**
   * For mobile containers only: A list of trigger IDs for enabling conditional
   * variables; the variable is enabled if one of the enabling triggers is true
   * while all the disabling triggers are false. Treated as an unordered set.
   *
   * @var string[]
   */
  public $enablingTriggerId;
  /**
   * The fingerprint of the GTM Variable as computed at storage time. This value
   * is recomputed whenever the variable is modified.
   *
   * @var string
   */
  public $fingerprint;
  protected $formatValueType = VariableFormatValue::class;
  protected $formatValueDataType = '';
  /**
   * Variable display name.
   *
   * @var string
   */
  public $name;
  /**
   * User notes on how to apply this variable in the container.
   *
   * @var string
   */
  public $notes;
  protected $parameterType = Parameter::class;
  protected $parameterDataType = 'array';
  /**
   * Parent folder id.
   *
   * @var string
   */
  public $parentFolderId;
  /**
   * GTM Variable's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * The end timestamp in milliseconds to schedule a variable.
   *
   * @var string
   */
  public $scheduleEndMs;
  /**
   * The start timestamp in milliseconds to schedule a variable.
   *
   * @var string
   */
  public $scheduleStartMs;
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  /**
   * GTM Variable Type.
   *
   * @var string
   */
  public $type;
  /**
   * The Variable ID uniquely identifies the GTM Variable.
   *
   * @var string
   */
  public $variableId;
  /**
   * GTM Workspace ID.
   *
   * @var string
   */
  public $workspaceId;

  /**
   * GTM Account ID.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * GTM Container ID.
   *
   * @param string $containerId
   */
  public function setContainerId($containerId)
  {
    $this->containerId = $containerId;
  }
  /**
   * @return string
   */
  public function getContainerId()
  {
    return $this->containerId;
  }
  /**
   * For mobile containers only: A list of trigger IDs for disabling conditional
   * variables; the variable is enabled if one of the enabling trigger is true
   * while all the disabling trigger are false. Treated as an unordered set.
   *
   * @param string[] $disablingTriggerId
   */
  public function setDisablingTriggerId($disablingTriggerId)
  {
    $this->disablingTriggerId = $disablingTriggerId;
  }
  /**
   * @return string[]
   */
  public function getDisablingTriggerId()
  {
    return $this->disablingTriggerId;
  }
  /**
   * For mobile containers only: A list of trigger IDs for enabling conditional
   * variables; the variable is enabled if one of the enabling triggers is true
   * while all the disabling triggers are false. Treated as an unordered set.
   *
   * @param string[] $enablingTriggerId
   */
  public function setEnablingTriggerId($enablingTriggerId)
  {
    $this->enablingTriggerId = $enablingTriggerId;
  }
  /**
   * @return string[]
   */
  public function getEnablingTriggerId()
  {
    return $this->enablingTriggerId;
  }
  /**
   * The fingerprint of the GTM Variable as computed at storage time. This value
   * is recomputed whenever the variable is modified.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Option to convert a variable value to other value.
   *
   * @param VariableFormatValue $formatValue
   */
  public function setFormatValue(VariableFormatValue $formatValue)
  {
    $this->formatValue = $formatValue;
  }
  /**
   * @return VariableFormatValue
   */
  public function getFormatValue()
  {
    return $this->formatValue;
  }
  /**
   * Variable display name.
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
   * User notes on how to apply this variable in the container.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * The variable's parameters.
   *
   * @param Parameter[] $parameter
   */
  public function setParameter($parameter)
  {
    $this->parameter = $parameter;
  }
  /**
   * @return Parameter[]
   */
  public function getParameter()
  {
    return $this->parameter;
  }
  /**
   * Parent folder id.
   *
   * @param string $parentFolderId
   */
  public function setParentFolderId($parentFolderId)
  {
    $this->parentFolderId = $parentFolderId;
  }
  /**
   * @return string
   */
  public function getParentFolderId()
  {
    return $this->parentFolderId;
  }
  /**
   * GTM Variable's API relative path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * The end timestamp in milliseconds to schedule a variable.
   *
   * @param string $scheduleEndMs
   */
  public function setScheduleEndMs($scheduleEndMs)
  {
    $this->scheduleEndMs = $scheduleEndMs;
  }
  /**
   * @return string
   */
  public function getScheduleEndMs()
  {
    return $this->scheduleEndMs;
  }
  /**
   * The start timestamp in milliseconds to schedule a variable.
   *
   * @param string $scheduleStartMs
   */
  public function setScheduleStartMs($scheduleStartMs)
  {
    $this->scheduleStartMs = $scheduleStartMs;
  }
  /**
   * @return string
   */
  public function getScheduleStartMs()
  {
    return $this->scheduleStartMs;
  }
  /**
   * Auto generated link to the tag manager UI
   *
   * @param string $tagManagerUrl
   */
  public function setTagManagerUrl($tagManagerUrl)
  {
    $this->tagManagerUrl = $tagManagerUrl;
  }
  /**
   * @return string
   */
  public function getTagManagerUrl()
  {
    return $this->tagManagerUrl;
  }
  /**
   * GTM Variable Type.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The Variable ID uniquely identifies the GTM Variable.
   *
   * @param string $variableId
   */
  public function setVariableId($variableId)
  {
    $this->variableId = $variableId;
  }
  /**
   * @return string
   */
  public function getVariableId()
  {
    return $this->variableId;
  }
  /**
   * GTM Workspace ID.
   *
   * @param string $workspaceId
   */
  public function setWorkspaceId($workspaceId)
  {
    $this->workspaceId = $workspaceId;
  }
  /**
   * @return string
   */
  public function getWorkspaceId()
  {
    return $this->workspaceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Variable::class, 'Google_Service_TagManager_Variable');
