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

class GoogleBigtableAdminV2TypeTimestampEncoding extends \Google\Model
{
  protected $unixMicrosInt64Type = GoogleBigtableAdminV2TypeInt64Encoding::class;
  protected $unixMicrosInt64DataType = '';

  /**
   * Encodes the number of microseconds since the Unix epoch using the given
   * `Int64` encoding. Values must be microsecond-aligned. Compatible with: -
   * Java `Instant.truncatedTo()` with `ChronoUnit.MICROS`
   *
   * @param GoogleBigtableAdminV2TypeInt64Encoding $unixMicrosInt64
   */
  public function setUnixMicrosInt64(GoogleBigtableAdminV2TypeInt64Encoding $unixMicrosInt64)
  {
    $this->unixMicrosInt64 = $unixMicrosInt64;
  }
  /**
   * @return GoogleBigtableAdminV2TypeInt64Encoding
   */
  public function getUnixMicrosInt64()
  {
    return $this->unixMicrosInt64;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeTimestampEncoding::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeTimestampEncoding');
