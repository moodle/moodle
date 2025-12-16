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

class GoogleCloudDialogflowCxV3ToolTLSConfigCACert extends \Google\Model
{
  /**
   * Required. The allowed custom CA certificates (in DER format) for HTTPS
   * verification. This overrides the default SSL trust store. If this is empty
   * or unspecified, Dialogflow will use Google's default trust store to verify
   * certificates. N.B. Make sure the HTTPS server certificates are signed with
   * "subject alt name". For instance a certificate can be self-signed using the
   * following command: ``` openssl x509 -req -days 200 -in example.com.csr \
   * -signkey example.com.key \ -out example.com.crt \ -extfile <(printf
   * "\nsubjectAltName='DNS:www.example.com'") ```
   *
   * @var string
   */
  public $cert;
  /**
   * Required. The name of the allowed custom CA certificates. This can be used
   * to disambiguate the custom CA certificates.
   *
   * @var string
   */
  public $displayName;

  /**
   * Required. The allowed custom CA certificates (in DER format) for HTTPS
   * verification. This overrides the default SSL trust store. If this is empty
   * or unspecified, Dialogflow will use Google's default trust store to verify
   * certificates. N.B. Make sure the HTTPS server certificates are signed with
   * "subject alt name". For instance a certificate can be self-signed using the
   * following command: ``` openssl x509 -req -days 200 -in example.com.csr \
   * -signkey example.com.key \ -out example.com.crt \ -extfile <(printf
   * "\nsubjectAltName='DNS:www.example.com'") ```
   *
   * @param string $cert
   */
  public function setCert($cert)
  {
    $this->cert = $cert;
  }
  /**
   * @return string
   */
  public function getCert()
  {
    return $this->cert;
  }
  /**
   * Required. The name of the allowed custom CA certificates. This can be used
   * to disambiguate the custom CA certificates.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ToolTLSConfigCACert::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolTLSConfigCACert');
