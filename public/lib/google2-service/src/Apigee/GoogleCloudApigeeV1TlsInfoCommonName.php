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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1TlsInfoCommonName extends \Google\Model
{
  /**
   * The TLS Common Name string of the certificate.
   *
   * @var string
   */
  public $value;
  /**
   * Indicates whether the cert should be matched against as a wildcard cert.
   *
   * @var bool
   */
  public $wildcardMatch;

  /**
   * The TLS Common Name string of the certificate.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Indicates whether the cert should be matched against as a wildcard cert.
   *
   * @param bool $wildcardMatch
   */
  public function setWildcardMatch($wildcardMatch)
  {
    $this->wildcardMatch = $wildcardMatch;
  }
  /**
   * @return bool
   */
  public function getWildcardMatch()
  {
    return $this->wildcardMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1TlsInfoCommonName::class, 'Google_Service_Apigee_GoogleCloudApigeeV1TlsInfoCommonName');
