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

namespace Google\Service\TrafficDirectorService;

class Node extends \Google\Collection
{
  protected $collection_key = 'listeningAddresses';
  /**
   * Client feature support list. These are well known features described in the
   * Envoy API repository for a given major version of an API. Client features
   * use reverse DNS naming scheme, for example ``com.acme.feature``. See
   * :ref:`the list of features ` that xDS client may support.
   *
   * @var string[]
   */
  public $clientFeatures;
  /**
   * Defines the local service cluster name where Envoy is running. Though
   * optional, it should be set if any of the following features are used:
   * :ref:`statsd `, :ref:`health check cluster verification `, :ref:`runtime
   * override directory `, :ref:`user agent addition `, :ref:`HTTP global rate
   * limiting `, :ref:`CDS `, and :ref:`HTTP tracing `, either in this message
   * or via :option:`--service-cluster`.
   *
   * @var string
   */
  public $cluster;
  protected $dynamicParametersType = ContextParams::class;
  protected $dynamicParametersDataType = 'map';
  protected $extensionsType = Extension::class;
  protected $extensionsDataType = 'array';
  /**
   * An opaque node identifier for the Envoy node. This also provides the local
   * service node name. It should be set if any of the following features are
   * used: :ref:`statsd `, :ref:`CDS `, and :ref:`HTTP tracing `, either in this
   * message or via :option:`--service-node`.
   *
   * @var string
   */
  public $id;
  protected $listeningAddressesType = Address::class;
  protected $listeningAddressesDataType = 'array';
  protected $localityType = Locality::class;
  protected $localityDataType = '';
  /**
   * Opaque metadata extending the node identifier. Envoy will pass this
   * directly to the management server.
   *
   * @var array[]
   */
  public $metadata;
  protected $userAgentBuildVersionType = BuildVersion::class;
  protected $userAgentBuildVersionDataType = '';
  /**
   * Free-form string that identifies the entity requesting config. E.g. "envoy"
   * or "grpc"
   *
   * @var string
   */
  public $userAgentName;
  /**
   * Free-form string that identifies the version of the entity requesting
   * config. E.g. "1.12.2" or "abcd1234", or "SpecialEnvoyBuild"
   *
   * @var string
   */
  public $userAgentVersion;

  /**
   * Client feature support list. These are well known features described in the
   * Envoy API repository for a given major version of an API. Client features
   * use reverse DNS naming scheme, for example ``com.acme.feature``. See
   * :ref:`the list of features ` that xDS client may support.
   *
   * @param string[] $clientFeatures
   */
  public function setClientFeatures($clientFeatures)
  {
    $this->clientFeatures = $clientFeatures;
  }
  /**
   * @return string[]
   */
  public function getClientFeatures()
  {
    return $this->clientFeatures;
  }
  /**
   * Defines the local service cluster name where Envoy is running. Though
   * optional, it should be set if any of the following features are used:
   * :ref:`statsd `, :ref:`health check cluster verification `, :ref:`runtime
   * override directory `, :ref:`user agent addition `, :ref:`HTTP global rate
   * limiting `, :ref:`CDS `, and :ref:`HTTP tracing `, either in this message
   * or via :option:`--service-cluster`.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Map from xDS resource type URL to dynamic context parameters. These may
   * vary at runtime (unlike other fields in this message). For example, the xDS
   * client may have a shard identifier that changes during the lifetime of the
   * xDS client. In Envoy, this would be achieved by updating the dynamic
   * context on the Server::Instance's LocalInfo context provider. The shard ID
   * dynamic parameter then appears in this field during future discovery
   * requests.
   *
   * @param ContextParams[] $dynamicParameters
   */
  public function setDynamicParameters($dynamicParameters)
  {
    $this->dynamicParameters = $dynamicParameters;
  }
  /**
   * @return ContextParams[]
   */
  public function getDynamicParameters()
  {
    return $this->dynamicParameters;
  }
  /**
   * List of extensions and their versions supported by the node.
   *
   * @param Extension[] $extensions
   */
  public function setExtensions($extensions)
  {
    $this->extensions = $extensions;
  }
  /**
   * @return Extension[]
   */
  public function getExtensions()
  {
    return $this->extensions;
  }
  /**
   * An opaque node identifier for the Envoy node. This also provides the local
   * service node name. It should be set if any of the following features are
   * used: :ref:`statsd `, :ref:`CDS `, and :ref:`HTTP tracing `, either in this
   * message or via :option:`--service-node`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Known listening ports on the node as a generic hint to the management
   * server for filtering :ref:`listeners ` to be returned. For example, if
   * there is a listener bound to port 80, the list can optionally contain the
   * SocketAddress ``(0.0.0.0,80)``. The field is optional and just a hint.
   *
   * @deprecated
   * @param Address[] $listeningAddresses
   */
  public function setListeningAddresses($listeningAddresses)
  {
    $this->listeningAddresses = $listeningAddresses;
  }
  /**
   * @deprecated
   * @return Address[]
   */
  public function getListeningAddresses()
  {
    return $this->listeningAddresses;
  }
  /**
   * Locality specifying where the Envoy instance is running.
   *
   * @param Locality $locality
   */
  public function setLocality(Locality $locality)
  {
    $this->locality = $locality;
  }
  /**
   * @return Locality
   */
  public function getLocality()
  {
    return $this->locality;
  }
  /**
   * Opaque metadata extending the node identifier. Envoy will pass this
   * directly to the management server.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Structured version of the entity requesting config.
   *
   * @param BuildVersion $userAgentBuildVersion
   */
  public function setUserAgentBuildVersion(BuildVersion $userAgentBuildVersion)
  {
    $this->userAgentBuildVersion = $userAgentBuildVersion;
  }
  /**
   * @return BuildVersion
   */
  public function getUserAgentBuildVersion()
  {
    return $this->userAgentBuildVersion;
  }
  /**
   * Free-form string that identifies the entity requesting config. E.g. "envoy"
   * or "grpc"
   *
   * @param string $userAgentName
   */
  public function setUserAgentName($userAgentName)
  {
    $this->userAgentName = $userAgentName;
  }
  /**
   * @return string
   */
  public function getUserAgentName()
  {
    return $this->userAgentName;
  }
  /**
   * Free-form string that identifies the version of the entity requesting
   * config. E.g. "1.12.2" or "abcd1234", or "SpecialEnvoyBuild"
   *
   * @param string $userAgentVersion
   */
  public function setUserAgentVersion($userAgentVersion)
  {
    $this->userAgentVersion = $userAgentVersion;
  }
  /**
   * @return string
   */
  public function getUserAgentVersion()
  {
    return $this->userAgentVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Node::class, 'Google_Service_TrafficDirectorService_Node');
