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

namespace Google\Service\Fitness;

class Application extends \Google\Model
{
  /**
   * An optional URI that can be used to link back to the application.
   *
   * @var string
   */
  public $detailsUrl;
  /**
   * The name of this application. This is required for REST clients, but we do
   * not enforce uniqueness of this name. It is provided as a matter of
   * convenience for other developers who would like to identify which REST
   * created an Application or Data Source.
   *
   * @var string
   */
  public $name;
  /**
   * Package name for this application. This is used as a unique identifier when
   * created by Android applications, but cannot be specified by REST clients.
   * REST clients will have their developer project number reflected into the
   * Data Source data stream IDs, instead of the packageName.
   *
   * @var string
   */
  public $packageName;
  /**
   * Version of the application. You should update this field whenever the
   * application changes in a way that affects the computation of the data.
   *
   * @var string
   */
  public $version;

  /**
   * An optional URI that can be used to link back to the application.
   *
   * @param string $detailsUrl
   */
  public function setDetailsUrl($detailsUrl)
  {
    $this->detailsUrl = $detailsUrl;
  }
  /**
   * @return string
   */
  public function getDetailsUrl()
  {
    return $this->detailsUrl;
  }
  /**
   * The name of this application. This is required for REST clients, but we do
   * not enforce uniqueness of this name. It is provided as a matter of
   * convenience for other developers who would like to identify which REST
   * created an Application or Data Source.
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
   * Package name for this application. This is used as a unique identifier when
   * created by Android applications, but cannot be specified by REST clients.
   * REST clients will have their developer project number reflected into the
   * Data Source data stream IDs, instead of the packageName.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * Version of the application. You should update this field whenever the
   * application changes in a way that affects the computation of the data.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Application::class, 'Google_Service_Fitness_Application');
