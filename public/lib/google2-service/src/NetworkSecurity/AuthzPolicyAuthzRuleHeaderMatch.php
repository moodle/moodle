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

namespace Google\Service\NetworkSecurity;

class AuthzPolicyAuthzRuleHeaderMatch extends \Google\Model
{
  /**
   * Optional. Specifies the name of the header in the request.
   *
   * @var string
   */
  public $name;
  protected $valueType = AuthzPolicyAuthzRuleStringMatch::class;
  protected $valueDataType = '';

  /**
   * Optional. Specifies the name of the header in the request.
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
   * Optional. Specifies how the header match will be performed.
   *
   * @param AuthzPolicyAuthzRuleStringMatch $value
   */
  public function setValue(AuthzPolicyAuthzRuleStringMatch $value)
  {
    $this->value = $value;
  }
  /**
   * @return AuthzPolicyAuthzRuleStringMatch
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyAuthzRuleHeaderMatch::class, 'Google_Service_NetworkSecurity_AuthzPolicyAuthzRuleHeaderMatch');
