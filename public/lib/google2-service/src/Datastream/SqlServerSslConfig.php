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

namespace Google\Service\Datastream;

class SqlServerSslConfig extends \Google\Model
{
  protected $basicEncryptionType = BasicEncryption::class;
  protected $basicEncryptionDataType = '';
  protected $encryptionAndServerValidationType = EncryptionAndServerValidation::class;
  protected $encryptionAndServerValidationDataType = '';
  protected $encryptionNotEnforcedType = EncryptionNotEnforced::class;
  protected $encryptionNotEnforcedDataType = '';

  /**
   * If set, Datastream will enforce encryption without authenticating server
   * identity. Server certificates will be trusted by default.
   *
   * @param BasicEncryption $basicEncryption
   */
  public function setBasicEncryption(BasicEncryption $basicEncryption)
  {
    $this->basicEncryption = $basicEncryption;
  }
  /**
   * @return BasicEncryption
   */
  public function getBasicEncryption()
  {
    return $this->basicEncryption;
  }
  /**
   * If set, Datastream will enforce encryption and authenticate server
   * identity.
   *
   * @param EncryptionAndServerValidation $encryptionAndServerValidation
   */
  public function setEncryptionAndServerValidation(EncryptionAndServerValidation $encryptionAndServerValidation)
  {
    $this->encryptionAndServerValidation = $encryptionAndServerValidation;
  }
  /**
   * @return EncryptionAndServerValidation
   */
  public function getEncryptionAndServerValidation()
  {
    return $this->encryptionAndServerValidation;
  }
  /**
   * If set, Datastream will not enforce encryption. If the DB server mandates
   * encryption, then connection will be encrypted but server identity will not
   * be authenticated.
   *
   * @param EncryptionNotEnforced $encryptionNotEnforced
   */
  public function setEncryptionNotEnforced(EncryptionNotEnforced $encryptionNotEnforced)
  {
    $this->encryptionNotEnforced = $encryptionNotEnforced;
  }
  /**
   * @return EncryptionNotEnforced
   */
  public function getEncryptionNotEnforced()
  {
    return $this->encryptionNotEnforced;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerSslConfig::class, 'Google_Service_Datastream_SqlServerSslConfig');
