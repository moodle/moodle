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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaImportUserEventsRequest extends \Google\Model
{
  protected $bigquerySourceType = GoogleCloudDiscoveryengineV1betaBigQuerySource::class;
  protected $bigquerySourceDataType = '';
  protected $errorConfigType = GoogleCloudDiscoveryengineV1betaImportErrorConfig::class;
  protected $errorConfigDataType = '';
  protected $gcsSourceType = GoogleCloudDiscoveryengineV1betaGcsSource::class;
  protected $gcsSourceDataType = '';
  protected $inlineSourceType = GoogleCloudDiscoveryengineV1betaImportUserEventsRequestInlineSource::class;
  protected $inlineSourceDataType = '';

  /**
   * @param GoogleCloudDiscoveryengineV1betaBigQuerySource
   */
  public function setBigquerySource(GoogleCloudDiscoveryengineV1betaBigQuerySource $bigquerySource)
  {
    $this->bigquerySource = $bigquerySource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaBigQuerySource
   */
  public function getBigquerySource()
  {
    return $this->bigquerySource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaImportErrorConfig
   */
  public function setErrorConfig(GoogleCloudDiscoveryengineV1betaImportErrorConfig $errorConfig)
  {
    $this->errorConfig = $errorConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaImportErrorConfig
   */
  public function getErrorConfig()
  {
    return $this->errorConfig;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaGcsSource
   */
  public function setGcsSource(GoogleCloudDiscoveryengineV1betaGcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaGcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaImportUserEventsRequestInlineSource
   */
  public function setInlineSource(GoogleCloudDiscoveryengineV1betaImportUserEventsRequestInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaImportUserEventsRequestInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaImportUserEventsRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaImportUserEventsRequest');
