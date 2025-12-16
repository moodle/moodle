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

class EnterpriseCrmEventbusProtoParamSpecEntryProtoDefinition extends \Google\Model
{
  /**
   * The fully-qualified proto name. This message, for example, would be
   * "enterprise.crm.eventbus.proto.ParamSpecEntry.ProtoDefinition".
   *
   * @var string
   */
  public $fullName;
  /**
   * Path to the proto file that contains the message type's definition.
   *
   * @var string
   */
  public $path;

  /**
   * The fully-qualified proto name. This message, for example, would be
   * "enterprise.crm.eventbus.proto.ParamSpecEntry.ProtoDefinition".
   *
   * @param string $fullName
   */
  public function setFullName($fullName)
  {
    $this->fullName = $fullName;
  }
  /**
   * @return string
   */
  public function getFullName()
  {
    return $this->fullName;
  }
  /**
   * Path to the proto file that contains the message type's definition.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoParamSpecEntryProtoDefinition::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoParamSpecEntryProtoDefinition');
