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

namespace Google\Service\CloudIdentity;

class AddIdpCredentialOperationMetadata extends \Google\Model
{
  /**
   * State of this Operation Will be "awaiting-multi-party-approval" when the
   * operation is deferred due to the target customer having enabled [Multi-
   * party approval for sensitive
   * actions](https://support.google.com/a/answer/13790448).
   *
   * @var string
   */
  public $state;

  /**
   * State of this Operation Will be "awaiting-multi-party-approval" when the
   * operation is deferred due to the target customer having enabled [Multi-
   * party approval for sensitive
   * actions](https://support.google.com/a/answer/13790448).
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddIdpCredentialOperationMetadata::class, 'Google_Service_CloudIdentity_AddIdpCredentialOperationMetadata');
