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

namespace Google\Service\CloudFilestore;

class PscConfig extends \Google\Model
{
  /**
   * Optional. Consumer service project in which the Private Service Connect
   * endpoint would be set up. This is optional, and only relevant in case the
   * network is a shared VPC. If this is not specified, the endpoint would be
   * setup in the VPC host project.
   *
   * @var string
   */
  public $endpointProject;

  /**
   * Optional. Consumer service project in which the Private Service Connect
   * endpoint would be set up. This is optional, and only relevant in case the
   * network is a shared VPC. If this is not specified, the endpoint would be
   * setup in the VPC host project.
   *
   * @param string $endpointProject
   */
  public function setEndpointProject($endpointProject)
  {
    $this->endpointProject = $endpointProject;
  }
  /**
   * @return string
   */
  public function getEndpointProject()
  {
    return $this->endpointProject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscConfig::class, 'Google_Service_CloudFilestore_PscConfig');
