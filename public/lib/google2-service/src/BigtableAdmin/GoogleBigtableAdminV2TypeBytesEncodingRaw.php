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

class GoogleBigtableAdminV2TypeBytesEncodingRaw extends \Google\Model
{
  /**
   * If set, allows NULL values to be encoded as the empty string "". The actual
   * empty string, or any value which only contains the null byte `0x00`, has
   * one more null byte appended.
   *
   * @var bool
   */
  public $escapeNulls;

  /**
   * If set, allows NULL values to be encoded as the empty string "". The actual
   * empty string, or any value which only contains the null byte `0x00`, has
   * one more null byte appended.
   *
   * @param bool $escapeNulls
   */
  public function setEscapeNulls($escapeNulls)
  {
    $this->escapeNulls = $escapeNulls;
  }
  /**
   * @return bool
   */
  public function getEscapeNulls()
  {
    return $this->escapeNulls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeBytesEncodingRaw::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeBytesEncodingRaw');
