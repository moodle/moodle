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

namespace Google\Service\CloudSearch;

class StartUploadItemRequest extends \Google\Model
{
  /**
   * The name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   *
   * @var string
   */
  public $connectorName;
  protected $debugOptionsType = DebugOptions::class;
  protected $debugOptionsDataType = '';

  /**
   * The name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   *
   * @param string $connectorName
   */
  public function setConnectorName($connectorName)
  {
    $this->connectorName = $connectorName;
  }
  /**
   * @return string
   */
  public function getConnectorName()
  {
    return $this->connectorName;
  }
  /**
   * Common debug options.
   *
   * @param DebugOptions $debugOptions
   */
  public function setDebugOptions(DebugOptions $debugOptions)
  {
    $this->debugOptions = $debugOptions;
  }
  /**
   * @return DebugOptions
   */
  public function getDebugOptions()
  {
    return $this->debugOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StartUploadItemRequest::class, 'Google_Service_CloudSearch_StartUploadItemRequest');
