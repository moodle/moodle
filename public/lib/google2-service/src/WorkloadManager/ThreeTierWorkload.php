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

namespace Google\Service\WorkloadManager;

class ThreeTierWorkload extends \Google\Model
{
  protected $apiLayerType = APILayerServer::class;
  protected $apiLayerDataType = '';
  protected $backendType = BackendServer::class;
  protected $backendDataType = '';
  /**
   * @var string
   */
  public $endpoint;
  protected $frontendType = FrontEndServer::class;
  protected $frontendDataType = '';

  /**
   * @param APILayerServer
   */
  public function setApiLayer(APILayerServer $apiLayer)
  {
    $this->apiLayer = $apiLayer;
  }
  /**
   * @return APILayerServer
   */
  public function getApiLayer()
  {
    return $this->apiLayer;
  }
  /**
   * @param BackendServer
   */
  public function setBackend(BackendServer $backend)
  {
    $this->backend = $backend;
  }
  /**
   * @return BackendServer
   */
  public function getBackend()
  {
    return $this->backend;
  }
  /**
   * @param string
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * @param FrontEndServer
   */
  public function setFrontend(FrontEndServer $frontend)
  {
    $this->frontend = $frontend;
  }
  /**
   * @return FrontEndServer
   */
  public function getFrontend()
  {
    return $this->frontend;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThreeTierWorkload::class, 'Google_Service_WorkloadManager_ThreeTierWorkload');
