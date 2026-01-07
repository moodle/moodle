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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1BigtableOptions extends \Google\Model
{
  protected $familiesType = GoogleCloudDiscoveryengineV1BigtableOptionsBigtableColumnFamily::class;
  protected $familiesDataType = 'map';
  /**
   * The field name used for saving row key value in the document. The name has
   * to match the pattern `a-zA-Z0-9*`.
   *
   * @var string
   */
  public $keyFieldName;

  /**
   * The mapping from family names to an object that contains column families
   * level information for the given column family. If a family is not present
   * in this map it will be ignored.
   *
   * @param GoogleCloudDiscoveryengineV1BigtableOptionsBigtableColumnFamily[] $families
   */
  public function setFamilies($families)
  {
    $this->families = $families;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1BigtableOptionsBigtableColumnFamily[]
   */
  public function getFamilies()
  {
    return $this->families;
  }
  /**
   * The field name used for saving row key value in the document. The name has
   * to match the pattern `a-zA-Z0-9*`.
   *
   * @param string $keyFieldName
   */
  public function setKeyFieldName($keyFieldName)
  {
    $this->keyFieldName = $keyFieldName;
  }
  /**
   * @return string
   */
  public function getKeyFieldName()
  {
    return $this->keyFieldName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1BigtableOptions::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1BigtableOptions');
