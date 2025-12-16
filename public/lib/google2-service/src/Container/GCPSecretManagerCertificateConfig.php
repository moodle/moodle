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

namespace Google\Service\Container;

class GCPSecretManagerCertificateConfig extends \Google\Model
{
  /**
   * Secret URI, in the form
   * "projects/$PROJECT_ID/secrets/$SECRET_NAME/versions/$VERSION". Version can
   * be fixed (e.g. "2") or "latest"
   *
   * @var string
   */
  public $secretUri;

  /**
   * Secret URI, in the form
   * "projects/$PROJECT_ID/secrets/$SECRET_NAME/versions/$VERSION". Version can
   * be fixed (e.g. "2") or "latest"
   *
   * @param string $secretUri
   */
  public function setSecretUri($secretUri)
  {
    $this->secretUri = $secretUri;
  }
  /**
   * @return string
   */
  public function getSecretUri()
  {
    return $this->secretUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GCPSecretManagerCertificateConfig::class, 'Google_Service_Container_GCPSecretManagerCertificateConfig');
