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

class AddIdpCredentialRequest extends \Google\Model
{
  /**
   * PEM encoded x509 certificate containing the public key for verifying IdP
   * signatures.
   *
   * @var string
   */
  public $pemData;

  /**
   * PEM encoded x509 certificate containing the public key for verifying IdP
   * signatures.
   *
   * @param string $pemData
   */
  public function setPemData($pemData)
  {
    $this->pemData = $pemData;
  }
  /**
   * @return string
   */
  public function getPemData()
  {
    return $this->pemData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddIdpCredentialRequest::class, 'Google_Service_CloudIdentity_AddIdpCredentialRequest');
