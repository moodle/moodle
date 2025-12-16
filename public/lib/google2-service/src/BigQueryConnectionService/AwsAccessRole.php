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

namespace Google\Service\BigQueryConnectionService;

class AwsAccessRole extends \Google\Model
{
  /**
   * The user’s AWS IAM Role that trusts the Google-owned AWS IAM user
   * Connection.
   *
   * @var string
   */
  public $iamRoleId;
  /**
   * A unique Google-owned and Google-generated identity for the Connection.
   * This identity will be used to access the user's AWS IAM Role.
   *
   * @var string
   */
  public $identity;

  /**
   * The user’s AWS IAM Role that trusts the Google-owned AWS IAM user
   * Connection.
   *
   * @param string $iamRoleId
   */
  public function setIamRoleId($iamRoleId)
  {
    $this->iamRoleId = $iamRoleId;
  }
  /**
   * @return string
   */
  public function getIamRoleId()
  {
    return $this->iamRoleId;
  }
  /**
   * A unique Google-owned and Google-generated identity for the Connection.
   * This identity will be used to access the user's AWS IAM Role.
   *
   * @param string $identity
   */
  public function setIdentity($identity)
  {
    $this->identity = $identity;
  }
  /**
   * @return string
   */
  public function getIdentity()
  {
    return $this->identity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsAccessRole::class, 'Google_Service_BigQueryConnectionService_AwsAccessRole');
