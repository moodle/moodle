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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainInitializeEncryptionSpecRequest extends \Google\Model
{
  protected $encryptionSpecType = GoogleCloudContactcenterinsightsV1mainEncryptionSpec::class;
  protected $encryptionSpecDataType = '';

  /**
   * Required. The encryption spec used for CMEK encryption. It is required that
   * the kms key is in the same region as the endpoint. The same key will be
   * used for all provisioned resources, if encryption is available. If the
   * `kms_key_name` field is left empty, no encryption will be enforced.
   *
   * @param GoogleCloudContactcenterinsightsV1mainEncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudContactcenterinsightsV1mainEncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainEncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainInitializeEncryptionSpecRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainInitializeEncryptionSpecRequest');
