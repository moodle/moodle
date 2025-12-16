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

class RestoreConfig extends \Google\Collection
{
  /**
   * Unspecified. Only allowed if no cluster-scoped resources will be restored.
   */
  public const CLUSTER_RESOURCE_CONFLICT_POLICY_CLUSTER_RESOURCE_CONFLICT_POLICY_UNSPECIFIED = 'CLUSTER_RESOURCE_CONFLICT_POLICY_UNSPECIFIED';
  /**
   * Do not attempt to restore the conflicting resource.
   */
  public const CLUSTER_RESOURCE_CONFLICT_POLICY_USE_EXISTING_VERSION = 'USE_EXISTING_VERSION';
  /**
   * Delete the existing version before re-creating it from the Backup. This is
   * a dangerous option which could cause unintentional data loss if used
   * inappropriately. For example, deleting a CRD will cause Kubernetes to
   * delete all CRs of that type.
   */
  public const CLUSTER_RESOURCE_CONFLICT_POLICY_USE_BACKUP_VERSION = 'USE_BACKUP_VERSION';
  /**
   * Unspecified (invalid).
   */
  public const NAMESPACED_RESOURCE_RESTORE_MODE_NAMESPACED_RESOURCE_RESTORE_MODE_UNSPECIFIED = 'NAMESPACED_RESOURCE_RESTORE_MODE_UNSPECIFIED';
  /**
   * When conflicting top-level resources (either Namespaces or
   * ProtectedApplications, depending upon the scope) are encountered, this will
   * first trigger a delete of the conflicting resource AND ALL OF ITS
   * REFERENCED RESOURCES (e.g., all resources in the Namespace or all resources
   * referenced by the ProtectedApplication) before restoring the resources from
   * the Backup. This mode should only be used when you are intending to revert
   * some portion of a cluster to an earlier state.
   */
  public const NAMESPACED_RESOURCE_RESTORE_MODE_DELETE_AND_RESTORE = 'DELETE_AND_RESTORE';
  /**
   * If conflicting top-level resources (either Namespaces or
   * ProtectedApplications, depending upon the scope) are encountered at the
   * beginning of a restore process, the Restore will fail. If a conflict occurs
   * during the restore process itself (e.g., because an out of band process
   * creates conflicting resources), a conflict will be reported.
   */
  public const NAMESPACED_RESOURCE_RESTORE_MODE_FAIL_ON_CONFLICT = 'FAIL_ON_CONFLICT';
  /**
   * This mode merges the backup and the target cluster and skips the
   * conflicting resources. If a single resource to restore exists in the
   * cluster before restoration, the resource will be skipped, otherwise it will
   * be restored.
   */
  public const NAMESPACED_RESOURCE_RESTORE_MODE_MERGE_SKIP_ON_CONFLICT = 'MERGE_SKIP_ON_CONFLICT';
  /**
   * This mode merges the backup and the target cluster and skips the
   * conflicting resources except volume data. If a PVC to restore already
   * exists, this mode will restore/reconnect the volume without overwriting the
   * PVC. It is similar to MERGE_SKIP_ON_CONFLICT except that it will apply the
   * volume data policy for the conflicting PVCs: -
   * RESTORE_VOLUME_DATA_FROM_BACKUP: restore data only and respect the reclaim
   * policy of the original PV; - REUSE_VOLUME_HANDLE_FROM_BACKUP: reconnect and
   * respect the reclaim policy of the original PV; -
   * NO_VOLUME_DATA_RESTORATION: new provision and respect the reclaim policy of
   * the original PV. Note that this mode could cause data loss as the original
   * PV can be retained or deleted depending on its reclaim policy.
   */
  public const NAMESPACED_RESOURCE_RESTORE_MODE_MERGE_REPLACE_VOLUME_ON_CONFLICT = 'MERGE_REPLACE_VOLUME_ON_CONFLICT';
  /**
   * This mode merges the backup and the target cluster and replaces the
   * conflicting resources with the ones in the backup. If a single resource to
   * restore exists in the cluster before restoration, the resource will be
   * replaced with the one from the backup. To replace an existing resource, the
   * first attempt is to update the resource to match the one from the backup;
   * if the update fails, the second attempt is to delete the resource and
   * restore it from the backup. Note that this mode could cause data loss as it
   * replaces the existing resources in the target cluster, and the original PV
   * can be retained or deleted depending on its reclaim policy.
   */
  public const NAMESPACED_RESOURCE_RESTORE_MODE_MERGE_REPLACE_ON_CONFLICT = 'MERGE_REPLACE_ON_CONFLICT';
  /**
   * Unspecified (illegal).
   */
  public const VOLUME_DATA_RESTORE_POLICY_VOLUME_DATA_RESTORE_POLICY_UNSPECIFIED = 'VOLUME_DATA_RESTORE_POLICY_UNSPECIFIED';
  /**
   * For each PVC to be restored, create a new underlying volume and PV from the
   * corresponding VolumeBackup contained within the Backup.
   */
  public const VOLUME_DATA_RESTORE_POLICY_RESTORE_VOLUME_DATA_FROM_BACKUP = 'RESTORE_VOLUME_DATA_FROM_BACKUP';
  /**
   * For each PVC to be restored, attempt to reuse the original PV contained in
   * the Backup (with its original underlying volume). This option is likely
   * only usable when restoring a workload to its original cluster.
   */
  public const VOLUME_DATA_RESTORE_POLICY_REUSE_VOLUME_HANDLE_FROM_BACKUP = 'REUSE_VOLUME_HANDLE_FROM_BACKUP';
  /**
   * For each PVC to be restored, create PVC without any particular action to
   * restore data. In this case, the normal Kubernetes provisioning logic would
   * kick in, and this would likely result in either dynamically provisioning
   * blank PVs or binding to statically provisioned PVs.
   */
  public const VOLUME_DATA_RESTORE_POLICY_NO_VOLUME_DATA_RESTORATION = 'NO_VOLUME_DATA_RESTORATION';
  protected $collection_key = 'volumeDataRestorePolicyBindings';
  /**
   * Restore all namespaced resources in the Backup if set to "True". Specifying
   * this field to "False" is an error.
   *
   * @var bool
   */
  public $allNamespaces;
  /**
   * Optional. Defines the behavior for handling the situation where cluster-
   * scoped resources being restored already exist in the target cluster. This
   * MUST be set to a value other than
   * CLUSTER_RESOURCE_CONFLICT_POLICY_UNSPECIFIED if
   * cluster_resource_restore_scope is not empty.
   *
   * @var string
   */
  public $clusterResourceConflictPolicy;
  protected $clusterResourceRestoreScopeType = ClusterResourceRestoreScope::class;
  protected $clusterResourceRestoreScopeDataType = '';
  protected $excludedNamespacesType = Namespaces::class;
  protected $excludedNamespacesDataType = '';
  /**
   * Optional. Defines the behavior for handling the situation where sets of
   * namespaced resources being restored already exist in the target cluster.
   * This MUST be set to a value other than
   * NAMESPACED_RESOURCE_RESTORE_MODE_UNSPECIFIED.
   *
   * @var string
   */
  public $namespacedResourceRestoreMode;
  /**
   * Do not restore any namespaced resources if set to "True". Specifying this
   * field to "False" is not allowed.
   *
   * @var bool
   */
  public $noNamespaces;
  protected $restoreOrderType = RestoreOrder::class;
  protected $restoreOrderDataType = '';
  protected $selectedApplicationsType = NamespacedNames::class;
  protected $selectedApplicationsDataType = '';
  protected $selectedNamespacesType = Namespaces::class;
  protected $selectedNamespacesDataType = '';
  protected $substitutionRulesType = SubstitutionRule::class;
  protected $substitutionRulesDataType = 'array';
  protected $transformationRulesType = TransformationRule::class;
  protected $transformationRulesDataType = 'array';
  /**
   * Optional. Specifies the mechanism to be used to restore volume data.
   * Default: VOLUME_DATA_RESTORE_POLICY_UNSPECIFIED (will be treated as
   * NO_VOLUME_DATA_RESTORATION).
   *
   * @var string
   */
  public $volumeDataRestorePolicy;
  protected $volumeDataRestorePolicyBindingsType = VolumeDataRestorePolicyBinding::class;
  protected $volumeDataRestorePolicyBindingsDataType = 'array';

