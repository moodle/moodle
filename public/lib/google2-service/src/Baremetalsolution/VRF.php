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

class VRF extends \Google\Collection
{
  /**
   * The unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The vrf is provisioning.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The vrf is provisioned.
   */
  public const STATE_PROVISIONED = 'PROVISIONED';
  protected $collection_key = 'vlanAttachments';
  /**
   * The name of the VRF.
   *
   * @var string
   */
  public $name;
  protected $qosPolicyType = QosPolicy::class;
  protected $qosPolicyDataType = '';
  /**
   * The possible state of VRF.
   *
   * @var string
   */
  public $state;
  protected $vlanAttachmentsType = VlanAttachment::class;
  protected $vlanAttachmentsDataType = 'array';

  /**
   * The name of the VRF.
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
   * The QOS policy applied to this VRF. The value is only meaningful when all
   * the vlan attachments have the same QoS. This field should not be used for
   * new integrations, use vlan attachment level qos instead. The field is left
   * for backward-compatibility.
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
   * The possible state of VRF.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, PROVISIONED
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
   * The list of VLAN attachments for the VRF.
   *
   * @param VlanAttachment[] $vlanAttachments
   */
  public function setVlanAttachments($vlanAttachments)
  {
    $this->vlanAttachments = $vlanAttachments;
  }
  /**
   * @return VlanAttachment[]
   */
  public function getVlanAttachments()
  {
    return $this->vlanAttachments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VRF::class, 'Google_Service_Baremetalsolution_VRF');
