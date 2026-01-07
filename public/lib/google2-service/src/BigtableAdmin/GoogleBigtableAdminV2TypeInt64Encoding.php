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

class GoogleBigtableAdminV2TypeInt64Encoding extends \Google\Model
{
  protected $bigEndianBytesType = GoogleBigtableAdminV2TypeInt64EncodingBigEndianBytes::class;
  protected $bigEndianBytesDataType = '';
  protected $orderedCodeBytesType = GoogleBigtableAdminV2TypeInt64EncodingOrderedCodeBytes::class;
  protected $orderedCodeBytesDataType = '';

  /**
   * Use `BigEndianBytes` encoding.
   *
   * @param GoogleBigtableAdminV2TypeInt64EncodingBigEndianBytes $bigEndianBytes
   */
  public function setBigEndianBytes(GoogleBigtableAdminV2TypeInt64EncodingBigEndianBytes $bigEndianBytes)
  {
    $this->bigEndianBytes = $bigEndianBytes;
  }
  /**
   * @return GoogleBigtableAdminV2TypeInt64EncodingBigEndianBytes
   */
  public function getBigEndianBytes()
  {
    return $this->bigEndianBytes;
  }
  /**
   * Use `OrderedCodeBytes` encoding.
   *
   * @param GoogleBigtableAdminV2TypeInt64EncodingOrderedCodeBytes $orderedCodeBytes
   */
  public function setOrderedCodeBytes(GoogleBigtableAdminV2TypeInt64EncodingOrderedCodeBytes $orderedCodeBytes)
  {
    $this->orderedCodeBytes = $orderedCodeBytes;
  }
  /**
   * @return GoogleBigtableAdminV2TypeInt64EncodingOrderedCodeBytes
   */
  public function getOrderedCodeBytes()
  {
    return $this->orderedCodeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeInt64Encoding::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeInt64Encoding');
