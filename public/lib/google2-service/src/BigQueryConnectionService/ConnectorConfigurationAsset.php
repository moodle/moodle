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

namespace Google\Service\BigQueryConnectionService;

class ConnectorConfigurationAsset extends \Google\Model
{
  /**
   * Name of the database.
   *
   * @var string
   */
  public $database;
  /**
   * Full Google Cloud resource name -
   * https://cloud.google.com/apis/design/resource_names#full_resource_name.
   * Example: `//library.googleapis.com/shelves/shelf1/books/book2`
   *
   * @var string
   */
  public $googleCloudResource;

  /**
   * Name of the database.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Full Google Cloud resource name -
   * https://cloud.google.com/apis/design/resource_names#full_resource_name.
   * Example: `//library.googleapis.com/shelves/shelf1/books/book2`
   *
   * @param string $googleCloudResource
   */
  public function setGoogleCloudResource($googleCloudResource)
  {
    $this->googleCloudResource = $googleCloudResource;
  }
  /**
   * @return string
   */
  public function getGoogleCloudResource()
  {
    return $this->googleCloudResource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectorConfigurationAsset::class, 'Google_Service_BigQueryConnectionService_ConnectorConfigurationAsset');
