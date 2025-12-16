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

namespace Google\Service\Dataform;

class IamPolicyOverrideView extends \Google\Model
{
  protected $iamPolicyNameType = PolicyName::class;
  protected $iamPolicyNameDataType = '';
  /**
   * Whether the IAM policy encoded in this view is active.
   *
   * @var bool
   */
  public $isActive;

  /**
   * The IAM policy name for the resource.
   *
   * @param PolicyName $iamPolicyName
   */
  public function setIamPolicyName(PolicyName $iamPolicyName)
  {
    $this->iamPolicyName = $iamPolicyName;
  }
  /**
   * @return PolicyName
   */
  public function getIamPolicyName()
  {
    return $this->iamPolicyName;
  }
  /**
   * Whether the IAM policy encoded in this view is active.
   *
   * @param bool $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }
  /**
   * @return bool
   */
  public function getIsActive()
  {
    return $this->isActive;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IamPolicyOverrideView::class, 'Google_Service_Dataform_IamPolicyOverrideView');
