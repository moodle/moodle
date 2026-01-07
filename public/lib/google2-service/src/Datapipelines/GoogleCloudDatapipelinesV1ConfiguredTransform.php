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

namespace Google\Service\Datapipelines;

class GoogleCloudDatapipelinesV1ConfiguredTransform extends \Google\Model
{
  protected $configType = GoogleCloudDatapipelinesV1Row::class;
  protected $configDataType = '';
  /**
   * @var string
   */
  public $uniformResourceName;

  /**
   * @param GoogleCloudDatapipelinesV1Row
   */
  public function setConfig(GoogleCloudDatapipelinesV1Row $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleCloudDatapipelinesV1Row
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * @param string
   */
  public function setUniformResourceName($uniformResourceName)
  {
    $this->uniformResourceName = $uniformResourceName;
  }
  /**
   * @return string
   */
  public function getUniformResourceName()
  {
    return $this->uniformResourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1ConfiguredTransform::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1ConfiguredTransform');
