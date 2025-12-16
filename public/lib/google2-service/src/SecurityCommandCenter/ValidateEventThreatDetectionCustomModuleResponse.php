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

namespace Google\Service\SecurityCommandCenter;

class ValidateEventThreatDetectionCustomModuleResponse extends \Google\Model
{
  protected $errorsType = CustomModuleValidationErrors::class;
  protected $errorsDataType = '';

  /**
   * A list of errors returned by the validator. If the list is empty, there
   * were no errors.
   *
   * @param CustomModuleValidationErrors $errors
   */
  public function setErrors(CustomModuleValidationErrors $errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return CustomModuleValidationErrors
   */
  public function getErrors()
  {
    return $this->errors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidateEventThreatDetectionCustomModuleResponse::class, 'Google_Service_SecurityCommandCenter_ValidateEventThreatDetectionCustomModuleResponse');
