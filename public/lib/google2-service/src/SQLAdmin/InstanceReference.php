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

namespace Google\Service\SQLAdmin;

class InstanceReference extends \Google\Model
{
  /**
   * The name of the Cloud SQL instance being referenced. This does not include
   * the project ID.
   *
   * @var string
   */
  public $name;
  /**
   * The project ID of the Cloud SQL instance being referenced. The default is
   * the same project ID as the instance references it.
   *
   * @var string
   */
  public $project;
  /**
   * The region of the Cloud SQL instance being referenced.
   *
   * @var string
   */
  public $region;

  /**
   * The name of the Cloud SQL instance being referenced. This does not include
   * the project ID.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The project ID of the Cloud SQL instance being referenced. The default is
   * the same project ID as the instance references it.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * The region of the Cloud SQL instance being referenced.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceReference::class, 'Google_Service_SQLAdmin_InstanceReference');
