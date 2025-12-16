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

namespace Google\Service\Apigee;

class GoogleRpcPreconditionFailureViolation extends \Google\Model
{
  /**
   * A description of how the precondition failed. Developers can use this
   * description to understand how to fix the failure. For example: "Terms of
   * service not accepted".
   *
   * @var string
   */
  public $description;
  /**
   * The subject, relative to the type, that failed. For example,
   * "google.com/cloud" relative to the "TOS" type would indicate which terms of
   * service is being referenced.
   *
   * @var string
   */
  public $subject;
  /**
   * The type of PreconditionFailure. We recommend using a service-specific enum
   * type to define the supported precondition violation subjects. For example,
   * "TOS" for "Terms of Service violation".
   *
   * @var string
   */
  public $type;

  /**
   * A description of how the precondition failed. Developers can use this
   * description to understand how to fix the failure. For example: "Terms of
   * service not accepted".
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The subject, relative to the type, that failed. For example,
   * "google.com/cloud" relative to the "TOS" type would indicate which terms of
   * service is being referenced.
   *
   * @param string $subject
   */
  public function setSubject($subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return string
   */
  public function getSubject()
  {
    return $this->subject;
  }
  /**
   * The type of PreconditionFailure. We recommend using a service-specific enum
   * type to define the supported precondition violation subjects. For example,
   * "TOS" for "Terms of Service violation".
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleRpcPreconditionFailureViolation::class, 'Google_Service_Apigee_GoogleRpcPreconditionFailureViolation');
