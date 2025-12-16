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

class GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfigStreamingError extends \Google\Model
{
  /**
   * Streaming error reason unspecified.
   */
  public const STREAMING_ERROR_REASON_STREAMING_ERROR_REASON_UNSPECIFIED = 'STREAMING_ERROR_REASON_UNSPECIFIED';
  /**
   * Some error occurred while setting up resources for realtime sync.
   */
  public const STREAMING_ERROR_REASON_STREAMING_SETUP_ERROR = 'STREAMING_SETUP_ERROR';
  /**
   * Some error was encountered while running realtime sync for the connector.
   */
  public const STREAMING_ERROR_REASON_STREAMING_SYNC_ERROR = 'STREAMING_SYNC_ERROR';
  /**
   * Ingress endpoint is required when setting up realtime sync in private
   * connectivity.
   */
  public const STREAMING_ERROR_REASON_INGRESS_ENDPOINT_REQUIRED = 'INGRESS_ENDPOINT_REQUIRED';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * Optional. Streaming error.
   *
   * @var string
   */
  public $streamingErrorReason;

  /**
   * Optional. Error details.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Optional. Streaming error.
   *
   * Accepted values: STREAMING_ERROR_REASON_UNSPECIFIED, STREAMING_SETUP_ERROR,
   * STREAMING_SYNC_ERROR, INGRESS_ENDPOINT_REQUIRED
   *
   * @param self::STREAMING_ERROR_REASON_* $streamingErrorReason
   */
  public function setStreamingErrorReason($streamingErrorReason)
  {
    $this->streamingErrorReason = $streamingErrorReason;
  }
  /**
   * @return self::STREAMING_ERROR_REASON_*
   */
  public function getStreamingErrorReason()
  {
    return $this->streamingErrorReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfigStreamingError::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1DataConnectorRealtimeSyncConfigStreamingError');
