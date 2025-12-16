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

namespace Google\Service\Compute;

class SubnetworksScopedWarning extends \Google\Model
{
  /**
   * Name of the scope containing this set of Subnetworks.
   *
   * @var string
   */
  public $scopeName;
  protected $warningType = SubnetworksScopedWarningWarning::class;
  protected $warningDataType = '';

  /**
   * Name of the scope containing this set of Subnetworks.
   *
   * @param string $scopeName
   */
  public function setScopeName($scopeName)
  {
    $this->scopeName = $scopeName;
  }
  /**
   * @return string
   */
  public function getScopeName()
  {
    return $this->scopeName;
  }
  /**
   * An informational warning about unreachable scope
   *
   * @param SubnetworksScopedWarningWarning $warning
   */
  public function setWarning(SubnetworksScopedWarningWarning $warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return SubnetworksScopedWarningWarning
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubnetworksScopedWarning::class, 'Google_Service_Compute_SubnetworksScopedWarning');
