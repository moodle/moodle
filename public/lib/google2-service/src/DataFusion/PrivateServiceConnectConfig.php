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

namespace Google\Service\DataFusion;

class PrivateServiceConnectConfig extends \Google\Model
{
  /**
   * Output only. The CIDR block to which the CDF instance can't route traffic
   * to in the consumer project VPC. The size of this block is /25. The format
   * of this field is governed by RFC 4632. Example: 240.0.0.0/25
   *
   * @var string
   */
  public $effectiveUnreachableCidrBlock;
  /**
   * Required. The reference to the network attachment used to establish private
   * connectivity. It will be of the form projects/{project-
   * id}/regions/{region}/networkAttachments/{network-attachment-id}.
   *
   * @var string
   */
  public $networkAttachment;
  /**
   * Optional. Input only. The CIDR block to which the CDF instance can't route
   * traffic to in the consumer project VPC. The size of this block should be at
   * least /25. This range should not overlap with the primary address range of
   * any subnetwork used by the network attachment. This range can be used for
   * other purposes in the consumer VPC as long as there is no requirement for
   * CDF to reach destinations using these addresses. If this value is not
   * provided, the server chooses a non RFC 1918 address range. The format of
   * this field is governed by RFC 4632. Example: 192.168.0.0/25
   *
   * @var string
   */
  public $unreachableCidrBlock;

  /**
   * Output only. The CIDR block to which the CDF instance can't route traffic
   * to in the consumer project VPC. The size of this block is /25. The format
   * of this field is governed by RFC 4632. Example: 240.0.0.0/25
   *
   * @param string $effectiveUnreachableCidrBlock
   */
  public function setEffectiveUnreachableCidrBlock($effectiveUnreachableCidrBlock)
  {
    $this->effectiveUnreachableCidrBlock = $effectiveUnreachableCidrBlock;
  }
  /**
   * @return string
   */
  public function getEffectiveUnreachableCidrBlock()
  {
    return $this->effectiveUnreachableCidrBlock;
  }
  /**
   * Required. The reference to the network attachment used to establish private
   * connectivity. It will be of the form projects/{project-
   * id}/regions/{region}/networkAttachments/{network-attachment-id}.
   *
   * @param string $networkAttachment
   */
  public function setNetworkAttachment($networkAttachment)
  {
    $this->networkAttachment = $networkAttachment;
  }
  /**
   * @return string
   */
  public function getNetworkAttachment()
  {
    return $this->networkAttachment;
  }
  /**
   * Optional. Input only. The CIDR block to which the CDF instance can't route
   * traffic to in the consumer project VPC. The size of this block should be at
   * least /25. This range should not overlap with the primary address range of
   * any subnetwork used by the network attachment. This range can be used for
   * other purposes in the consumer VPC as long as there is no requirement for
   * CDF to reach destinations using these addresses. If this value is not
   * provided, the server chooses a non RFC 1918 address range. The format of
   * this field is governed by RFC 4632. Example: 192.168.0.0/25
   *
   * @param string $unreachableCidrBlock
   */
  public function setUnreachableCidrBlock($unreachableCidrBlock)
  {
    $this->unreachableCidrBlock = $unreachableCidrBlock;
  }
  /**
   * @return string
   */
  public function getUnreachableCidrBlock()
  {
    return $this->unreachableCidrBlock;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateServiceConnectConfig::class, 'Google_Service_DataFusion_PrivateServiceConnectConfig');
