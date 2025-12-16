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

namespace Google\Service\ServiceManagement;

class ConfigSource extends \Google\Collection
{
  protected $collection_key = 'files';
  protected $filesType = ConfigFile::class;
  protected $filesDataType = 'array';
  /**
   * A unique ID for a specific instance of this message, typically assigned by
   * the client for tracking purpose. If empty, the server may choose to
   * generate one instead.
   *
   * @var string
   */
  public $id;

  /**
   * Set of source configuration files that are used to generate a service
   * configuration (`google.api.Service`).
   *
   * @param ConfigFile[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return ConfigFile[]
   */
  public function getFiles()
  {
    return $this->files;
  }
  /**
   * A unique ID for a specific instance of this message, typically assigned by
   * the client for tracking purpose. If empty, the server may choose to
   * generate one instead.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigSource::class, 'Google_Service_ServiceManagement_ConfigSource');
