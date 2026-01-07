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

class GoogleBigtableAdminV2TypeStructEncoding extends \Google\Model
{
  protected $delimitedBytesType = GoogleBigtableAdminV2TypeStructEncodingDelimitedBytes::class;
  protected $delimitedBytesDataType = '';
  protected $orderedCodeBytesType = GoogleBigtableAdminV2TypeStructEncodingOrderedCodeBytes::class;
  protected $orderedCodeBytesDataType = '';
  protected $singletonType = GoogleBigtableAdminV2TypeStructEncodingSingleton::class;
  protected $singletonDataType = '';

  /**
   * Use `DelimitedBytes` encoding.
   *
   * @param GoogleBigtableAdminV2TypeStructEncodingDelimitedBytes $delimitedBytes
   */
  public function setDelimitedBytes(GoogleBigtableAdminV2TypeStructEncodingDelimitedBytes $delimitedBytes)
  {
    $this->delimitedBytes = $delimitedBytes;
  }
  /**
   * @return GoogleBigtableAdminV2TypeStructEncodingDelimitedBytes
   */
  public function getDelimitedBytes()
  {
    return $this->delimitedBytes;
  }
  /**
   * User `OrderedCodeBytes` encoding.
   *
   * @param GoogleBigtableAdminV2TypeStructEncodingOrderedCodeBytes $orderedCodeBytes
   */
  public function setOrderedCodeBytes(GoogleBigtableAdminV2TypeStructEncodingOrderedCodeBytes $orderedCodeBytes)
  {
    $this->orderedCodeBytes = $orderedCodeBytes;
  }
  /**
   * @return GoogleBigtableAdminV2TypeStructEncodingOrderedCodeBytes
   */
  public function getOrderedCodeBytes()
  {
    return $this->orderedCodeBytes;
  }
  /**
   * Use `Singleton` encoding.
   *
   * @param GoogleBigtableAdminV2TypeStructEncodingSingleton $singleton
   */
  public function setSingleton(GoogleBigtableAdminV2TypeStructEncodingSingleton $singleton)
  {
    $this->singleton = $singleton;
  }
  /**
   * @return GoogleBigtableAdminV2TypeStructEncodingSingleton
   */
  public function getSingleton()
  {
    return $this->singleton;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeStructEncoding::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeStructEncoding');
