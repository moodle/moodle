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

namespace Google\Service\ServiceUsage;

class GoogleApiServiceusageV1beta1ServiceIdentity extends \Google\Model
{
  /**
   * The email address of the service account that a service producer would use
   * to access consumer resources.
   *
   * @var string
   */
  public $email;
  /**
   * The unique and stable id of the service account. https://cloud.google.com/i
   * am/reference/rest/v1/projects.serviceAccounts#ServiceAccount
   *
   * @var string
   */
  public $uniqueId;

  /**
   * The email address of the service account that a service producer would use
   * to access consumer resources.
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
  /**
   * The unique and stable id of the service account. https://cloud.google.com/i
   * am/reference/rest/v1/projects.serviceAccounts#ServiceAccount
   *
   * @param string $uniqueId
   */
  public function setUniqueId($uniqueId)
  {
    $this->uniqueId = $uniqueId;
  }
  /**
   * @return string
   */
  public function getUniqueId()
  {
    return $this->uniqueId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiServiceusageV1beta1ServiceIdentity::class, 'Google_Service_ServiceUsage_GoogleApiServiceusageV1beta1ServiceIdentity');
