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

namespace Google\Service\Compute;

class NetworkProfileProfileType extends \Google\Model
{
  /**
   * RDMA network.
   */
  public const NETWORK_TYPE_RDMA = 'RDMA';
  /**
   * ULL network.
   */
  public const NETWORK_TYPE_ULL = 'ULL';
  /**
   * VPC network.
   */
  public const NETWORK_TYPE_VPC = 'VPC';
  /**
   * RDMA over Falcon.
   */
  public const RDMA_SUBTYPE_FALCON = 'FALCON';
  /**
   * RDMA over Converged Ethernet (RoCE).
   */
  public const RDMA_SUBTYPE_ROCE = 'ROCE';
  /**
   * Exchange operator.
   */
  public const ULL_SUBTYPE_OPERATOR = 'OPERATOR';
  /**
   * Exchange participant.
   */
  public const ULL_SUBTYPE_PARTICIPANT = 'PARTICIPANT';
  /**
   * Regionally bound VPC network.
   */
  public const VPC_SUBTYPE_REGIONAL = 'REGIONAL';
  /**
   * @var string
   */
  public $networkType;
  /**
   * @var string
   */
  public $rdmaSubtype;
  /**
   * @var string
   */
  public $ullSubtype;
  /**
   * @var string
   */
  public $vpcSubtype;

  /**
   * @param self::NETWORK_TYPE_* $networkType
   */
  public function setNetworkType($networkType)
  {
    $this->networkType = $networkType;
  }
  /**
   * @return self::NETWORK_TYPE_*
   */
  public function getNetworkType()
  {
    return $this->networkType;
  }
  /**
   * @param self::RDMA_SUBTYPE_* $rdmaSubtype
   */
  public function setRdmaSubtype($rdmaSubtype)
  {
    $this->rdmaSubtype = $rdmaSubtype;
  }
  /**
   * @return self::RDMA_SUBTYPE_*
   */
  public function getRdmaSubtype()
  {
    return $this->rdmaSubtype;
  }
  /**
   * @param self::ULL_SUBTYPE_* $ullSubtype
   */
  public function setUllSubtype($ullSubtype)
  {
    $this->ullSubtype = $ullSubtype;
  }
  /**
   * @return self::ULL_SUBTYPE_*
   */
  public function getUllSubtype()
  {
    return $this->ullSubtype;
  }
  /**
   * @param self::VPC_SUBTYPE_* $vpcSubtype
   */
  public function setVpcSubtype($vpcSubtype)
  {
    $this->vpcSubtype = $vpcSubtype;
  }
  /**
   * @return self::VPC_SUBTYPE_*
   */
  public function getVpcSubtype()
  {
    return $this->vpcSubtype;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkProfileProfileType::class, 'Google_Service_Compute_NetworkProfileProfileType');
