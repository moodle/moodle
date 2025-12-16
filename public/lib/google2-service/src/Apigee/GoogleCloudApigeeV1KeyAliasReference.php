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

class GoogleCloudApigeeV1KeyAliasReference extends \Google\Model
{
  /**
   * Alias ID. Must exist in the keystore referred to by the reference.
   *
   * @var string
   */
  public $aliasId;
  /**
   * Reference name in the following format:
   * `organizations/{org}/environments/{env}/references/{reference}`
   *
   * @var string
   */
  public $reference;

  /**
   * Alias ID. Must exist in the keystore referred to by the reference.
   *
   * @param string $aliasId
   */
  public function setAliasId($aliasId)
  {
    $this->aliasId = $aliasId;
  }
  /**
   * @return string
   */
  public function getAliasId()
  {
    return $this->aliasId;
  }
  /**
   * Reference name in the following format:
   * `organizations/{org}/environments/{env}/references/{reference}`
   *
   * @param string $reference
   */
  public function setReference($reference)
  {
    $this->reference = $reference;
  }
  /**
   * @return string
   */
  public function getReference()
  {
    return $this->reference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1KeyAliasReference::class, 'Google_Service_Apigee_GoogleCloudApigeeV1KeyAliasReference');
