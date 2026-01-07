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

namespace Google\Service\Baremetalsolution;

class VlanAttachment extends \Google\Model
{
  /**
   * Immutable. The identifier of the attachment within vrf.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. The name of the vlan attachment within vrf. This is of the form p
   * rojects/{project_number}/regions/{region}/interconnectAttachments/{intercon
   * nect_attachment}
   *
   * @var string
   */
  public $interconnectAttachment;
  /**
   * Input only. Pairing key.
   *
   * @var string
   */
  public $pairingKey;
  /**
   * The peer IP of the attachment.
   *
   * @var string
   */
  public $peerIp;
  /**
   * The peer vlan ID of the attachment.
   *
   * @var string
   */
  public $peerVlanId;
  protected $qosPolicyType = QosPolicy::class;
  protected $qosPolicyDataType = '';
  /**
   * The router IP of the attachment.
   *
   * @var string
   */
  public $routerIp;

  /**
   * Immutable. The identifier of the attachment within vrf.
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
   * Optional. The name of the vlan attachment within vrf. This is of the form p
   * rojects/{project_number}/regions/{region}/interconnectAttachments/{intercon
   * nect_attachment}
   *
   * @param string $interconnectAttachment
   */
  public function setInterconnectAttachment($interconnectAttachment)
  {
    $this->interconnectAttachment = $interconnectAttachment;
  }
  /**
   * @return string
   */
  public function getInterconnectAttachment()
  {
    return $this->interconnectAttachment;
  }
  /**
   * Input only. Pairing key.
   *
   * @param string $pairingKey
   */
  public function setPairingKey($pairingKey)
  {
    $this->pairingKey = $pairingKey;
  }
  /**
   * @return string
   */
  public function getPairingKey()
  {
    return $this->pairingKey;
  }
  /**
   * The peer IP of the attachment.
   *
   * @param string $peerIp
   */
  public function setPeerIp($peerIp)
  {
    $this->peerIp = $peerIp;
  }
  /**
   * @return string
   */
  public function getPeerIp()
  {
    return $this->peerIp;
  }
  /**
   * The peer vlan ID of the attachment.
   *
   * @param string $peerVlanId
   */
  public function setPeerVlanId($peerVlanId)
  {
    $this->peerVlanId = $peerVlanId;
  }
  /**
   * @return string
   */
  public function getPeerVlanId()
  {
    return $this->peerVlanId;
  }
  /**
   * The QOS policy applied to this VLAN attachment. This value should be
   * preferred to using qos at vrf level.
   *
   * @param QosPolicy $qosPolicy
   */
  public function setQosPolicy(QosPolicy $qosPolicy)
  {
    $this->qosPolicy = $qosPolicy;
  }
  /**
   * @return QosPolicy
   */
  public function getQosPolicy()
  {
    return $this->qosPolicy;
  }
  /**
   * The router IP of the attachment.
   *
   * @param string $routerIp
   */
  public function setRouterIp($routerIp)
  {
    $this->routerIp = $routerIp;
  }
  /**
   * @return string
   */
  public function getRouterIp()
  {
    return $this->routerIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VlanAttachment::class, 'Google_Service_Baremetalsolution_VlanAttachment');
