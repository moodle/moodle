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

namespace Google\Service\Networkconnectivity;

class Warnings extends \Google\Model
{
  /**
   * Default value.
   */
  public const CODE_WARNING_UNSPECIFIED = 'WARNING_UNSPECIFIED';
  /**
   * The policy-based route is not active and functioning. Common causes are
   * that the dependent network was deleted or the resource project was turned
   * off.
   */
  public const CODE_RESOURCE_NOT_ACTIVE = 'RESOURCE_NOT_ACTIVE';
  /**
   * The policy-based route is being modified (e.g. created/deleted) at this
   * time.
   */
  public const CODE_RESOURCE_BEING_MODIFIED = 'RESOURCE_BEING_MODIFIED';
  /**
   * Output only. A warning code, if applicable.
   *
   * @var string
   */
  public $code;
  /**
   * Output only. Metadata about this warning in key: value format. The key
   * should provides more detail on the warning being returned. For example, for
   * warnings where there are no results in a list request for a particular
   * zone, this key might be scope and the key value might be the zone name.
   * Other examples might be a key indicating a deprecated resource and a
   * suggested replacement.
   *
   * @var string[]
   */
  public $data;
  /**
   * Output only. A human-readable description of the warning code.
   *
   * @var string
   */
  public $warningMessage;

  /**
   * Output only. A warning code, if applicable.
   *
   * Accepted values: WARNING_UNSPECIFIED, RESOURCE_NOT_ACTIVE,
   * RESOURCE_BEING_MODIFIED
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Output only. Metadata about this warning in key: value format. The key
   * should provides more detail on the warning being returned. For example, for
   * warnings where there are no results in a list request for a particular
   * zone, this key might be scope and the key value might be the zone name.
   * Other examples might be a key indicating a deprecated resource and a
   * suggested replacement.
   *
   * @param string[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Output only. A human-readable description of the warning code.
   *
   * @param string $warningMessage
   */
  public function setWarningMessage($warningMessage)
  {
    $this->warningMessage = $warningMessage;
  }
  /**
   * @return string
   */
  public function getWarningMessage()
  {
    return $this->warningMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Warnings::class, 'Google_Service_Networkconnectivity_Warnings');
