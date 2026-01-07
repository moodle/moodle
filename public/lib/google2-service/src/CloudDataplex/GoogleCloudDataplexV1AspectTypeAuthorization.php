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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1AspectTypeAuthorization extends \Google\Model
{
  /**
   * Immutable. The IAM permission grantable on the EntryGroup to allow access
   * to instantiate Aspects of Dataplex Universal Catalog owned AspectTypes,
   * only settable for Dataplex Universal Catalog owned Types.
   *
   * @var string
   */
  public $alternateUsePermission;

  /**
   * Immutable. The IAM permission grantable on the EntryGroup to allow access
   * to instantiate Aspects of Dataplex Universal Catalog owned AspectTypes,
   * only settable for Dataplex Universal Catalog owned Types.
   *
   * @param string $alternateUsePermission
   */
  public function setAlternateUsePermission($alternateUsePermission)
  {
    $this->alternateUsePermission = $alternateUsePermission;
  }
  /**
   * @return string
   */
  public function getAlternateUsePermission()
  {
    return $this->alternateUsePermission;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1AspectTypeAuthorization::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1AspectTypeAuthorization');
