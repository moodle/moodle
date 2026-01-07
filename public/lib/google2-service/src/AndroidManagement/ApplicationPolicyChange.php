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

namespace Google\Service\AndroidManagement;

class ApplicationPolicyChange extends \Google\Model
{
  protected $applicationType = ApplicationPolicy::class;
  protected $applicationDataType = '';
  /**
   * The field mask indicating the fields to update. If omitted, all modifiable
   * fields are updated.
   *
   * @var string
   */
  public $updateMask;

  /**
   * If ApplicationPolicy.packageName matches an existing ApplicationPolicy
   * object within the Policy being modified, then that object will be updated.
   * Otherwise, it will be added to the end of the Policy.applications.
   *
   * @param ApplicationPolicy $application
   */
  public function setApplication(ApplicationPolicy $application)
  {
    $this->application = $application;
  }
  /**
   * @return ApplicationPolicy
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * The field mask indicating the fields to update. If omitted, all modifiable
   * fields are updated.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationPolicyChange::class, 'Google_Service_AndroidManagement_ApplicationPolicyChange');
