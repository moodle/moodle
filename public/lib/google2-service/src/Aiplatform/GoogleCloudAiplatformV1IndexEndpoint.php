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

class GoogleCloudAiplatformV1IndexEndpoint extends \Google\Collection
{
  protected $collection_key = 'deployedIndexes';
  /**
   * Output only. Timestamp when this IndexEndpoint was created.
   *
   * @var string
   */
  public $createTime;
  protected $deployedIndexesType = GoogleCloudAiplatformV1DeployedIndex::class;
  protected $deployedIndexesDataType = 'array';
  /**
   * The description of the IndexEndpoint.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the IndexEndpoint. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Deprecated: If true, expose the IndexEndpoint via private service
   * connect. Only one of the fields, network or enable_private_service_connect,
   * can be set.
   *
   * @deprecated
   * @var bool
   */
  public $enablePrivateServiceConnect;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * The labels with user-defined metadata to organize your IndexEndpoints.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the IndexEndpoint.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The full name of the Google Compute Engine
   * [network](https://cloud.google.com/compute/docs/networks-and-
   * firewalls#networks) to which the IndexEndpoint should be peered. Private
   * services access must already be configured for the network. If left
   * unspecified, the Endpoint is not peered with any network. network and
   * private_service_connect_config are mutually exclusive. [Format](https://clo
   * ud.google.com/compute/docs/reference/rest/v1/networks/insert):
   * `projects/{project}/global/networks/{network}`. Where {project} is a
   * project number, as in '12345', and {network} is network name.
   *
   * @var string
   */
  public $network;
  protected $privateServiceConnectConfigType = GoogleCloudAiplatformV1PrivateServiceConnectConfig::class;
  protected $privateServiceConnectConfigDataType = '';
  /**
   * Output only. If public_endpoint_enabled is true, this field will be
   * populated with the domain name to use for this index endpoint.
   *
   * @var string
   */
  public $publicEndpointDomainName;
  /**
   * Optional. If true, the deployed index will be accessible through public
   * endpoint.
   *
   * @var bool
   */
  public $publicEndpointEnabled;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Timestamp when this IndexEndpoint was last updated. This
   * timestamp is not updated when the endpoint's DeployedIndexes are updated,
   * e.g. due to updates of the original Indexes they are the deployments of.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this IndexEndpoint was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The indexes deployed in this endpoint.
   *
   * @param GoogleCloudAiplatformV1DeployedIndex[] $deployedIndexes
   */
  public function setDeployedIndexes($deployedIndexes)
  {
    $this->deployedIndexes = $deployedIndexes;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedIndex[]
   */
  public function getDeployedIndexes()
  {
    return $this->deployedIndexes;
  }
  /**
   * The description of the IndexEndpoint.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the IndexEndpoint. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Deprecated: If true, expose the IndexEndpoint via private service
   * connect. Only one of the fields, network or enable_private_service_connect,
   * can be set.
   *
   * @deprecated
   * @param bool $enablePrivateServiceConnect
   */
  public function setEnablePrivateServiceConnect($enablePrivateServiceConnect)
  {
    $this->enablePrivateServiceConnect = $enablePrivateServiceConnect;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnablePrivateServiceConnect()
  {
    return $this->enablePrivateServiceConnect;
  }
  /**
   * Immutable. Customer-managed encryption key spec for an IndexEndpoint. If
   * set, this IndexEndpoint and all sub-resources of this IndexEndpoint will be
   * secured by this key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The labels with user-defined metadata to organize your IndexEndpoints.
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
   * Output only. The resource name of the IndexEndpoint.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The full name of the Google Compute Engine
   * [network](https://cloud.google.com/compute/docs/networks-and-
   * firewalls#networks) to which the IndexEndpoint should be peered. Private
   * services access must already be configured for the network. If left
   * unspecified, the Endpoint is not peered with any network. network and
   * private_service_connect_config are mutually exclusive. [Format](https://clo
   * ud.google.com/compute/docs/reference/rest/v1/networks/insert):
   * `projects/{project}/global/networks/{network}`. Where {project} is a
   * project number, as in '12345', and {network} is network name.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. Configuration for private service connect. network and
   * private_service_connect_config are mutually exclusive.
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
   * Output only. If public_endpoint_enabled is true, this field will be
   * populated with the domain name to use for this index endpoint.
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
   * Optional. If true, the deployed index will be accessible through public
   * endpoint.
   *
   * @param bool $publicEndpointEnabled
   */
  public function setPublicEndpointEnabled($publicEndpointEnabled)
  {
    $this->publicEndpointEnabled = $publicEndpointEnabled;
  }
  /**
   * @return bool
   */
  public function getPublicEndpointEnabled()
  {
    return $this->publicEndpointEnabled;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Timestamp when this IndexEndpoint was last updated. This
   * timestamp is not updated when the endpoint's DeployedIndexes are updated,
   * e.g. due to updates of the original Indexes they are the deployments of.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1IndexEndpoint::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1IndexEndpoint');
