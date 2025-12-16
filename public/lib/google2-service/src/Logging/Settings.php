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

namespace Google\Service\Logging;

class Settings extends \Google\Model
{
  protected $defaultSinkConfigType = DefaultSinkConfig::class;
  protected $defaultSinkConfigDataType = '';
  /**
   * Optional. If set to true, the _Default sink in newly created projects and
   * folders will created in a disabled state. This can be used to automatically
   * disable log storage if there is already an aggregated sink configured in
   * the hierarchy. The _Default sink can be re-enabled manually if needed.
   *
   * @var bool
   */
  public $disableDefaultSink;
  /**
   * Optional. The resource name for the configured Cloud KMS key.KMS key name
   * format: "projects/[PROJECT_ID]/locations/[LOCATION]/keyRings/[KEYRING]/cryp
   * toKeys/[KEY]" For example:"projects/my-project/locations/us-
   * central1/keyRings/my-ring/cryptoKeys/my-key"To enable CMEK, set this field
   * to a valid kms_key_name for which the associated service account has the
   * required roles/cloudkms.cryptoKeyEncrypterDecrypter role assigned for the
   * key.The Cloud KMS key used by the Log Router can be updated by changing the
   * kms_key_name to a new valid key name.To disable CMEK for the Log Router,
   * set this field to an empty string.See Enabling CMEK for Log Router
   * (https://cloud.google.com/logging/docs/routing/managed-encryption) for more
   * information.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Output only. The service account that will be used by the Log Router to
   * access your Cloud KMS key.Before enabling CMEK, you must first assign the
   * role roles/cloudkms.cryptoKeyEncrypterDecrypter to the service account that
   * will be used to access your Cloud KMS key. Use GetSettings to obtain the
   * service account ID.See Enabling CMEK for Log Router
   * (https://cloud.google.com/logging/docs/routing/managed-encryption) for more
   * information.
   *
   * @var string
   */
  public $kmsServiceAccountId;
  /**
   * Output only. The service account for the given resource container, such as
   * project or folder. Log sinks use this service account as their
   * writer_identity if no custom service account is provided in the request
   * when calling the create sink method.
   *
   * @var string
   */
  public $loggingServiceAccountId;
  /**
   * Output only. The resource name of the settings.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The storage location for the _Default and _Required log buckets
   * of newly created projects and folders, unless the storage location is
   * explicitly provided.Example value: europe-west1.Note: this setting does not
   * affect the location of resources where a location is explicitly provided
   * when created, such as custom log buckets.
   *
   * @var string
   */
  public $storageLocation;

  /**
   * Optional. Overrides the built-in configuration for _Default sink.
   *
   * @param DefaultSinkConfig $defaultSinkConfig
   */
  public function setDefaultSinkConfig(DefaultSinkConfig $defaultSinkConfig)
  {
    $this->defaultSinkConfig = $defaultSinkConfig;
  }
  /**
   * @return DefaultSinkConfig
   */
  public function getDefaultSinkConfig()
  {
    return $this->defaultSinkConfig;
  }
  /**
   * Optional. If set to true, the _Default sink in newly created projects and
   * folders will created in a disabled state. This can be used to automatically
   * disable log storage if there is already an aggregated sink configured in
   * the hierarchy. The _Default sink can be re-enabled manually if needed.
   *
   * @param bool $disableDefaultSink
   */
  public function setDisableDefaultSink($disableDefaultSink)
  {
    $this->disableDefaultSink = $disableDefaultSink;
  }
  /**
   * @return bool
   */
  public function getDisableDefaultSink()
  {
    return $this->disableDefaultSink;
  }
  /**
   * Optional. The resource name for the configured Cloud KMS key.KMS key name
   * format: "projects/[PROJECT_ID]/locations/[LOCATION]/keyRings/[KEYRING]/cryp
   * toKeys/[KEY]" For example:"projects/my-project/locations/us-
   * central1/keyRings/my-ring/cryptoKeys/my-key"To enable CMEK, set this field
   * to a valid kms_key_name for which the associated service account has the
   * required roles/cloudkms.cryptoKeyEncrypterDecrypter role assigned for the
   * key.The Cloud KMS key used by the Log Router can be updated by changing the
   * kms_key_name to a new valid key name.To disable CMEK for the Log Router,
   * set this field to an empty string.See Enabling CMEK for Log Router
   * (https://cloud.google.com/logging/docs/routing/managed-encryption) for more
   * information.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Output only. The service account that will be used by the Log Router to
   * access your Cloud KMS key.Before enabling CMEK, you must first assign the
   * role roles/cloudkms.cryptoKeyEncrypterDecrypter to the service account that
   * will be used to access your Cloud KMS key. Use GetSettings to obtain the
   * service account ID.See Enabling CMEK for Log Router
   * (https://cloud.google.com/logging/docs/routing/managed-encryption) for more
   * information.
   *
   * @param string $kmsServiceAccountId
   */
  public function setKmsServiceAccountId($kmsServiceAccountId)
  {
    $this->kmsServiceAccountId = $kmsServiceAccountId;
  }
  /**
   * @return string
   */
  public function getKmsServiceAccountId()
  {
    return $this->kmsServiceAccountId;
  }
  /**
   * Output only. The service account for the given resource container, such as
   * project or folder. Log sinks use this service account as their
   * writer_identity if no custom service account is provided in the request
   * when calling the create sink method.
   *
   * @param string $loggingServiceAccountId
   */
  public function setLoggingServiceAccountId($loggingServiceAccountId)
  {
    $this->loggingServiceAccountId = $loggingServiceAccountId;
  }
  /**
   * @return string
   */
  public function getLoggingServiceAccountId()
  {
    return $this->loggingServiceAccountId;
  }
  /**
   * Output only. The resource name of the settings.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The storage location for the _Default and _Required log buckets
   * of newly created projects and folders, unless the storage location is
   * explicitly provided.Example value: europe-west1.Note: this setting does not
   * affect the location of resources where a location is explicitly provided
   * when created, such as custom log buckets.
   *
   * @param string $storageLocation
   */
  public function setStorageLocation($storageLocation)
  {
    $this->storageLocation = $storageLocation;
  }
  /**
   * @return string
   */
  public function getStorageLocation()
  {
    return $this->storageLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Settings::class, 'Google_Service_Logging_Settings');
