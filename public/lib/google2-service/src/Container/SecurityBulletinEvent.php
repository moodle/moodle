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

class SecurityBulletinEvent extends \Google\Collection
{
  protected $collection_key = 'patchedVersions';
  /**
   * The GKE minor versions affected by this vulnerability.
   *
   * @var string[]
   */
  public $affectedSupportedMinors;
  /**
   * A brief description of the bulletin. See the bulletin pointed to by the
   * bulletin_uri field for an expanded description.
   *
   * @var string
   */
  public $briefDescription;
  /**
   * The ID of the bulletin corresponding to the vulnerability.
   *
   * @var string
   */
  public $bulletinId;
  /**
   * The URI link to the bulletin on the website for more information.
   *
   * @var string
   */
  public $bulletinUri;
  /**
   * The CVEs associated with this bulletin.
   *
   * @var string[]
   */
  public $cveIds;
  /**
   * If this field is specified, it means there are manual steps that the user
   * must take to make their clusters safe.
   *
   * @var bool
   */
  public $manualStepsRequired;
  /**
   * The GKE versions where this vulnerability is mitigated.
   *
   * @var string[]
   */
  public $mitigatedVersions;
  /**
   * The GKE versions where this vulnerability is patched.
   *
   * @var string[]
   */
  public $patchedVersions;
  /**
   * The resource type (node/control plane) that has the vulnerability. Multiple
   * notifications (1 notification per resource type) will be sent for a
   * vulnerability that affects > 1 resource type.
   *
   * @var string
   */
  public $resourceTypeAffected;
  /**
   * The severity of this bulletin as it relates to GKE.
   *
   * @var string
   */
  public $severity;
  /**
   * This represents a version selected from the patched_versions field that the
   * cluster receiving this notification should most likely want to upgrade to
   * based on its current version. Note that if this notification is being
   * received by a given cluster, it means that this version is currently
   * available as an upgrade target in that cluster's location.
   *
   * @var string
   */
  public $suggestedUpgradeTarget;

  /**
   * The GKE minor versions affected by this vulnerability.
   *
   * @param string[] $affectedSupportedMinors
   */
  public function setAffectedSupportedMinors($affectedSupportedMinors)
  {
    $this->affectedSupportedMinors = $affectedSupportedMinors;
  }
  /**
   * @return string[]
   */
  public function getAffectedSupportedMinors()
  {
    return $this->affectedSupportedMinors;
  }
  /**
   * A brief description of the bulletin. See the bulletin pointed to by the
   * bulletin_uri field for an expanded description.
   *
   * @param string $briefDescription
   */
  public function setBriefDescription($briefDescription)
  {
    $this->briefDescription = $briefDescription;
  }
  /**
   * @return string
   */
  public function getBriefDescription()
  {
    return $this->briefDescription;
  }
  /**
   * The ID of the bulletin corresponding to the vulnerability.
   *
   * @param string $bulletinId
   */
  public function setBulletinId($bulletinId)
  {
    $this->bulletinId = $bulletinId;
  }
  /**
   * @return string
   */
  public function getBulletinId()
  {
    return $this->bulletinId;
  }
  /**
   * The URI link to the bulletin on the website for more information.
   *
   * @param string $bulletinUri
   */
  public function setBulletinUri($bulletinUri)
  {
    $this->bulletinUri = $bulletinUri;
  }
  /**
   * @return string
   */
  public function getBulletinUri()
  {
    return $this->bulletinUri;
  }
  /**
   * The CVEs associated with this bulletin.
   *
   * @param string[] $cveIds
   */
  public function setCveIds($cveIds)
  {
    $this->cveIds = $cveIds;
  }
  /**
   * @return string[]
   */
  public function getCveIds()
  {
    return $this->cveIds;
  }
  /**
   * If this field is specified, it means there are manual steps that the user
   * must take to make their clusters safe.
   *
   * @param bool $manualStepsRequired
   */
  public function setManualStepsRequired($manualStepsRequired)
  {
    $this->manualStepsRequired = $manualStepsRequired;
  }
  /**
   * @return bool
   */
  public function getManualStepsRequired()
  {
    return $this->manualStepsRequired;
  }
  /**
   * The GKE versions where this vulnerability is mitigated.
   *
   * @param string[] $mitigatedVersions
   */
  public function setMitigatedVersions($mitigatedVersions)
  {
    $this->mitigatedVersions = $mitigatedVersions;
  }
  /**
   * @return string[]
   */
  public function getMitigatedVersions()
  {
    return $this->mitigatedVersions;
  }
  /**
   * The GKE versions where this vulnerability is patched.
   *
   * @param string[] $patchedVersions
   */
  public function setPatchedVersions($patchedVersions)
  {
    $this->patchedVersions = $patchedVersions;
  }
  /**
   * @return string[]
   */
  public function getPatchedVersions()
  {
    return $this->patchedVersions;
  }
  /**
   * The resource type (node/control plane) that has the vulnerability. Multiple
   * notifications (1 notification per resource type) will be sent for a
   * vulnerability that affects > 1 resource type.
   *
   * @param string $resourceTypeAffected
   */
  public function setResourceTypeAffected($resourceTypeAffected)
  {
    $this->resourceTypeAffected = $resourceTypeAffected;
  }
  /**
   * @return string
   */
  public function getResourceTypeAffected()
  {
    return $this->resourceTypeAffected;
  }
  /**
   * The severity of this bulletin as it relates to GKE.
   *
   * @param string $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return string
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * This represents a version selected from the patched_versions field that the
   * cluster receiving this notification should most likely want to upgrade to
   * based on its current version. Note that if this notification is being
   * received by a given cluster, it means that this version is currently
   * available as an upgrade target in that cluster's location.
   *
   * @param string $suggestedUpgradeTarget
   */
  public function setSuggestedUpgradeTarget($suggestedUpgradeTarget)
  {
    $this->suggestedUpgradeTarget = $suggestedUpgradeTarget;
  }
  /**
   * @return string
   */
  public function getSuggestedUpgradeTarget()
  {
    return $this->suggestedUpgradeTarget;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityBulletinEvent::class, 'Google_Service_Container_SecurityBulletinEvent');
