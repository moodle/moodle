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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3AgentClientCertificateSettings extends \Google\Model
{
  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * passphrase. 'passphrase' should be left unset if the private key is not
   * encrypted. Format: `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @var string
   */
  public $passphrase;
  /**
   * Required. The name of the SecretManager secret version resource storing the
   * private key encoded in PEM format. Format:
   * `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @var string
   */
  public $privateKey;
  /**
   * Required. The ssl certificate encoded in PEM format. This string must
   * include the begin header and end footer lines.
   *
   * @var string
   */
  public $sslCertificate;

  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * passphrase. 'passphrase' should be left unset if the private key is not
   * encrypted. Format: `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @param string $passphrase
   */
  public function setPassphrase($passphrase)
  {
    $this->passphrase = $passphrase;
  }
  /**
   * @return string
   */
  public function getPassphrase()
  {
    return $this->passphrase;
  }
  /**
   * Required. The name of the SecretManager secret version resource storing the
   * private key encoded in PEM format. Format:
   * `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @param string $privateKey
   */
  public function setPrivateKey($privateKey)
  {
    $this->privateKey = $privateKey;
  }
  /**
   * @return string
   */
  public function getPrivateKey()
  {
    return $this->privateKey;
  }
  /**
   * Required. The ssl certificate encoded in PEM format. This string must
   * include the begin header and end footer lines.
   *
   * @param string $sslCertificate
   */
  public function setSslCertificate($sslCertificate)
  {
    $this->sslCertificate = $sslCertificate;
  }
  /**
   * @return string
   */
  public function getSslCertificate()
  {
    return $this->sslCertificate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3AgentClientCertificateSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3AgentClientCertificateSettings');
