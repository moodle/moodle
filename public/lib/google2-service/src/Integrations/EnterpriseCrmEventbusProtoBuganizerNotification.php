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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoBuganizerNotification extends \Google\Model
{
  /**
   * Whom to assign the new bug. Optional.
   *
   * @var string
   */
  public $assigneeEmailAddress;
  /**
   * ID of the buganizer component within which to create a new issue. Required.
   *
   * @var string
   */
  public $componentId;
  /**
   * ID of the buganizer template to use. Optional.
   *
   * @var string
   */
  public $templateId;
  /**
   * Title of the issue to be created. Required.
   *
   * @var string
   */
  public $title;

  /**
   * Whom to assign the new bug. Optional.
   *
   * @param string $assigneeEmailAddress
   */
  public function setAssigneeEmailAddress($assigneeEmailAddress)
  {
    $this->assigneeEmailAddress = $assigneeEmailAddress;
  }
  /**
   * @return string
   */
  public function getAssigneeEmailAddress()
  {
    return $this->assigneeEmailAddress;
  }
  /**
   * ID of the buganizer component within which to create a new issue. Required.
   *
   * @param string $componentId
   */
  public function setComponentId($componentId)
  {
    $this->componentId = $componentId;
  }
  /**
   * @return string
   */
  public function getComponentId()
  {
    return $this->componentId;
  }
  /**
   * ID of the buganizer template to use. Optional.
   *
   * @param string $templateId
   */
  public function setTemplateId($templateId)
  {
    $this->templateId = $templateId;
  }
  /**
   * @return string
   */
  public function getTemplateId()
  {
    return $this->templateId;
  }
  /**
   * Title of the issue to be created. Required.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoBuganizerNotification::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoBuganizerNotification');
