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

namespace Google\Service\BigLakeService;

class HiveDatabaseOptions extends \Google\Model
{
  /**
   * Cloud Storage folder URI where the database data is stored, starting with
   * "gs://".
   *
   * @var string
   */
  public $locationUri;
  /**
   * Stores user supplied Hive database parameters.
   *
   * @var string[]
   */
  public $parameters;

  /**
   * Cloud Storage folder URI where the database data is stored, starting with
   * "gs://".
   *
   * @param string $locationUri
   */
  public function setLocationUri($locationUri)
  {
    $this->locationUri = $locationUri;
  }
  /**
   * @return string
   */
  public function getLocationUri()
  {
    return $this->locationUri;
  }
  /**
   * Stores user supplied Hive database parameters.
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HiveDatabaseOptions::class, 'Google_Service_BigLakeService_HiveDatabaseOptions');
