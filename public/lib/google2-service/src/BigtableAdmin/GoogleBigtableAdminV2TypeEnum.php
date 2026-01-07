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

namespace Google\Service\BigtableAdmin;

class GoogleBigtableAdminV2TypeEnum extends \Google\Model
{
  /**
   * The fully qualified name of the protobuf enum message, including package.
   * In the format of "foo.bar.EnumMessage".
   *
   * @var string
   */
  public $enumName;
  /**
   * The ID of the schema bundle that this enum is defined in.
   *
   * @var string
   */
  public $schemaBundleId;

  /**
   * The fully qualified name of the protobuf enum message, including package.
   * In the format of "foo.bar.EnumMessage".
   *
   * @param string $enumName
   */
  public function setEnumName($enumName)
  {
    $this->enumName = $enumName;
  }
  /**
   * @return string
   */
  public function getEnumName()
  {
    return $this->enumName;
  }
  /**
   * The ID of the schema bundle that this enum is defined in.
   *
   * @param string $schemaBundleId
   */
  public function setSchemaBundleId($schemaBundleId)
  {
    $this->schemaBundleId = $schemaBundleId;
  }
  /**
   * @return string
   */
  public function getSchemaBundleId()
  {
    return $this->schemaBundleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeEnum::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeEnum');