  /**
   * Restore all namespaced resources in the Backup if set to "True". Specifying
   * this field to "False" is an error.
   *
   * @param bool $allNamespaces
   */
  public function setAllNamespaces($allNamespaces)
  {
    $this->allNamespaces = $allNamespaces;
  }
  /**
   * @return bool
   */
  public function getAllNamespaces()
  {
    return $this->allNamespaces;
  }
  /**
   * Optional. Defines the behavior for handling the situation where cluster-
   * scoped resources being restored already exist in the target cluster. This
   * MUST be set to a value other than
   * CLUSTER_RESOURCE_CONFLICT_POLICY_UNSPECIFIED if
   * cluster_resource_restore_scope is not empty.
   *
   * Accepted values: CLUSTER_RESOURCE_CONFLICT_POLICY_UNSPECIFIED,
   * USE_EXISTING_VERSION, USE_BACKUP_VERSION
   *
   * @param self::CLUSTER_RESOURCE_CONFLICT_POLICY_* $clusterResourceConflictPolicy
   */
  public function setClusterResourceConflictPolicy($clusterResourceConflictPolicy)
  {
    $this->clusterResourceConflictPolicy = $clusterResourceConflictPolicy;
  }
  /**
   * @return self::CLUSTER_RESOURCE_CONFLICT_POLICY_*
   */
  public function getClusterResourceConflictPolicy()
  {
    return $this->clusterResourceConflictPolicy;
  }
  /**
   * Optional. Identifies the cluster-scoped resources to restore from the
   * Backup. Not specifying it means NO cluster resource will be restored.
   *
   * @param ClusterResourceRestoreScope $clusterResourceRestoreScope
   */
  public function setClusterResourceRestoreScope(ClusterResourceRestoreScope $clusterResourceRestoreScope)
  {
    $this->clusterResourceRestoreScope = $clusterResourceRestoreScope;
  }
  /**
   * @return ClusterResourceRestoreScope
   */
  public function getClusterResourceRestoreScope()
  {
    return $this->clusterResourceRestoreScope;
  }
  /**
   * A list of selected namespaces excluded from restoration. All namespaces
   * except those in this list will be restored.
   *
   * @param Namespaces $excludedNamespaces
   */
  public function setExcludedNamespaces(Namespaces $excludedNamespaces)
  {
    $this->excludedNamespaces = $excludedNamespaces;
  }
  /**
   * @return Namespaces
   */
  public function getExcludedNamespaces()
  {
    return $this->excludedNamespaces;
  }
  /**
   * Optional. Defines the behavior for handling the situation where sets of
   * namespaced resources being restored already exist in the target cluster.
   * This MUST be set to a value other than
   * NAMESPACED_RESOURCE_RESTORE_MODE_UNSPECIFIED.
   *
   * Accepted values: NAMESPACED_RESOURCE_RESTORE_MODE_UNSPECIFIED,
   * DELETE_AND_RESTORE, FAIL_ON_CONFLICT, MERGE_SKIP_ON_CONFLICT,
   * MERGE_REPLACE_VOLUME_ON_CONFLICT, MERGE_REPLACE_ON_CONFLICT
   *
   * @param self::NAMESPACED_RESOURCE_RESTORE_MODE_* $namespacedResourceRestoreMode
   */
  public function setNamespacedResourceRestoreMode($namespacedResourceRestoreMode)
  {
    $this->namespacedResourceRestoreMode = $namespacedResourceRestoreMode;
  }
  /**
   * @return self::NAMESPACED_RESOURCE_RESTORE_MODE_*
   */
  public function getNamespacedResourceRestoreMode()
  {
    return $this->namespacedResourceRestoreMode;
  }
  /**
   * Do not restore any namespaced resources if set to "True". Specifying this
   * field to "False" is not allowed.
   *
   * @param bool $noNamespaces
   */
  public function setNoNamespaces($noNamespaces)
  {
    $this->noNamespaces = $noNamespaces;
  }
  /**
   * @return bool
   */
  public function getNoNamespaces()
  {
    return $this->noNamespaces;
  }
  /**
   * Optional. RestoreOrder contains custom ordering to use on a Restore.
   *
   * @param RestoreOrder $restoreOrder
   */
  public function setRestoreOrder(RestoreOrder $restoreOrder)
  {
    $this->restoreOrder = $restoreOrder;
  }
  /**
   * @return RestoreOrder
   */
  public function getRestoreOrder()
  {
    return $this->restoreOrder;
  }
  /**
   * A list of selected ProtectedApplications to restore. The listed
   * ProtectedApplications and all the resources to which they refer will be
   * restored.
   *
   * @param NamespacedNames $selectedApplications
   */
  public function setSelectedApplications(NamespacedNames $selectedApplications)
  {
    $this->selectedApplications = $selectedApplications;
  }
  /**
   * @return NamespacedNames
   */
  public function getSelectedApplications()
  {
    return $this->selectedApplications;
  }
  /**
   * A list of selected Namespaces to restore from the Backup. The listed
   * Namespaces and all resources contained in them will be restored.
   *
   * @param Namespaces $selectedNamespaces
   */
  public function setSelectedNamespaces(Namespaces $selectedNamespaces)
  {
    $this->selectedNamespaces = $selectedNamespaces;
  }
  /**
   * @return Namespaces
   */
  public function getSelectedNamespaces()
  {
    return $this->selectedNamespaces;
  }
  /**
   * Optional. A list of transformation rules to be applied against Kubernetes
   * resources as they are selected for restoration from a Backup. Rules are
   * executed in order defined - this order matters, as changes made by a rule
   * may impact the filtering logic of subsequent rules. An empty list means no
   * substitution will occur.
   *
   * @param SubstitutionRule[] $substitutionRules
   */
  public function setSubstitutionRules($substitutionRules)
  {
    $this->substitutionRules = $substitutionRules;
  }
  /**
   * @return SubstitutionRule[]
   */
  public function getSubstitutionRules()
  {
    return $this->substitutionRules;
  }
  /**
   * Optional. A list of transformation rules to be applied against Kubernetes
   * resources as they are selected for restoration from a Backup. Rules are
   * executed in order defined - this order matters, as changes made by a rule
   * may impact the filtering logic of subsequent rules. An empty list means no
   * transformation will occur.
   *
   * @param TransformationRule[] $transformationRules
   */
  public function setTransformationRules($transformationRules)
  {
    $this->transformationRules = $transformationRules;
  }
  /**
   * @return TransformationRule[]
   */
  public function getTransformationRules()
  {
    return $this->transformationRules;
  }
  /**
   * Optional. Specifies the mechanism to be used to restore volume data.
   * Default: VOLUME_DATA_RESTORE_POLICY_UNSPECIFIED (will be treated as
   * NO_VOLUME_DATA_RESTORATION).
   *
   * Accepted values: VOLUME_DATA_RESTORE_POLICY_UNSPECIFIED,
   * RESTORE_VOLUME_DATA_FROM_BACKUP, REUSE_VOLUME_HANDLE_FROM_BACKUP,
   * NO_VOLUME_DATA_RESTORATION
   *
   * @param self::VOLUME_DATA_RESTORE_POLICY_* $volumeDataRestorePolicy
   */
  public function setVolumeDataRestorePolicy($volumeDataRestorePolicy)
  {
    $this->volumeDataRestorePolicy = $volumeDataRestorePolicy;
  }
  /**
   * @return self::VOLUME_DATA_RESTORE_POLICY_*
   */
  public function getVolumeDataRestorePolicy()
  {
    return $this->volumeDataRestorePolicy;
  }
  /**
   * Optional. A table that binds volumes by their scope to a restore policy.
   * Bindings must have a unique scope. Any volumes not scoped in the bindings
   * are subject to the policy defined in volume_data_restore_policy.
   *
   * @param VolumeDataRestorePolicyBinding[] $volumeDataRestorePolicyBindings
   */
  public function setVolumeDataRestorePolicyBindings($volumeDataRestorePolicyBindings)
  {
    $this->volumeDataRestorePolicyBindings = $volumeDataRestorePolicyBindings;
  }
  /**
   * @return VolumeDataRestorePolicyBinding[]
   */
  public function getVolumeDataRestorePolicyBindings()
  {
    return $this->volumeDataRestorePolicyBindings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreConfig::class, 'Google_Service_BackupforGKE_RestoreConfig');
