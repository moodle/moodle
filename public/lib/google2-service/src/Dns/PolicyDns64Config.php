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

namespace Google\Service\Dns;

class PolicyDns64Config extends \Google\Model
{
  /**
   * @var string
   */
  public $kind;
  protected $scopeType = PolicyDns64ConfigScope::class;
  protected $scopeDataType = '';

  /**
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The scope to which DNS64 config will be applied to.
   *
   * @param PolicyDns64ConfigScope $scope
   */
  public function setScope(PolicyDns64ConfigScope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return PolicyDns64ConfigScope
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyDns64Config::class, 'Google_Service_Dns_PolicyDns64Config');
