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

namespace Google\Service\GKEOnPrem;

class VmwareVsphereConfig extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * The name of the vCenter datastore. Inherited from the user cluster.
   *
   * @var string
   */
  public $datastore;
  /**
   * Vsphere host groups to apply to all VMs in the node pool
   *
   * @var string[]
   */
  public $hostGroups;
  protected $tagsType = VmwareVsphereTag::class;
  protected $tagsDataType = 'array';

  /**
   * The name of the vCenter datastore. Inherited from the user cluster.
   *
   * @param string $datastore
   */
  public function setDatastore($datastore)
  {
    $this->datastore = $datastore;
  }
  /**
   * @return string
   */
  public function getDatastore()
  {
    return $this->datastore;
  }
  /**
   * Vsphere host groups to apply to all VMs in the node pool
   *
   * @param string[] $hostGroups
   */
  public function setHostGroups($hostGroups)
  {
    $this->hostGroups = $hostGroups;
  }
  /**
   * @return string[]
   */
  public function getHostGroups()
  {
    return $this->hostGroups;
  }
  /**
   * Tags to apply to VMs.
   *
   * @param VmwareVsphereTag[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return VmwareVsphereTag[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareVsphereConfig::class, 'Google_Service_GKEOnPrem_VmwareVsphereConfig');
