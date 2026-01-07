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

class GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfig extends \Google\Model
{
  /**
   * Optional. The ID of the Secret Manager secret used for webhook secret.
   *
   * @var string
   */
  public $realtimeSyncSecret;
  protected $streamingErrorType = GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfigStreamingError::class;
  protected $streamingErrorDataType = '';
  /**
   * Optional. Webhook url for the connector to specify additional params for
   * realtime sync.
   *
   * @var string
   */
  public $webhookUri;

  /**
   * Optional. The ID of the Secret Manager secret used for webhook secret.
   *
   * @param string $realtimeSyncSecret
   */
  public function setRealtimeSyncSecret($realtimeSyncSecret)
  {
    $this->realtimeSyncSecret = $realtimeSyncSecret;
  }
  /**
   * @return string
   */
  public function getRealtimeSyncSecret()
  {
    return $this->realtimeSyncSecret;
  }
  /**
   * Optional. Streaming error details.
   *
   * @param GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfigStreamingError $streamingError
   */
  public function setStreamingError(GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfigStreamingError $streamingError)
  {
    $this->streamingError = $streamingError;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfigStreamingError
   */
  public function getStreamingError()
  {
    return $this->streamingError;
  }
  /**
   * Optional. Webhook url for the connector to specify additional params for
   * realtime sync.
   *
   * @param string $webhookUri
   */
  public function setWebhookUri($webhookUri)
  {
    $this->webhookUri = $webhookUri;
  }
  /**
   * @return string
   */
  public function getWebhookUri()
  {
    return $this->webhookUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfig');
