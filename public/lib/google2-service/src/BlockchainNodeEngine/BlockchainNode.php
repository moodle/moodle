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

class BlockchainNode extends \Google\Model
{
  /**
   * Blockchain type has not been specified, but should be.
   */
  public const BLOCKCHAIN_TYPE_BLOCKCHAIN_TYPE_UNSPECIFIED = 'BLOCKCHAIN_TYPE_UNSPECIFIED';
  /**
   * The blockchain type is Ethereum.
   */
  public const BLOCKCHAIN_TYPE_ETHEREUM = 'ETHEREUM';
  /**
   * The state has not been specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The node has been requested and is in the process of being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The existing node is undergoing deletion, but is not yet finished.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The node is running and ready for use.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The node is in an unexpected or errored state.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The node is currently being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The node is currently being repaired.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  /**
   * The node is currently being reconciled.
   */
  public const STATE_RECONCILING = 'RECONCILING';
  /**
   * The node is syncing, which is the process by which it obtains the latest
   * block and current global state.
   */
  public const STATE_SYNCING = 'SYNCING';
  /**
   * Immutable. The blockchain type of the node.
   *
   * @var string
   */
  public $blockchainType;
  protected $connectionInfoType = ConnectionInfo::class;
  protected $connectionInfoDataType = '';
  /**
   * Output only. The timestamp at which the blockchain node was first created.
   *
   * @var string
   */
  public $createTime;
  protected $ethereumDetailsType = EthereumDetails::class;
  protected $ethereumDetailsDataType = '';
  /**
   * User-provided key-value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The fully qualified name of the blockchain node. e.g.
   * `projects/my-project/locations/us-central1/blockchainNodes/my-node`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. When true, the node is only accessible via Private Service
   * Connect; no public endpoints are exposed. Otherwise, the node is only
   * accessible via public endpoints. Warning: These nodes are deprecated,
   * please use public endpoints instead.
   *
   * @deprecated
   * @var bool
   */
  public $privateServiceConnectEnabled;
  /**
   * Output only. A status representing the state of the node.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The timestamp at which the blockchain node was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Immutable. The blockchain type of the node.
   *
   * Accepted values: BLOCKCHAIN_TYPE_UNSPECIFIED, ETHEREUM
   *
   * @param self::BLOCKCHAIN_TYPE_* $blockchainType
   */
  public function setBlockchainType($blockchainType)
  {
    $this->blockchainType = $blockchainType;
  }
  /**
   * @return self::BLOCKCHAIN_TYPE_*
   */
  public function getBlockchainType()
  {
    return $this->blockchainType;
  }
  /**
   * Output only. The connection information used to interact with a blockchain
   * node.
   *
   * @param ConnectionInfo $connectionInfo
   */
  public function setConnectionInfo(ConnectionInfo $connectionInfo)
  {
    $this->connectionInfo = $connectionInfo;
  }
  /**
   * @return ConnectionInfo
   */
  public function getConnectionInfo()
  {
    return $this->connectionInfo;
  }
  /**
   * Output only. The timestamp at which the blockchain node was first created.
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
   * Ethereum-specific blockchain node details.
   *
   * @param EthereumDetails $ethereumDetails
   */
  public function setEthereumDetails(EthereumDetails $ethereumDetails)
  {
    $this->ethereumDetails = $ethereumDetails;
  }
  /**
   * @return EthereumDetails
   */
  public function getEthereumDetails()
  {
    return $this->ethereumDetails;
  }
  /**
   * User-provided key-value pairs.
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
   * Output only. The fully qualified name of the blockchain node. e.g.
   * `projects/my-project/locations/us-central1/blockchainNodes/my-node`.
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
   * Optional. When true, the node is only accessible via Private Service
   * Connect; no public endpoints are exposed. Otherwise, the node is only
   * accessible via public endpoints. Warning: These nodes are deprecated,
   * please use public endpoints instead.
   *
   * @deprecated
   * @param bool $privateServiceConnectEnabled
   */
  public function setPrivateServiceConnectEnabled($privateServiceConnectEnabled)
  {
    $this->privateServiceConnectEnabled = $privateServiceConnectEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getPrivateServiceConnectEnabled()
  {
    return $this->privateServiceConnectEnabled;
  }
  /**
   * Output only. A status representing the state of the node.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, DELETING, RUNNING, ERROR,
   * UPDATING, REPAIRING, RECONCILING, SYNCING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The timestamp at which the blockchain node was last updated.
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
class_alias(BlockchainNode::class, 'Google_Service_BlockchainNodeEngine_BlockchainNode');
