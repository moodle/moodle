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

namespace Google\Service\TPU;

class QueuedResource extends \Google\Model
{
  /**
   * Output only. The time when the QueuedResource was created.
   *
   * @var string
   */
  public $createTime;
  protected $guaranteedType = Guaranteed::class;
  protected $guaranteedDataType = '';
  /**
   * Output only. Immutable. The name of the QueuedResource.
   *
   * @var string
   */
  public $name;
  protected $queueingPolicyType = QueueingPolicy::class;
  protected $queueingPolicyDataType = '';
  /**
   * Optional. Name of the reservation in which the resource should be
   * provisioned. Format:
   * projects/{project}/locations/{zone}/reservations/{reservation}
   *
   * @var string
   */
  public $reservationName;
  protected $spotType = Spot::class;
  protected $spotDataType = '';
  protected $stateType = QueuedResourceState::class;
  protected $stateDataType = '';
  protected $tpuType = Tpu::class;
  protected $tpuDataType = '';

  /**
   * Output only. The time when the QueuedResource was created.
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
   * Optional. The Guaranteed tier
   *
   * @param Guaranteed $guaranteed
   */
  public function setGuaranteed(Guaranteed $guaranteed)
  {
    $this->guaranteed = $guaranteed;
  }
  /**
   * @return Guaranteed
   */
  public function getGuaranteed()
  {
    return $this->guaranteed;
  }
  /**
   * Output only. Immutable. The name of the QueuedResource.
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
   * Optional. The queueing policy of the QueuedRequest.
   *
   * @param QueueingPolicy $queueingPolicy
   */
  public function setQueueingPolicy(QueueingPolicy $queueingPolicy)
  {
    $this->queueingPolicy = $queueingPolicy;
  }
  /**
   * @return QueueingPolicy
   */
  public function getQueueingPolicy()
  {
    return $this->queueingPolicy;
  }
  /**
   * Optional. Name of the reservation in which the resource should be
   * provisioned. Format:
   * projects/{project}/locations/{zone}/reservations/{reservation}
   *
   * @param string $reservationName
   */
  public function setReservationName($reservationName)
  {
    $this->reservationName = $reservationName;
  }
  /**
   * @return string
   */
  public function getReservationName()
  {
    return $this->reservationName;
  }
  /**
   * Optional. The Spot tier.
   *
   * @param Spot $spot
   */
  public function setSpot(Spot $spot)
  {
    $this->spot = $spot;
  }
  /**
   * @return Spot
   */
  public function getSpot()
  {
    return $this->spot;
  }
  /**
   * Output only. State of the QueuedResource request.
   *
   * @param QueuedResourceState $state
   */
  public function setState(QueuedResourceState $state)
  {
    $this->state = $state;
  }
  /**
   * @return QueuedResourceState
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Defines a TPU resource.
   *
   * @param Tpu $tpu
   */
  public function setTpu(Tpu $tpu)
  {
    $this->tpu = $tpu;
  }
  /**
   * @return Tpu
   */
  public function getTpu()
  {
    return $this->tpu;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueuedResource::class, 'Google_Service_TPU_QueuedResource');
