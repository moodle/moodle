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

namespace Google\Service\TPU;

class BootDiskConfig extends \Google\Model
{
  protected $customerEncryptionKeyType = CustomerEncryptionKey::class;
  protected $customerEncryptionKeyDataType = '';

  /**
   * Optional. Customer encryption key for boot disk.
   *
   * @param CustomerEncryptionKey $customerEncryptionKey
   */
  public function setCustomerEncryptionKey(CustomerEncryptionKey $customerEncryptionKey)
  {
    $this->customerEncryptionKey = $customerEncryptionKey;
  }
  /**
   * @return CustomerEncryptionKey
   */
  public function getCustomerEncryptionKey()
  {
    return $this->customerEncryptionKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BootDiskConfig::class, 'Google_Service_TPU_BootDiskConfig');
