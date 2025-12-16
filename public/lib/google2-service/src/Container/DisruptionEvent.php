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

namespace Google\Service\Container;

class DisruptionEvent extends \Google\Collection
{
  /**
   * DISRUPTION_TYPE_UNSPECIFIED indicates the disruption type is unspecified.
   */
  public const DISRUPTION_TYPE_DISRUPTION_TYPE_UNSPECIFIED = 'DISRUPTION_TYPE_UNSPECIFIED';
  /**
   * POD_NOT_ENOUGH_PDB indicates there are still running pods on the node
   * during node drain because their evictions are blocked by PDB.
   */
  public const DISRUPTION_TYPE_POD_NOT_ENOUGH_PDB = 'POD_NOT_ENOUGH_PDB';
  /**
   * POD_PDB_VIOLATION indicates that there are force pod evictions during node
   * drain which violate the PDB.
   */
  public const DISRUPTION_TYPE_POD_PDB_VIOLATION = 'POD_PDB_VIOLATION';
  protected $collection_key = 'pdbBlockedPod';
  /**
   * The type of the disruption event.
   *
   * @var string
   */
  public $disruptionType;
  /**
   * The node whose drain is blocked by PDB. This field is set for both
   * POD_PDB_VIOLATION and POD_NOT_ENOUGH_PDB event.
   *
   * @var string
   */
  public $pdbBlockedNode;
  protected $pdbBlockedPodType = PdbBlockedPod::class;
  protected $pdbBlockedPodDataType = 'array';
  /**
   * The timeout in seconds for which the node drain is blocked by PDB. After
   * this timeout, pods are forcefully evicted. This field is only populated
   * when event_type is POD_PDB_VIOLATION.
   *
   * @var string
   */
  public $pdbViolationTimeout;

  /**
   * The type of the disruption event.
   *
   * Accepted values: DISRUPTION_TYPE_UNSPECIFIED, POD_NOT_ENOUGH_PDB,
   * POD_PDB_VIOLATION
   *
   * @param self::DISRUPTION_TYPE_* $disruptionType
   */
  public function setDisruptionType($disruptionType)
  {
    $this->disruptionType = $disruptionType;
  }
  /**
   * @return self::DISRUPTION_TYPE_*
   */
  public function getDisruptionType()
  {
    return $this->disruptionType;
  }
  /**
   * The node whose drain is blocked by PDB. This field is set for both
   * POD_PDB_VIOLATION and POD_NOT_ENOUGH_PDB event.
   *
   * @param string $pdbBlockedNode
   */
  public function setPdbBlockedNode($pdbBlockedNode)
  {
    $this->pdbBlockedNode = $pdbBlockedNode;
  }
  /**
   * @return string
   */
  public function getPdbBlockedNode()
  {
    return $this->pdbBlockedNode;
  }
  /**
   * The pods whose evictions are blocked by PDB. This field is set for both
   * POD_PDB_VIOLATION and POD_NOT_ENOUGH_PDB event.
   *
   * @param PdbBlockedPod[] $pdbBlockedPod
   */
  public function setPdbBlockedPod($pdbBlockedPod)
  {
    $this->pdbBlockedPod = $pdbBlockedPod;
  }
  /**
   * @return PdbBlockedPod[]
   */
  public function getPdbBlockedPod()
  {
    return $this->pdbBlockedPod;
  }
  /**
   * The timeout in seconds for which the node drain is blocked by PDB. After
   * this timeout, pods are forcefully evicted. This field is only populated
   * when event_type is POD_PDB_VIOLATION.
   *
   * @param string $pdbViolationTimeout
   */
  public function setPdbViolationTimeout($pdbViolationTimeout)
  {
    $this->pdbViolationTimeout = $pdbViolationTimeout;
  }
  /**
   * @return string
   */
  public function getPdbViolationTimeout()
  {
    return $this->pdbViolationTimeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DisruptionEvent::class, 'Google_Service_Container_DisruptionEvent');
