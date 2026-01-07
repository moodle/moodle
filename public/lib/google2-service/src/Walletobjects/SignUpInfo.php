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

namespace Google\Service\Walletobjects;

class SignUpInfo extends \Google\Model
{
  /**
   * ID of the class the user can sign up for.
   *
   * @var string
   */
  public $classId;

  /**
   * ID of the class the user can sign up for.
   *
   * @param string $classId
   */
  public function setClassId($classId)
  {
    $this->classId = $classId;
  }
  /**
   * @return string
   */
  public function getClassId()
  {
    return $this->classId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignUpInfo::class, 'Google_Service_Walletobjects_SignUpInfo');
