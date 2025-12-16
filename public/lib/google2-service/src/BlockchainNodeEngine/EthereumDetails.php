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

namespace Google\Service\BlockchainNodeEngine;

class EthereumDetails extends \Google\Model
{
  /**
   * Consensus client has not been specified, but should be.
   */
  public const CONSENSUS_CLIENT_CONSENSUS_CLIENT_UNSPECIFIED = 'CONSENSUS_CLIENT_UNSPECIFIED';
  /**
   * Consensus client implementation written in Rust, maintained by Sigma Prime.
   * See [Lighthouse - Sigma Prime](https://lighthouse.sigmaprime.io/) for
   * details.
   */
  public const CONSENSUS_CLIENT_LIGHTHOUSE = 'LIGHTHOUSE';
  /**
   * Erigon's embedded consensus client embedded in the execution client. Note
   * this option is not currently available when creating new blockchain nodes.
   * See [Erigon on GitHub](https://github.com/ledgerwatch/erigon#embedded-
   * consensus-layer) for details.
   *
   * @deprecated
   */
  public const CONSENSUS_CLIENT_ERIGON_EMBEDDED_CONSENSUS_LAYER = 'ERIGON_EMBEDDED_CONSENSUS_LAYER';
  /**
   * Execution client has not been specified, but should be.
   */
  public const EXECUTION_CLIENT_EXECUTION_CLIENT_UNSPECIFIED = 'EXECUTION_CLIENT_UNSPECIFIED';
  /**
   * Official Go implementation of the Ethereum protocol. See [go-
   * ethereum](https://geth.ethereum.org/) for details.
   */
  public const EXECUTION_CLIENT_GETH = 'GETH';
  /**
   * An implementation of Ethereum (execution client), on the efficiency
   * frontier, written in Go. See [Erigon on
   * GitHub](https://github.com/ledgerwatch/erigon) for details.
   */
  public const EXECUTION_CLIENT_ERIGON = 'ERIGON';
  /**
   * The network has not been specified, but should be.
   */
  public const NETWORK_NETWORK_UNSPECIFIED = 'NETWORK_UNSPECIFIED';
  /**
   * The Ethereum Mainnet.
   */
  public const NETWORK_MAINNET = 'MAINNET';
  /**
   * Deprecated: The Ethereum Testnet based on Goerli protocol. Please use
   * another test network.
   *
   * @deprecated
   */
  public const NETWORK_TESTNET_GOERLI_PRATER = 'TESTNET_GOERLI_PRATER';
  /**
   * The Ethereum Testnet based on Sepolia/Bepolia protocol. See
   * https://github.com/eth-clients/sepolia.
   */
  public const NETWORK_TESTNET_SEPOLIA = 'TESTNET_SEPOLIA';
  /**
   * The Ethereum Testnet based on Holesky specification. See
   * https://github.com/eth-clients/holesky.
   */
  public const NETWORK_TESTNET_HOLESKY = 'TESTNET_HOLESKY';
  /**
   * Node type has not been specified, but should be.
   */
  public const NODE_TYPE_NODE_TYPE_UNSPECIFIED = 'NODE_TYPE_UNSPECIFIED';
  /**
   * An Ethereum node that only downloads Ethereum block headers.
   */
  public const NODE_TYPE_LIGHT = 'LIGHT';
  /**
   * Keeps a complete copy of the blockchain data, and contributes to the
   * network by receiving, validating, and forwarding transactions.
   */
  public const NODE_TYPE_FULL = 'FULL';
  /**
   * Holds the same data as full node as well as all of the blockchain's history
   * state data dating back to the Genesis Block.
   */
  public const NODE_TYPE_ARCHIVE = 'ARCHIVE';
  protected $additionalEndpointsType = EthereumEndpoints::class;
  protected $additionalEndpointsDataType = '';
  /**
   * Immutable. Enables JSON-RPC access to functions in the `admin` namespace.
   * Defaults to `false`.
   *
   * @var bool
   */
  public $apiEnableAdmin;
  /**
   * Immutable. Enables JSON-RPC access to functions in the `debug` namespace.
   * Defaults to `false`.
   *
   * @var bool
   */
  public $apiEnableDebug;
  /**
   * Immutable. The consensus client.
   *
   * @var string
   */
  public $consensusClient;
  /**
   * Immutable. The execution client
   *
   * @var string
   */
  public $executionClient;
  protected $gethDetailsType = GethDetails::class;
  protected $gethDetailsDataType = '';
  /**
   * Immutable. The Ethereum environment being accessed.
   *
   * @var string
   */
  public $network;
  /**
   * Immutable. The type of Ethereum node.
   *
   * @var string
   */
  public $nodeType;
  protected $validatorConfigType = ValidatorConfig::class;
  protected $validatorConfigDataType = '';

