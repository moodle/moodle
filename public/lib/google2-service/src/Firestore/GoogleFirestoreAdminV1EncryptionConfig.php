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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1EncryptionConfig extends \Google\Model
{
  protected $customerManagedEncryptionType = GoogleFirestoreAdminV1CustomerManagedEncryptionOptions::class;
  protected $customerManagedEncryptionDataType = '';
  protected $googleDefaultEncryptionType = GoogleFirestoreAdminV1GoogleDefaultEncryptionOptions::class;
  protected $googleDefaultEncryptionDataType = '';
  protected $useSourceEncryptionType = GoogleFirestoreAdminV1SourceEncryptionOptions::class;
  protected $useSourceEncryptionDataType = '';

  /**
   * Use Customer Managed Encryption Keys (CMEK) for encryption.
   *
   * @param GoogleFirestoreAdminV1CustomerManagedEncryptionOptions $customerManagedEncryption
   */
  public function setCustomerManagedEncryption(GoogleFirestoreAdminV1CustomerManagedEncryptionOptions $customerManagedEncryption)
  {
    $this->customerManagedEncryption = $customerManagedEncryption;
  }
  /**
   * @return GoogleFirestoreAdminV1CustomerManagedEncryptionOptions
   */
  public function getCustomerManagedEncryption()
  {
    return $this->customerManagedEncryption;
  }
  /**
   * Use Google default encryption.
   *
   * @param GoogleFirestoreAdminV1GoogleDefaultEncryptionOptions $googleDefaultEncryption
   */
  public function setGoogleDefaultEncryption(GoogleFirestoreAdminV1GoogleDefaultEncryptionOptions $googleDefaultEncryption)
  {
    $this->googleDefaultEncryption = $googleDefaultEncryption;
  }
  /**
   * @return GoogleFirestoreAdminV1GoogleDefaultEncryptionOptions
   */
  public function getGoogleDefaultEncryption()
  {
    return $this->googleDefaultEncryption;
  }
  /**
   * The database will use the same encryption configuration as the source.
   *
   * @param GoogleFirestoreAdminV1SourceEncryptionOptions $useSourceEncryption
   */
  public function setUseSourceEncryption(GoogleFirestoreAdminV1SourceEncryptionOptions $useSourceEncryption)
  {
    $this->useSourceEncryption = $useSourceEncryption;
  }
  /**
   * @return GoogleFirestoreAdminV1SourceEncryptionOptions
   */
  public function getUseSourceEncryption()
  {
    return $this->useSourceEncryption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1EncryptionConfig::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1EncryptionConfig');
