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

namespace Google\Service\CloudAlloyDBAdmin;

class PrimaryConfig extends \Google\Collection
{
  protected $collection_key = 'secondaryClusterNames';
  /**
   * Output only. Names of the clusters that are replicating from this cluster.
   *
   * @var string[]
   */
  public $secondaryClusterNames;

  /**
   * Output only. Names of the clusters that are replicating from this cluster.
   *
   * @param string[] $secondaryClusterNames
   */
  public function setSecondaryClusterNames($secondaryClusterNames)
  {
    $this->secondaryClusterNames = $secondaryClusterNames;
  }
  /**
   * @return string[]
   */
  public function getSecondaryClusterNames()
  {
    return $this->secondaryClusterNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrimaryConfig::class, 'Google_Service_CloudAlloyDBAdmin_PrimaryConfig');
