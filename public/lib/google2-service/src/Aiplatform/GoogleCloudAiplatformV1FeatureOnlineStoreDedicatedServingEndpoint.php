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

class GoogleCloudAiplatformV1FeatureOnlineStoreDedicatedServingEndpoint extends \Google\Model
{
  protected $privateServiceConnectConfigType = GoogleCloudAiplatformV1PrivateServiceConnectConfig::class;
  protected $privateServiceConnectConfigDataType = '';
  /**
   * Output only. This field will be populated with the domain name to use for
   * this FeatureOnlineStore
   *
   * @var string
   */
  public $publicEndpointDomainName;
  /**
   * Output only. The name of the service attachment resource. Populated if
   * private service connect is enabled and after FeatureViewSync is created.
   *
   * @var string
   */
  public $serviceAttachment;

  /**
   * Optional. Private service connect config. The private service connection is
   * available only for Optimized storage type, not for embedding management
   * now. If PrivateServiceConnectConfig.enable_private_service_connect set to
   * true, customers will use private service connection to send request.
   * Otherwise, the connection will set to public endpoint.
   *
   * @param GoogleCloudAiplatformV1PrivateServiceConnectConfig $privateServiceConnectConfig
   */
  public function setPrivateServiceConnectConfig(GoogleCloudAiplatformV1PrivateServiceConnectConfig $privateServiceConnectConfig)
  {
    $this->privateServiceConnectConfig = $privateServiceConnectConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PrivateServiceConnectConfig
   */
  public function getPrivateServiceConnectConfig()
  {
    return $this->privateServiceConnectConfig;
  }
  /**
   * Output only. This field will be populated with the domain name to use for
   * this FeatureOnlineStore
   *
   * @param string $publicEndpointDomainName
   */
  public function setPublicEndpointDomainName($publicEndpointDomainName)
  {
    $this->publicEndpointDomainName = $publicEndpointDomainName;
  }
  /**
   * @return string
   */
  public function getPublicEndpointDomainName()
  {
    return $this->publicEndpointDomainName;
  }
  /**
   * Output only. The name of the service attachment resource. Populated if
   * private service connect is enabled and after FeatureViewSync is created.
   *
   * @param string $serviceAttachment
   */
  public function setServiceAttachment($serviceAttachment)
  {
    $this->serviceAttachment = $serviceAttachment;
  }
  /**
   * @return string
   */
  public function getServiceAttachment()
  {
    return $this->serviceAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureOnlineStoreDedicatedServingEndpoint::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureOnlineStoreDedicatedServingEndpoint');
