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

class GoogleCloudDiscoveryengineV1ImportUserEventsRequest extends \Google\Model
{
  protected $bigquerySourceType = GoogleCloudDiscoveryengineV1BigQuerySource::class;
  protected $bigquerySourceDataType = '';
  protected $errorConfigType = GoogleCloudDiscoveryengineV1ImportErrorConfig::class;
  protected $errorConfigDataType = '';
  protected $gcsSourceType = GoogleCloudDiscoveryengineV1GcsSource::class;
  protected $gcsSourceDataType = '';
  protected $inlineSourceType = GoogleCloudDiscoveryengineV1ImportUserEventsRequestInlineSource::class;
  protected $inlineSourceDataType = '';

  /**
   * BigQuery input source.
   *
   * @param GoogleCloudDiscoveryengineV1BigQuerySource $bigquerySource
   */
  public function setBigquerySource(GoogleCloudDiscoveryengineV1BigQuerySource $bigquerySource)
  {
    $this->bigquerySource = $bigquerySource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1BigQuerySource
   */
  public function getBigquerySource()
  {
    return $this->bigquerySource;
  }
  /**
   * The desired location of errors incurred during the Import. Cannot be set
   * for inline user event imports.
   *
   * @param GoogleCloudDiscoveryengineV1ImportErrorConfig $errorConfig
   */
  public function setErrorConfig(GoogleCloudDiscoveryengineV1ImportErrorConfig $errorConfig)
  {
    $this->errorConfig = $errorConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ImportErrorConfig
   */
  public function getErrorConfig()
  {
    return $this->errorConfig;
  }
  /**
   * Cloud Storage location for the input content.
   *
   * @param GoogleCloudDiscoveryengineV1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudDiscoveryengineV1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * The Inline source for the input content for UserEvents.
   *
   * @param GoogleCloudDiscoveryengineV1ImportUserEventsRequestInlineSource $inlineSource
   */
  public function setInlineSource(GoogleCloudDiscoveryengineV1ImportUserEventsRequestInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ImportUserEventsRequestInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ImportUserEventsRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ImportUserEventsRequest');
