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

namespace Google\Service\CloudAsset;

class GoogleIamV2DenyRule extends \Google\Collection
{
  protected $collection_key = 'exceptionPrincipals';
  protected $denialConditionType = Expr::class;
  protected $denialConditionDataType = '';
  /**
   * @var string[]
   */
  public $deniedPermissions;
  /**
   * @var string[]
   */
  public $deniedPrincipals;
  /**
   * @var string[]
   */
  public $exceptionPermissions;
  /**
   * @var string[]
   */
  public $exceptionPrincipals;

  /**
   * @param Expr
   */
  public function setDenialCondition(Expr $denialCondition)
  {
    $this->denialCondition = $denialCondition;
  }
  /**
   * @return Expr
   */
  public function getDenialCondition()
  {
    return $this->denialCondition;
  }
  /**
   * @param string[]
   */
  public function setDeniedPermissions($deniedPermissions)
  {
    $this->deniedPermissions = $deniedPermissions;
  }
  /**
   * @return string[]
   */
  public function getDeniedPermissions()
  {
    return $this->deniedPermissions;
  }
  /**
   * @param string[]
   */
  public function setDeniedPrincipals($deniedPrincipals)
  {
    $this->deniedPrincipals = $deniedPrincipals;
  }
  /**
   * @return string[]
   */
  public function getDeniedPrincipals()
  {
    return $this->deniedPrincipals;
  }
  /**
   * @param string[]
   */
  public function setExceptionPermissions($exceptionPermissions)
  {
    $this->exceptionPermissions = $exceptionPermissions;
  }
  /**
   * @return string[]
   */
  public function getExceptionPermissions()
  {
    return $this->exceptionPermissions;
  }
  /**
   * @param string[]
   */
  public function setExceptionPrincipals($exceptionPrincipals)
  {
    $this->exceptionPrincipals = $exceptionPrincipals;
  }
  /**
   * @return string[]
   */
  public function getExceptionPrincipals()
  {
    return $this->exceptionPrincipals;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIamV2DenyRule::class, 'Google_Service_CloudAsset_GoogleIamV2DenyRule');
