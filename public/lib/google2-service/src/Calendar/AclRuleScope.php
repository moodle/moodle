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

namespace Google\Service\Calendar;

class AclRuleScope extends \Google\Model
{
  /**
   * The type of the scope. Possible values are: - "default" - The public scope.
   * This is the default value.  - "user" - Limits the scope to a single user.
   * - "group" - Limits the scope to a group.  - "domain" - Limits the scope to
   * a domain.  Note: The permissions granted to the "default", or public, scope
   * apply to any user, authenticated or not.
   *
   * @var string
   */
  public $type;
  /**
   * The email address of a user or group, or the name of a domain, depending on
   * the scope type. Omitted for type "default".
   *
   * @var string
   */
  public $value;

  /**
   * The type of the scope. Possible values are: - "default" - The public scope.
   * This is the default value.  - "user" - Limits the scope to a single user.
   * - "group" - Limits the scope to a group.  - "domain" - Limits the scope to
   * a domain.  Note: The permissions granted to the "default", or public, scope
   * apply to any user, authenticated or not.
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
   * The email address of a user or group, or the name of a domain, depending on
   * the scope type. Omitted for type "default".
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AclRuleScope::class, 'Google_Service_Calendar_AclRuleScope');
