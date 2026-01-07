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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1SubjectAltName extends \Google\Model
{
  /**
   * The subject alternative name type is unspecified.
   */
  public const TYPE_SUBJECT_ALT_NAME_TYPE_UNSPECIFIED = 'SUBJECT_ALT_NAME_TYPE_UNSPECIFIED';
  /**
   * The subject alternative name type is an email address adhering to RFC822.
   */
  public const TYPE_RFC822_NAME = 'RFC822_NAME';
  /**
   * The subject alternative name type is a Domain Name System (DNS).
   */
  public const TYPE_DNS_NAME = 'DNS_NAME';
  /**
   * The subject alternative name type is a User Principal Name (UPN).
   */
  public const TYPE_OTHER_NAME_USER_PRINCIPAL_NAME = 'OTHER_NAME_USER_PRINCIPAL_NAME';
  /**
   * The subject alternative name type is a Uniform Resource Identifier (URI).
   */
  public const TYPE_UNIFORM_RESOURCE_IDENTIFIER = 'UNIFORM_RESOURCE_IDENTIFIER';
  /**
   * Output only. The type of the SubjectAltName extension.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The value of the subject alternative name with respect to the
   * `type`.
   *
   * @var string
   */
  public $value;

  /**
   * Output only. The type of the SubjectAltName extension.
   *
   * Accepted values: SUBJECT_ALT_NAME_TYPE_UNSPECIFIED, RFC822_NAME, DNS_NAME,
   * OTHER_NAME_USER_PRINCIPAL_NAME, UNIFORM_RESOURCE_IDENTIFIER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The value of the subject alternative name with respect to the
   * `type`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1SubjectAltName::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1SubjectAltName');
