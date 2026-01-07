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

class GoogleBigtableAdminV2TypeBytes extends \Google\Model
{
  protected $encodingType = GoogleBigtableAdminV2TypeBytesEncoding::class;
  protected $encodingDataType = '';

  /**
   * The encoding to use when converting to or from lower level types.
   *
   * @param GoogleBigtableAdminV2TypeBytesEncoding $encoding
   */
  public function setEncoding(GoogleBigtableAdminV2TypeBytesEncoding $encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return GoogleBigtableAdminV2TypeBytesEncoding
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeBytes::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeBytes');
