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

namespace Google\Service\CertificateAuthorityService;

class CertificateExtensionConstraints extends \Google\Collection
{
  protected $collection_key = 'knownExtensions';
  protected $additionalExtensionsType = ObjectId::class;
  protected $additionalExtensionsDataType = 'array';
  /**
   * Optional. A set of named X.509 extensions. Will be combined with
   * additional_extensions to determine the full set of X.509 extensions.
   *
   * @var string[]
   */
  public $knownExtensions;

  /**
   * Optional. A set of ObjectIds identifying custom X.509 extensions. Will be
   * combined with known_extensions to determine the full set of X.509
   * extensions.
   *
   * @param ObjectId[] $additionalExtensions
   */
  public function setAdditionalExtensions($additionalExtensions)
  {
    $this->additionalExtensions = $additionalExtensions;
  }
  /**
   * @return ObjectId[]
   */
  public function getAdditionalExtensions()
  {
    return $this->additionalExtensions;
  }
  /**
   * Optional. A set of named X.509 extensions. Will be combined with
   * additional_extensions to determine the full set of X.509 extensions.
   *
   * @param string[] $knownExtensions
   */
  public function setKnownExtensions($knownExtensions)
  {
    $this->knownExtensions = $knownExtensions;
  }
  /**
   * @return string[]
   */
  public function getKnownExtensions()
  {
    return $this->knownExtensions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateExtensionConstraints::class, 'Google_Service_CertificateAuthorityService_CertificateExtensionConstraints');
