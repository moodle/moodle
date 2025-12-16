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

namespace Google\Service\Eventarc;

class GoogleCloudEventarcV1PipelineDestination extends \Google\Model
{
  protected $authenticationConfigType = GoogleCloudEventarcV1PipelineDestinationAuthenticationConfig::class;
  protected $authenticationConfigDataType = '';
  protected $httpEndpointType = GoogleCloudEventarcV1PipelineDestinationHttpEndpoint::class;
  protected $httpEndpointDataType = '';
  /**
   * Optional. The resource name of the Message Bus to which events should be
   * published. The Message Bus resource should exist in the same project as the
   * Pipeline. Format:
   * `projects/{project}/locations/{location}/messageBuses/{message_bus}`
   *
   * @var string
   */
  public $messageBus;
  protected $networkConfigType = GoogleCloudEventarcV1PipelineDestinationNetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $outputPayloadFormatType = GoogleCloudEventarcV1PipelineMessagePayloadFormat::class;
  protected $outputPayloadFormatDataType = '';
  /**
   * Optional. The resource name of the Pub/Sub topic to which events should be
   * published. Format: `projects/{project}/locations/{location}/topics/{topic}`
   *
   * @var string
   */
  public $topic;
  /**
   * Optional. The resource name of the Workflow whose Executions are triggered
   * by the events. The Workflow resource should be deployed in the same project
   * as the Pipeline. Format:
   * `projects/{project}/locations/{location}/workflows/{workflow}`
   *
   * @var string
   */
  public $workflow;

  /**
   * Optional. An authentication config used to authenticate message requests,
   * such that destinations can verify the source. For example, this can be used
   * with private Google Cloud destinations that require Google Cloud
   * credentials for access like Cloud Run. This field is optional and should be
   * set only by users interested in authenticated push.
   *
   * @param GoogleCloudEventarcV1PipelineDestinationAuthenticationConfig $authenticationConfig
   */
  public function setAuthenticationConfig(GoogleCloudEventarcV1PipelineDestinationAuthenticationConfig $authenticationConfig)
  {
    $this->authenticationConfig = $authenticationConfig;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineDestinationAuthenticationConfig
   */
  public function getAuthenticationConfig()
  {
    return $this->authenticationConfig;
  }
  /**
   * Optional. An HTTP endpoint destination described by an URI. If a DNS FQDN
   * is provided as the endpoint, Pipeline will create a peering zone to the
   * consumer VPC and forward DNS requests to the VPC specified by network
   * config to resolve the service endpoint. See:
   * https://cloud.google.com/dns/docs/zones/zones-overview#peering_zones
   *
   * @param GoogleCloudEventarcV1PipelineDestinationHttpEndpoint $httpEndpoint
   */
  public function setHttpEndpoint(GoogleCloudEventarcV1PipelineDestinationHttpEndpoint $httpEndpoint)
  {
    $this->httpEndpoint = $httpEndpoint;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineDestinationHttpEndpoint
   */
  public function getHttpEndpoint()
  {
    return $this->httpEndpoint;
  }
  /**
   * Optional. The resource name of the Message Bus to which events should be
   * published. The Message Bus resource should exist in the same project as the
   * Pipeline. Format:
   * `projects/{project}/locations/{location}/messageBuses/{message_bus}`
   *
   * @param string $messageBus
   */
  public function setMessageBus($messageBus)
  {
    $this->messageBus = $messageBus;
  }
  /**
   * @return string
   */
  public function getMessageBus()
  {
    return $this->messageBus;
  }
  /**
   * Optional. Network config is used to configure how Pipeline resolves and
   * connects to a destination.
   *
   * @param GoogleCloudEventarcV1PipelineDestinationNetworkConfig $networkConfig
   */
  public function setNetworkConfig(GoogleCloudEventarcV1PipelineDestinationNetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineDestinationNetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Optional. The message format before it is delivered to the destination. If
   * not set, the message will be delivered in the format it was originally
   * delivered to the Pipeline. This field can only be set if
   * Pipeline.input_payload_format is also set.
   *
   * @param GoogleCloudEventarcV1PipelineMessagePayloadFormat $outputPayloadFormat
   */
  public function setOutputPayloadFormat(GoogleCloudEventarcV1PipelineMessagePayloadFormat $outputPayloadFormat)
  {
    $this->outputPayloadFormat = $outputPayloadFormat;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineMessagePayloadFormat
   */
  public function getOutputPayloadFormat()
  {
    return $this->outputPayloadFormat;
  }
  /**
   * Optional. The resource name of the Pub/Sub topic to which events should be
   * published. Format: `projects/{project}/locations/{location}/topics/{topic}`
   *
   * @param string $topic
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
  /**
   * Optional. The resource name of the Workflow whose Executions are triggered
   * by the events. The Workflow resource should be deployed in the same project
   * as the Pipeline. Format:
   * `projects/{project}/locations/{location}/workflows/{workflow}`
   *
   * @param string $workflow
   */
  public function setWorkflow($workflow)
  {
    $this->workflow = $workflow;
  }
  /**
   * @return string
   */
  public function getWorkflow()
  {
    return $this->workflow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudEventarcV1PipelineDestination::class, 'Google_Service_Eventarc_GoogleCloudEventarcV1PipelineDestination');
