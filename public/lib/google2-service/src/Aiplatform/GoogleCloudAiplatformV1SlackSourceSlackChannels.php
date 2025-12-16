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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SlackSourceSlackChannels extends \Google\Collection
{
  protected $collection_key = 'channels';
  protected $apiKeyConfigType = GoogleCloudAiplatformV1ApiAuthApiKeyConfig::class;
  protected $apiKeyConfigDataType = '';
  protected $channelsType = GoogleCloudAiplatformV1SlackSourceSlackChannelsSlackChannel::class;
  protected $channelsDataType = 'array';

  /**
   * Required. The SecretManager secret version resource name (e.g.
   * projects/{project}/secrets/{secret}/versions/{version}) storing the Slack
   * channel access token that has access to the slack channel IDs. See:
   * https://api.slack.com/tutorials/tracks/getting-a-token.
   *
   * @param GoogleCloudAiplatformV1ApiAuthApiKeyConfig $apiKeyConfig
   */
  public function setApiKeyConfig(GoogleCloudAiplatformV1ApiAuthApiKeyConfig $apiKeyConfig)
  {
    $this->apiKeyConfig = $apiKeyConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ApiAuthApiKeyConfig
   */
  public function getApiKeyConfig()
  {
    return $this->apiKeyConfig;
  }
  /**
   * Required. The Slack channel IDs.
   *
   * @param GoogleCloudAiplatformV1SlackSourceSlackChannelsSlackChannel[] $channels
   */
  public function setChannels($channels)
  {
    $this->channels = $channels;
  }
  /**
   * @return GoogleCloudAiplatformV1SlackSourceSlackChannelsSlackChannel[]
   */
  public function getChannels()
  {
    return $this->channels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SlackSourceSlackChannels::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SlackSourceSlackChannels');
