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

namespace Google\Service\CloudKMS;

class AutokeyConfig extends \Google\Model
{
  /**
   * The state of the AutokeyConfig is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The AutokeyConfig is currently active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * A previously configured key project has been deleted and the current
   * AutokeyConfig is unusable.
   */
  public const STATE_KEY_PROJECT_DELETED = 'KEY_PROJECT_DELETED';
  /**
   * The AutokeyConfig is not yet initialized or has been reset to its default
   * uninitialized state.
   */
  public const STATE_UNINITIALIZED = 'UNINITIALIZED';
  /**
   * Optional. A checksum computed by the server based on the value of other
   * fields. This may be sent on update requests to ensure that the client has
   * an up-to-date value before proceeding. The request will be rejected with an
   * ABORTED error on a mismatched etag.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Name of the key project, e.g. `projects/{PROJECT_ID}` or
   * `projects/{PROJECT_NUMBER}`, where Cloud KMS Autokey will provision a new
   * CryptoKey when a KeyHandle is created. On UpdateAutokeyConfig, the caller
   * will require `cloudkms.cryptoKeys.setIamPolicy` permission on this key
   * project. Once configured, for Cloud KMS Autokey to function properly, this
   * key project must have the Cloud KMS API activated and the Cloud KMS Service
   * Agent for this key project must be granted the `cloudkms.admin` role (or
   * pertinent permissions). A request with an empty key project field will
   * clear the configuration.
   *
   * @var string
   */
  public $keyProject;
  /**
   * Identifier. Name of the AutokeyConfig resource, e.g.
   * `folders/{FOLDER_NUMBER}/autokeyConfig`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state for the AutokeyConfig.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. A checksum computed by the server based on the value of other
   * fields. This may be sent on update requests to ensure that the client has
   * an up-to-date value before proceeding. The request will be rejected with an
   * ABORTED error on a mismatched etag.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Name of the key project, e.g. `projects/{PROJECT_ID}` or
   * `projects/{PROJECT_NUMBER}`, where Cloud KMS Autokey will provision a new
   * CryptoKey when a KeyHandle is created. On UpdateAutokeyConfig, the caller
   * will require `cloudkms.cryptoKeys.setIamPolicy` permission on this key
   * project. Once configured, for Cloud KMS Autokey to function properly, this
   * key project must have the Cloud KMS API activated and the Cloud KMS Service
   * Agent for this key project must be granted the `cloudkms.admin` role (or
   * pertinent permissions). A request with an empty key project field will
   * clear the configuration.
   *
   * @param string $keyProject
   */
  public function setKeyProject($keyProject)
  {
    $this->keyProject = $keyProject;
  }
  /**
   * @return string
   */
  public function getKeyProject()
  {
    return $this->keyProject;
  }
  /**
   * Identifier. Name of the AutokeyConfig resource, e.g.
   * `folders/{FOLDER_NUMBER}/autokeyConfig`
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
   * Output only. The state for the AutokeyConfig.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, KEY_PROJECT_DELETED,
   * UNINITIALIZED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutokeyConfig::class, 'Google_Service_CloudKMS_AutokeyConfig');
