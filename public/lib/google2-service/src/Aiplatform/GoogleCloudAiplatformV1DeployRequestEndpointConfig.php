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

class GoogleCloudAiplatformV1DeployRequestEndpointConfig extends \Google\Model
{
  /**
   * Optional. By default, if dedicated endpoint is enabled and private service
   * connect config is not set, the endpoint will be exposed through a dedicated
   * DNS [Endpoint.dedicated_endpoint_dns]. If private service connect config is
   * set, the endpoint will be exposed through private service connect. Your
   * request to the dedicated DNS will be isolated from other users' traffic and
   * will have better performance and reliability. Note: Once you enabled
   * dedicated endpoint, you won't be able to send request to the shared DNS
   * {region}-aiplatform.googleapis.com. The limitations will be removed soon.
   * If this field is set to true, the dedicated endpoint will be disabled and
   * the deployed model will be exposed through the shared DNS
   * {region}-aiplatform.googleapis.com.
   *
   * @var bool
   */
  public $dedicatedEndpointDisabled;
  /**
   * Optional. Deprecated. Use dedicated_endpoint_disabled instead. If true, the
   * endpoint will be exposed through a dedicated DNS
   * [Endpoint.dedicated_endpoint_dns]. Your request to the dedicated DNS will
   * be isolated from other users' traffic and will have better performance and
   * reliability. Note: Once you enabled dedicated endpoint, you won't be able
   * to send request to the shared DNS {region}-aiplatform.googleapis.com. The
   * limitations will be removed soon.
   *
   * @deprecated
   * @var bool
   */
  public $dedicatedEndpointEnabled;
  /**
   * Optional. The user-specified display name of the endpoint. If not set, a
   * default name will be used.
   *
   * @var string
   */
  public $endpointDisplayName;
  /**
   * Optional. Immutable. The ID to use for endpoint, which will become the
   * final component of the endpoint resource name. If not provided, Vertex AI
   * will generate a value for this ID. If the first character is a letter, this
   * value may be up to 63 characters, and valid characters are `[a-z0-9-]`. The
   * last character must be a letter or number. If the first character is a
   * number, this value may be up to 9 characters, and valid characters are
   * `[0-9]` with no leading zeros. When using HTTP/JSON, this field is
   * populated based on a query string argument, such as `?endpoint_id=12345`.
   * This is the fallback for fields that are not included in either the URI or
   * the body.
   *
   * @var string
   */
  public $endpointUserId;
  /**
   * Optional. The labels with user-defined metadata to organize your Endpoints.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  protected $privateServiceConnectConfigType = GoogleCloudAiplatformV1PrivateServiceConnectConfig::class;
  protected $privateServiceConnectConfigDataType = '';

  /**
   * Optional. By default, if dedicated endpoint is enabled and private service
   * connect config is not set, the endpoint will be exposed through a dedicated
   * DNS [Endpoint.dedicated_endpoint_dns]. If private service connect config is
   * set, the endpoint will be exposed through private service connect. Your
   * request to the dedicated DNS will be isolated from other users' traffic and
   * will have better performance and reliability. Note: Once you enabled
   * dedicated endpoint, you won't be able to send request to the shared DNS
   * {region}-aiplatform.googleapis.com. The limitations will be removed soon.
   * If this field is set to true, the dedicated endpoint will be disabled and
   * the deployed model will be exposed through the shared DNS
   * {region}-aiplatform.googleapis.com.
   *
   * @param bool $dedicatedEndpointDisabled
   */
  public function setDedicatedEndpointDisabled($dedicatedEndpointDisabled)
  {
    $this->dedicatedEndpointDisabled = $dedicatedEndpointDisabled;
  }
  /**
   * @return bool
   */
  public function getDedicatedEndpointDisabled()
  {
    return $this->dedicatedEndpointDisabled;
  }
  /**
   * Optional. Deprecated. Use dedicated_endpoint_disabled instead. If true, the
   * endpoint will be exposed through a dedicated DNS
   * [Endpoint.dedicated_endpoint_dns]. Your request to the dedicated DNS will
   * be isolated from other users' traffic and will have better performance and
   * reliability. Note: Once you enabled dedicated endpoint, you won't be able
   * to send request to the shared DNS {region}-aiplatform.googleapis.com. The
   * limitations will be removed soon.
   *
   * @deprecated
   * @param bool $dedicatedEndpointEnabled
   */
  public function setDedicatedEndpointEnabled($dedicatedEndpointEnabled)
  {
    $this->dedicatedEndpointEnabled = $dedicatedEndpointEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getDedicatedEndpointEnabled()
  {
    return $this->dedicatedEndpointEnabled;
  }
  /**
   * Optional. The user-specified display name of the endpoint. If not set, a
   * default name will be used.
   *
   * @param string $endpointDisplayName
   */
  public function setEndpointDisplayName($endpointDisplayName)
  {
    $this->endpointDisplayName = $endpointDisplayName;
  }
  /**
   * @return string
   */
  public function getEndpointDisplayName()
  {
    return $this->endpointDisplayName;
  }
  /**
   * Optional. Immutable. The ID to use for endpoint, which will become the
   * final component of the endpoint resource name. If not provided, Vertex AI
   * will generate a value for this ID. If the first character is a letter, this
   * value may be up to 63 characters, and valid characters are `[a-z0-9-]`. The
   * last character must be a letter or number. If the first character is a
   * number, this value may be up to 9 characters, and valid characters are
   * `[0-9]` with no leading zeros. When using HTTP/JSON, this field is
   * populated based on a query string argument, such as `?endpoint_id=12345`.
   * This is the fallback for fields that are not included in either the URI or
   * the body.
   *
   * @param string $endpointUserId
   */
  public function setEndpointUserId($endpointUserId)
  {
    $this->endpointUserId = $endpointUserId;
  }
  /**
   * @return string
   */
  public function getEndpointUserId()
  {
    return $this->endpointUserId;
  }
  /**
   * Optional. The labels with user-defined metadata to organize your Endpoints.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information and examples of labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. Configuration for private service connect. If set, the endpoint
   * will be exposed through private service connect.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployRequestEndpointConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployRequestEndpointConfig');
