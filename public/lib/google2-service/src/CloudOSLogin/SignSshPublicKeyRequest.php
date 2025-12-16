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

namespace Google\Service\CloudOSLogin;

class SignSshPublicKeyRequest extends \Google\Model
{
  /**
   * The App Engine instance to sign the SSH public key for. Expected format:
   * apps/{app}/services/{service}/versions/{version}/instances/{instance}
   *
   * @var string
   */
  public $appEngineInstance;
  /**
   * The Compute instance to sign the SSH public key for. Expected format:
   * projects/{project}/zones/{zone}/instances/{numeric_instance_id}
   *
   * @var string
   */
  public $computeInstance;
  /**
   * Optional. The service account for the instance. If the instance in question
   * does not have a service account, this field should be left empty. If the
   * wrong service account is provided, this operation will return a signed
   * certificate that will not be accepted by the VM.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Required. The SSH public key to sign.
   *
   * @var string
   */
  public $sshPublicKey;

  /**
   * The App Engine instance to sign the SSH public key for. Expected format:
   * apps/{app}/services/{service}/versions/{version}/instances/{instance}
   *
   * @param string $appEngineInstance
   */
  public function setAppEngineInstance($appEngineInstance)
  {
    $this->appEngineInstance = $appEngineInstance;
  }
  /**
   * @return string
   */
  public function getAppEngineInstance()
  {
    return $this->appEngineInstance;
  }
  /**
   * The Compute instance to sign the SSH public key for. Expected format:
   * projects/{project}/zones/{zone}/instances/{numeric_instance_id}
   *
   * @param string $computeInstance
   */
  public function setComputeInstance($computeInstance)
  {
    $this->computeInstance = $computeInstance;
  }
  /**
   * @return string
   */
  public function getComputeInstance()
  {
    return $this->computeInstance;
  }
  /**
   * Optional. The service account for the instance. If the instance in question
   * does not have a service account, this field should be left empty. If the
   * wrong service account is provided, this operation will return a signed
   * certificate that will not be accepted by the VM.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Required. The SSH public key to sign.
   *
   * @param string $sshPublicKey
   */
  public function setSshPublicKey($sshPublicKey)
  {
    $this->sshPublicKey = $sshPublicKey;
  }
  /**
   * @return string
   */
  public function getSshPublicKey()
  {
    return $this->sshPublicKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignSshPublicKeyRequest::class, 'Google_Service_CloudOSLogin_SignSshPublicKeyRequest');
