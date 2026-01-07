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

class EnterpriseCrmEventbusProtoNodeIdentifier extends \Google\Model
{
  public const ELEMENT_TYPE_UNKNOWN_TYPE = 'UNKNOWN_TYPE';
  public const ELEMENT_TYPE_TASK_CONFIG = 'TASK_CONFIG';
  public const ELEMENT_TYPE_TRIGGER_CONFIG = 'TRIGGER_CONFIG';
  /**
   * Configuration of the edge.
   *
   * @var string
   */
  public $elementIdentifier;
  /**
   * Destination node where the edge ends. It can only be a task config.
   *
   * @var string
   */
  public $elementType;

  /**
   * Configuration of the edge.
   *
   * @param string $elementIdentifier
   */
  public function setElementIdentifier($elementIdentifier)
  {
    $this->elementIdentifier = $elementIdentifier;
  }
  /**
   * @return string
   */
  public function getElementIdentifier()
  {
    return $this->elementIdentifier;
  }
  /**
   * Destination node where the edge ends. It can only be a task config.
   *
   * Accepted values: UNKNOWN_TYPE, TASK_CONFIG, TRIGGER_CONFIG
   *
   * @param self::ELEMENT_TYPE_* $elementType
   */
  public function setElementType($elementType)
  {
    $this->elementType = $elementType;
  }
  /**
   * @return self::ELEMENT_TYPE_*
   */
  public function getElementType()
  {
    return $this->elementType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoNodeIdentifier::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoNodeIdentifier');
