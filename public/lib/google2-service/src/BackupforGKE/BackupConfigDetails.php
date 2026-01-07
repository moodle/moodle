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

class BackupConfigDetails extends \Google\Model
{
  /**
   * Output only. If True, include all namespaced resources
   *
   * @var bool
   */
  public $allNamespaces;
  protected $encryptionKeyType = EncryptionKey::class;
  protected $encryptionKeyDataType = '';
  /**
   * Output only. This flag specifies whether Kubernetes Secret resources should
   * be included when they fall into the scope of Backups. Default: False
   *
   * @var bool
   */
  public $includeSecrets;
  /**
   * Output only. This flag specifies whether volume data should be backed up
   * when PVCs are included in the scope of a Backup. Default: False
   *
   * @var bool
   */
  public $includeVolumeData;
  protected $selectedApplicationsType = NamespacedNames::class;
  protected $selectedApplicationsDataType = '';
  protected $selectedNamespacesType = Namespaces::class;
  protected $selectedNamespacesDataType = '';

  /**
   * Output only. If True, include all namespaced resources
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
   * Output only. This defines a customer managed encryption key that will be
   * used to encrypt the "config" portion (the Kubernetes resources) of Backups
   * created via this plan. Default (empty): Config backup artifacts will not be
   * encrypted.
   *
   * @param EncryptionKey $encryptionKey
   */
  public function setEncryptionKey(EncryptionKey $encryptionKey)
  {
    $this->encryptionKey = $encryptionKey;
  }
  /**
   * @return EncryptionKey
   */
  public function getEncryptionKey()
  {
    return $this->encryptionKey;
  }
  /**
   * Output only. This flag specifies whether Kubernetes Secret resources should
   * be included when they fall into the scope of Backups. Default: False
   *
   * @param bool $includeSecrets
   */
  public function setIncludeSecrets($includeSecrets)
  {
    $this->includeSecrets = $includeSecrets;
  }
  /**
   * @return bool
   */
  public function getIncludeSecrets()
  {
    return $this->includeSecrets;
  }
  /**
   * Output only. This flag specifies whether volume data should be backed up
   * when PVCs are included in the scope of a Backup. Default: False
   *
   * @param bool $includeVolumeData
   */
  public function setIncludeVolumeData($includeVolumeData)
  {
    $this->includeVolumeData = $includeVolumeData;
  }
  /**
   * @return bool
   */
  public function getIncludeVolumeData()
  {
    return $this->includeVolumeData;
  }
  /**
   * Output only. If set, include just the resources referenced by the listed
   * ProtectedApplications.
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
   * Output only. If set, include just the resources in the listed namespaces.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupConfigDetails::class, 'Google_Service_BackupforGKE_BackupConfigDetails');