  /**
   * Output only. Ethereum-specific endpoint information.
   *
   * @param EthereumEndpoints $additionalEndpoints
   */
  public function setAdditionalEndpoints(EthereumEndpoints $additionalEndpoints)
  {
    $this->additionalEndpoints = $additionalEndpoints;
  }
  /**
   * @return EthereumEndpoints
   */
  public function getAdditionalEndpoints()
  {
    return $this->additionalEndpoints;
  }
  /**
   * Immutable. Enables JSON-RPC access to functions in the `admin` namespace.
   * Defaults to `false`.
   *
   * @param bool $apiEnableAdmin
   */
  public function setApiEnableAdmin($apiEnableAdmin)
  {
    $this->apiEnableAdmin = $apiEnableAdmin;
  }
  /**
   * @return bool
   */
  public function getApiEnableAdmin()
  {
    return $this->apiEnableAdmin;
  }
  /**
   * Immutable. Enables JSON-RPC access to functions in the `debug` namespace.
   * Defaults to `false`.
   *
   * @param bool $apiEnableDebug
   */
  public function setApiEnableDebug($apiEnableDebug)
  {
    $this->apiEnableDebug = $apiEnableDebug;
  }
  /**
   * @return bool
   */
  public function getApiEnableDebug()
  {
    return $this->apiEnableDebug;
  }
  /**
   * Immutable. The consensus client.
   *
   * Accepted values: CONSENSUS_CLIENT_UNSPECIFIED, LIGHTHOUSE,
   * ERIGON_EMBEDDED_CONSENSUS_LAYER
   *
   * @param self::CONSENSUS_CLIENT_* $consensusClient
   */
  public function setConsensusClient($consensusClient)
  {
    $this->consensusClient = $consensusClient;
  }
  /**
   * @return self::CONSENSUS_CLIENT_*
   */
  public function getConsensusClient()
  {
    return $this->consensusClient;
  }
  /**
   * Immutable. The execution client
   *
   * Accepted values: EXECUTION_CLIENT_UNSPECIFIED, GETH, ERIGON
   *
   * @param self::EXECUTION_CLIENT_* $executionClient
   */
  public function setExecutionClient($executionClient)
  {
    $this->executionClient = $executionClient;
  }
  /**
   * @return self::EXECUTION_CLIENT_*
   */
  public function getExecutionClient()
  {
    return $this->executionClient;
  }
  /**
   * Details for the Geth execution client.
   *
   * @param GethDetails $gethDetails
   */
  public function setGethDetails(GethDetails $gethDetails)
  {
    $this->gethDetails = $gethDetails;
  }
  /**
   * @return GethDetails
   */
  public function getGethDetails()
  {
    return $this->gethDetails;
  }
  /**
   * Immutable. The Ethereum environment being accessed.
   *
   * Accepted values: NETWORK_UNSPECIFIED, MAINNET, TESTNET_GOERLI_PRATER,
   * TESTNET_SEPOLIA, TESTNET_HOLESKY
   *
   * @param self::NETWORK_* $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return self::NETWORK_*
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Immutable. The type of Ethereum node.
   *
   * Accepted values: NODE_TYPE_UNSPECIFIED, LIGHT, FULL, ARCHIVE
   *
   * @param self::NODE_TYPE_* $nodeType
   */
  public function setNodeType($nodeType)
  {
    $this->nodeType = $nodeType;
  }
  /**
   * @return self::NODE_TYPE_*
   */
  public function getNodeType()
  {
    return $this->nodeType;
  }
  /**
   * Configuration for validator-related parameters on the beacon client, and
   * for any GCP-managed validator client.
   *
   * @param ValidatorConfig $validatorConfig
   */
  public function setValidatorConfig(ValidatorConfig $validatorConfig)
  {
    $this->validatorConfig = $validatorConfig;
  }
  /**
   * @return ValidatorConfig
   */
  public function getValidatorConfig()
  {
    return $this->validatorConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EthereumDetails::class, 'Google_Service_BlockchainNodeEngine_EthereumDetails');
