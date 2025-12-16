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

namespace Google\Service\Logging;

class AppHubApplication extends \Google\Model
{
  /**
   * Resource container that owns the application. Example:
   * "projects/management_project"
   *
   * @var string
   */
  public $container;
  /**
   * Application Id. Example: "my-app"
   *
   * @var string
   */
  public $id;
  /**
   * Location associated with the Application. Example: "us-east1"
   *
   * @var string
   */
  public $location;

  /**
   * Resource container that owns the application. Example:
   * "projects/management_project"
   *
   * @param string $container
   */
  public function setContainer($container)
  {
    $this->container = $container;
  }
  /**
   * @return string
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * Application Id. Example: "my-app"
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Location associated with the Application. Example: "us-east1"
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppHubApplication::class, 'Google_Service_Logging_AppHubApplication');
