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

class Project extends \Google\Collection
{
  /**
   * Enterprise tier protection billed annually.
   */
  public const CLOUD_ARMOR_TIER_CA_ENTERPRISE_ANNUAL = 'CA_ENTERPRISE_ANNUAL';
  /**
   * Enterprise tier protection billed monthly.
   */
  public const CLOUD_ARMOR_TIER_CA_ENTERPRISE_PAYGO = 'CA_ENTERPRISE_PAYGO';
  /**
   * Standard protection.
   */
  public const CLOUD_ARMOR_TIER_CA_STANDARD = 'CA_STANDARD';
  /**
   * Public internet quality with fixed bandwidth.
   */
  public const DEFAULT_NETWORK_TIER_FIXED_STANDARD = 'FIXED_STANDARD';
  /**
   * High quality, Google-grade network tier, support for all networking
   * products.
   */
  public const DEFAULT_NETWORK_TIER_PREMIUM = 'PREMIUM';
  /**
   * Public internet quality, only limited support for other networking
   * products.
   */
  public const DEFAULT_NETWORK_TIER_STANDARD = 'STANDARD';
  /**
   * (Output only) Temporary tier for FIXED_STANDARD when fixed standard tier is
   * expired or not configured.
   */
  public const DEFAULT_NETWORK_TIER_STANDARD_OVERRIDES_FIXED_STANDARD = 'STANDARD_OVERRIDES_FIXED_STANDARD';
  public const VM_DNS_SETTING_GLOBAL_DEFAULT = 'GLOBAL_DEFAULT';
  public const VM_DNS_SETTING_UNSPECIFIED_VM_DNS_SETTING = 'UNSPECIFIED_VM_DNS_SETTING';
  public const VM_DNS_SETTING_ZONAL_DEFAULT = 'ZONAL_DEFAULT';
  public const VM_DNS_SETTING_ZONAL_ONLY = 'ZONAL_ONLY';
  public const XPN_PROJECT_STATUS_HOST = 'HOST';
  public const XPN_PROJECT_STATUS_UNSPECIFIED_XPN_PROJECT_STATUS = 'UNSPECIFIED_XPN_PROJECT_STATUS';
  protected $collection_key = 'quotas';
  /**
   * Output only. [Output Only] The Cloud Armor tier for this project. It can be
   * one of the following values: CA_STANDARD,CA_ENTERPRISE_PAYGO.
   *
   * If this field is not specified, it is assumed to beCA_STANDARD.
   *
   * @var string
   */
  public $cloudArmorTier;
  protected $commonInstanceMetadataType = Metadata::class;
  protected $commonInstanceMetadataDataType = '';
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * This signifies the default network tier used for configuring resources of
   * the project and can only take the following values:PREMIUM, STANDARD.
   * Initially the default network tier is PREMIUM.
   *
   * @var string
   */
  public $defaultNetworkTier;
  /**
   * [Output Only] Default service account used by VMs running in this project.
   *
   * @var string
   */
  public $defaultServiceAccount;
  /**
   * An optional textual description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * An optional list of restricted features enabled for use on this project.
   *
   * @var string[]
   */
  public $enabledFeatures;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server. This is *not* the project ID, and is just a unique
   * ID used by Compute Engine to identify resources.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#project for
   * projects.
   *
   * @var string
   */
  public $kind;
  /**
   * The project ID. For example: my-example-project. Use the project ID to make
   * requests to Compute Engine.
   *
   * @var string
   */
  public $name;
  protected $quotasType = Quota::class;
  protected $quotasDataType = 'array';
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $usageExportLocationType = UsageExportLocation::class;
  protected $usageExportLocationDataType = '';
  /**
   * Output only. [Output Only] Default internal DNS setting used by VMs running
   * in this project.
   *
   * @var string
   */
  public $vmDnsSetting;
  /**
   * [Output Only] The role this project has in a shared VPC configuration.
   * Currently, only projects with the host role, which is specified by the
   * value HOST, are differentiated.
   *
   * @var string
   */
  public $xpnProjectStatus;

