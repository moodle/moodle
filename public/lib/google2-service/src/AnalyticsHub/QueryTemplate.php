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

namespace Google\Service\AnalyticsHub;

class QueryTemplate extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The QueryTemplate is in draft state.
   */
  public const STATE_DRAFTED = 'DRAFTED';
  /**
   * The QueryTemplate is in pending state.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The QueryTemplate is in deleted state.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The QueryTemplate is in approved state.
   */
  public const STATE_APPROVED = 'APPROVED';
  /**
   * Output only. Timestamp when the QueryTemplate was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Short description of the QueryTemplate. The description must not
   * contain Unicode non-characters and C0 and C1 control codes except tabs
   * (HT), new lines (LF), carriage returns (CR), and page breaks (FF). Default
   * value is an empty string. Max length: 2000 bytes.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Human-readable display name of the QueryTemplate. The display
   * name must contain only Unicode letters, numbers (0-9), underscores (_),
   * dashes (-), spaces ( ), ampersands (&) and can't start or end with spaces.
   * Default value is an empty string. Max length: 63 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Documentation describing the QueryTemplate.
   *
   * @var string
   */
  public $documentation;
  /**
   * Output only. The resource name of the QueryTemplate. e.g.
   * `projects/myproject/locations/us/dataExchanges/123/queryTemplates/456`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Email or URL of the primary point of contact of the
   * QueryTemplate. Max Length: 1000 bytes.
   *
   * @var string
   */
  public $primaryContact;
  /**
   * Optional. Will be deprecated. Email or URL of the primary point of contact
   * of the QueryTemplate. Max Length: 1000 bytes.
   *
   * @var string
   */
  public $proposer;
  protected $routineType = Routine::class;
  protected $routineDataType = '';
  /**
   * Output only. The QueryTemplate lifecycle state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp when the QueryTemplate was last modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when the QueryTemplate was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Short description of the QueryTemplate. The description must not
   * contain Unicode non-characters and C0 and C1 control codes except tabs
   * (HT), new lines (LF), carriage returns (CR), and page breaks (FF). Default
   * value is an empty string. Max length: 2000 bytes.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Human-readable display name of the QueryTemplate. The display
   * name must contain only Unicode letters, numbers (0-9), underscores (_),
   * dashes (-), spaces ( ), ampersands (&) and can't start or end with spaces.
   * Default value is an empty string. Max length: 63 bytes.
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
   * Optional. Documentation describing the QueryTemplate.
   *
   * @param string $documentation
   */
  public function setDocumentation($documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return string
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Output only. The resource name of the QueryTemplate. e.g.
   * `projects/myproject/locations/us/dataExchanges/123/queryTemplates/456`
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
   * Optional. Email or URL of the primary point of contact of the
   * QueryTemplate. Max Length: 1000 bytes.
   *
   * @param string $primaryContact
   */
  public function setPrimaryContact($primaryContact)
  {
    $this->primaryContact = $primaryContact;
  }
  /**
   * @return string
   */
  public function getPrimaryContact()
  {
    return $this->primaryContact;
  }
  /**
   * Optional. Will be deprecated. Email or URL of the primary point of contact
   * of the QueryTemplate. Max Length: 1000 bytes.
   *
   * @param string $proposer
   */
  public function setProposer($proposer)
  {
    $this->proposer = $proposer;
  }
  /**
   * @return string
   */
  public function getProposer()
  {
    return $this->proposer;
  }
  /**
   * Optional. The routine associated with the QueryTemplate.
   *
   * @param Routine $routine
   */
  public function setRoutine(Routine $routine)
  {
    $this->routine = $routine;
  }
  /**
   * @return Routine
   */
  public function getRoutine()
  {
    return $this->routine;
  }
  /**
   * Output only. The QueryTemplate lifecycle state.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFTED, PENDING, DELETED, APPROVED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Timestamp when the QueryTemplate was last modified.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryTemplate::class, 'Google_Service_AnalyticsHub_QueryTemplate');
