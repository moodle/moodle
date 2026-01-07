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

namespace Google\Service\Gmail;

class KaclsKeyMetadata extends \Google\Model
{
  /**
   * Opaque data generated and used by the key access control list service.
   * Maximum size: 8 KiB.
   *
   * @var string
   */
  public $kaclsData;
  /**
   * The URI of the key access control list service that manages the private
   * key.
   *
   * @var string
   */
  public $kaclsUri;

  /**
   * Opaque data generated and used by the key access control list service.
   * Maximum size: 8 KiB.
   *
   * @param string $kaclsData
   */
  public function setKaclsData($kaclsData)
  {
    $this->kaclsData = $kaclsData;
  }
  /**
   * @return string
   */
  public function getKaclsData()
  {
    return $this->kaclsData;
  }
  /**
   * The URI of the key access control list service that manages the private
   * key.
   *
   * @param string $kaclsUri
   */
  public function setKaclsUri($kaclsUri)
  {
    $this->kaclsUri = $kaclsUri;
  }
  /**
   * @return string
   */
  public function getKaclsUri()
  {
    return $this->kaclsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KaclsKeyMetadata::class, 'Google_Service_Gmail_KaclsKeyMetadata');
