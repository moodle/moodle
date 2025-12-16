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

namespace Google\Service\Storage;

class BucketEncryption extends \Google\Model
{
  protected $customerManagedEncryptionEnforcementConfigType = BucketEncryptionCustomerManagedEncryptionEnforcementConfig::class;
  protected $customerManagedEncryptionEnforcementConfigDataType = '';
  protected $customerSuppliedEncryptionEnforcementConfigType = BucketEncryptionCustomerSuppliedEncryptionEnforcementConfig::class;
  protected $customerSuppliedEncryptionEnforcementConfigDataType = '';
  /**
   * A Cloud KMS key that will be used to encrypt objects inserted into this
   * bucket, if no encryption method is specified.
   *
   * @var string
   */
  public $defaultKmsKeyName;
  protected $googleManagedEncryptionEnforcementConfigType = BucketEncryptionGoogleManagedEncryptionEnforcementConfig::class;
  protected $googleManagedEncryptionEnforcementConfigDataType = '';

  /**
   * If set, the new objects created in this bucket must comply with this
   * enforcement config. Changing this has no effect on existing objects; it
   * applies to new objects only. If omitted, the new objects are allowed to be
   * encrypted with Customer Managed Encryption type by default.
   *
   * @param BucketEncryptionCustomerManagedEncryptionEnforcementConfig $customerManagedEncryptionEnforcementConfig
   */
  public function setCustomerManagedEncryptionEnforcementConfig(BucketEncryptionCustomerManagedEncryptionEnforcementConfig $customerManagedEncryptionEnforcementConfig)
  {
    $this->customerManagedEncryptionEnforcementConfig = $customerManagedEncryptionEnforcementConfig;
  }
  /**
   * @return BucketEncryptionCustomerManagedEncryptionEnforcementConfig
   */
  public function getCustomerManagedEncryptionEnforcementConfig()
  {
    return $this->customerManagedEncryptionEnforcementConfig;
  }
  /**
   * If set, the new objects created in this bucket must comply with this
   * enforcement config. Changing this has no effect on existing objects; it
   * applies to new objects only. If omitted, the new objects are allowed to be
   * encrypted with Customer Supplied Encryption type by default.
   *
   * @param BucketEncryptionCustomerSuppliedEncryptionEnforcementConfig $customerSuppliedEncryptionEnforcementConfig
   */
  public function setCustomerSuppliedEncryptionEnforcementConfig(BucketEncryptionCustomerSuppliedEncryptionEnforcementConfig $customerSuppliedEncryptionEnforcementConfig)
  {
    $this->customerSuppliedEncryptionEnforcementConfig = $customerSuppliedEncryptionEnforcementConfig;
  }
  /**
   * @return BucketEncryptionCustomerSuppliedEncryptionEnforcementConfig
   */
  public function getCustomerSuppliedEncryptionEnforcementConfig()
  {
    return $this->customerSuppliedEncryptionEnforcementConfig;
  }
  /**
   * A Cloud KMS key that will be used to encrypt objects inserted into this
   * bucket, if no encryption method is specified.
   *
   * @param string $defaultKmsKeyName
   */
  public function setDefaultKmsKeyName($defaultKmsKeyName)
  {
    $this->defaultKmsKeyName = $defaultKmsKeyName;
  }
  /**
   * @return string
   */
  public function getDefaultKmsKeyName()
  {
    return $this->defaultKmsKeyName;
  }
  /**
   * If set, the new objects created in this bucket must comply with this
   * enforcement config. Changing this has no effect on existing objects; it
   * applies to new objects only. If omitted, the new objects are allowed to be
   * encrypted with Google Managed Encryption type by default.
   *
   * @param BucketEncryptionGoogleManagedEncryptionEnforcementConfig $googleManagedEncryptionEnforcementConfig
   */
  public function setGoogleManagedEncryptionEnforcementConfig(BucketEncryptionGoogleManagedEncryptionEnforcementConfig $googleManagedEncryptionEnforcementConfig)
  {
    $this->googleManagedEncryptionEnforcementConfig = $googleManagedEncryptionEnforcementConfig;
  }
  /**
   * @return BucketEncryptionGoogleManagedEncryptionEnforcementConfig
   */
  public function getGoogleManagedEncryptionEnforcementConfig()
  {
    return $this->googleManagedEncryptionEnforcementConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketEncryption::class, 'Google_Service_Storage_BucketEncryption');
