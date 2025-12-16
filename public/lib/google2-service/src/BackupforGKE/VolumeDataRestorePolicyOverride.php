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

namespace Google\Service\BackupforGKE;

class VolumeDataRestorePolicyOverride extends \Google\Model
{
  /**
   * Unspecified (illegal).
   */
  public const POLICY_VOLUME_DATA_RESTORE_POLICY_UNSPECIFIED = 'VOLUME_DATA_RESTORE_POLICY_UNSPECIFIED';
  /**
   * For each PVC to be restored, create a new underlying volume and PV from the
   * corresponding VolumeBackup contained within the Backup.
   */
  public const POLICY_RESTORE_VOLUME_DATA_FROM_BACKUP = 'RESTORE_VOLUME_DATA_FROM_BACKUP';
  /**
   * For each PVC to be restored, attempt to reuse the original PV contained in
   * the Backup (with its original underlying volume). This option is likely
   * only usable when restoring a workload to its original cluster.
   */
  public const POLICY_REUSE_VOLUME_HANDLE_FROM_BACKUP = 'REUSE_VOLUME_HANDLE_FROM_BACKUP';
  /**
   * For each PVC to be restored, create PVC without any particular action to
   * restore data. In this case, the normal Kubernetes provisioning logic would
   * kick in, and this would likely result in either dynamically provisioning
   * blank PVs or binding to statically provisioned PVs.
   */
  public const POLICY_NO_VOLUME_DATA_RESTORATION = 'NO_VOLUME_DATA_RESTORATION';
  /**
   * Required. The VolumeDataRestorePolicy to apply when restoring volumes in
   * scope.
   *
   * @var string
   */
  public $policy;
  protected $selectedPvcsType = NamespacedNames::class;
  protected $selectedPvcsDataType = '';

  /**
   * Required. The VolumeDataRestorePolicy to apply when restoring volumes in
   * scope.
   *
   * Accepted values: VOLUME_DATA_RESTORE_POLICY_UNSPECIFIED,
   * RESTORE_VOLUME_DATA_FROM_BACKUP, REUSE_VOLUME_HANDLE_FROM_BACKUP,
   * NO_VOLUME_DATA_RESTORATION
   *
   * @param self::POLICY_* $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return self::POLICY_*
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * A list of PVCs to apply the policy override to.
   *
   * @param NamespacedNames $selectedPvcs
   */
  public function setSelectedPvcs(NamespacedNames $selectedPvcs)
  {
    $this->selectedPvcs = $selectedPvcs;
  }
  /**
   * @return NamespacedNames
   */
  public function getSelectedPvcs()
  {
    return $this->selectedPvcs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeDataRestorePolicyOverride::class, 'Google_Service_BackupforGKE_VolumeDataRestorePolicyOverride');