  /**
   * Output only. [Output Only] The Cloud Armor tier for this project. It can be
   * one of the following values: CA_STANDARD,CA_ENTERPRISE_PAYGO.
   *
   * If this field is not specified, it is assumed to beCA_STANDARD.
   *
   * Accepted values: CA_ENTERPRISE_ANNUAL, CA_ENTERPRISE_PAYGO, CA_STANDARD
   *
   * @param self::CLOUD_ARMOR_TIER_* $cloudArmorTier
   */
  public function setCloudArmorTier($cloudArmorTier)
  {
    $this->cloudArmorTier = $cloudArmorTier;
  }
  /**
   * @return self::CLOUD_ARMOR_TIER_*
   */
  public function getCloudArmorTier()
  {
    return $this->cloudArmorTier;
  }
  /**
   * Metadata key/value pairs available to all instances contained in this
   * project. See Custom metadata for more information.
   *
   * @param Metadata $commonInstanceMetadata
   */
  public function setCommonInstanceMetadata(Metadata $commonInstanceMetadata)
  {
    $this->commonInstanceMetadata = $commonInstanceMetadata;
  }
  /**
   * @return Metadata
   */
  public function getCommonInstanceMetadata()
  {
    return $this->commonInstanceMetadata;
  }
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * This signifies the default network tier used for configuring resources of
   * the project and can only take the following values:PREMIUM, STANDARD.
   * Initially the default network tier is PREMIUM.
   *
   * Accepted values: FIXED_STANDARD, PREMIUM, STANDARD,
   * STANDARD_OVERRIDES_FIXED_STANDARD
   *
   * @param self::DEFAULT_NETWORK_TIER_* $defaultNetworkTier
   */
  public function setDefaultNetworkTier($defaultNetworkTier)
  {
    $this->defaultNetworkTier = $defaultNetworkTier;
  }
  /**
   * @return self::DEFAULT_NETWORK_TIER_*
   */
  public function getDefaultNetworkTier()
  {
    return $this->defaultNetworkTier;
  }
  /**
   * [Output Only] Default service account used by VMs running in this project.
   *
   * @param string $defaultServiceAccount
   */
  public function setDefaultServiceAccount($defaultServiceAccount)
  {
    $this->defaultServiceAccount = $defaultServiceAccount;
  }
  /**
   * @return string
   */
  public function getDefaultServiceAccount()
  {
    return $this->defaultServiceAccount;
  }
  /**
   * An optional textual description of the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * An optional list of restricted features enabled for use on this project.
   *
   * @param string[] $enabledFeatures
   */
  public function setEnabledFeatures($enabledFeatures)
  {
    $this->enabledFeatures = $enabledFeatures;
  }
  /**
   * @return string[]
   */
  public function getEnabledFeatures()
  {
    return $this->enabledFeatures;
  }
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server. This is *not* the project ID, and is just a unique
   * ID used by Compute Engine to identify resources.
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
   * Output only. [Output Only] Type of the resource. Always compute#project for
   * projects.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The project ID. For example: my-example-project. Use the project ID to make
   * requests to Compute Engine.
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
   * [Output Only] Quotas assigned to this project.
   *
   * @param Quota[] $quotas
   */
  public function setQuotas($quotas)
  {
    $this->quotas = $quotas;
  }
  /**
   * @return Quota[]
   */
  public function getQuotas()
  {
    return $this->quotas;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * An optional naming prefix for daily usage reports and the Google Cloud
   * Storage bucket where they are stored.
   *
   * @param UsageExportLocation $usageExportLocation
   */
  public function setUsageExportLocation(UsageExportLocation $usageExportLocation)
  {
    $this->usageExportLocation = $usageExportLocation;
  }
  /**
   * @return UsageExportLocation
   */
  public function getUsageExportLocation()
  {
    return $this->usageExportLocation;
  }
  /**
   * Output only. [Output Only] Default internal DNS setting used by VMs running
   * in this project.
   *
   * Accepted values: GLOBAL_DEFAULT, UNSPECIFIED_VM_DNS_SETTING, ZONAL_DEFAULT,
   * ZONAL_ONLY
   *
   * @param self::VM_DNS_SETTING_* $vmDnsSetting
   */
  public function setVmDnsSetting($vmDnsSetting)
  {
    $this->vmDnsSetting = $vmDnsSetting;
  }
  /**
   * @return self::VM_DNS_SETTING_*
   */
  public function getVmDnsSetting()
  {
    return $this->vmDnsSetting;
  }
  /**
   * [Output Only] The role this project has in a shared VPC configuration.
   * Currently, only projects with the host role, which is specified by the
   * value HOST, are differentiated.
   *
   * Accepted values: HOST, UNSPECIFIED_XPN_PROJECT_STATUS
   *
   * @param self::XPN_PROJECT_STATUS_* $xpnProjectStatus
   */
  public function setXpnProjectStatus($xpnProjectStatus)
  {
    $this->xpnProjectStatus = $xpnProjectStatus;
  }
  /**
   * @return self::XPN_PROJECT_STATUS_*
   */
  public function getXpnProjectStatus()
  {
    return $this->xpnProjectStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Project::class, 'Google_Service_Compute_Project');
