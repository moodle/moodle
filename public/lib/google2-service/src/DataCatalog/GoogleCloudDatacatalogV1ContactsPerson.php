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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1ContactsPerson extends \Google\Model
{
  /**
   * Designation of the person, for example, Data Steward.
   *
   * @var string
   */
  public $designation;
  /**
   * Email of the person in the format of `john.doe@xyz`, ``, or `John Doe`.
   *
   * @var string
   */
  public $email;

  /**
   * Designation of the person, for example, Data Steward.
   *
   * @param string $designation
   */
  public function setDesignation($designation)
  {
    $this->designation = $designation;
  }
  /**
   * @return string
   */
  public function getDesignation()
  {
    return $this->designation;
  }
  /**
   * Email of the person in the format of `john.doe@xyz`, ``, or `John Doe`.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1ContactsPerson::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ContactsPerson');
