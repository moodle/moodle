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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Organization extends \Google\Collection
{
  /**
   * Billing type not specified.
   */
  public const BILLING_TYPE_BILLING_TYPE_UNSPECIFIED = 'BILLING_TYPE_UNSPECIFIED';
  /**
   * A pre-paid subscription to Apigee.
   */
  public const BILLING_TYPE_SUBSCRIPTION = 'SUBSCRIPTION';
  /**
   * Free and limited access to Apigee for evaluation purposes only.
   */
  public const BILLING_TYPE_EVALUATION = 'EVALUATION';
  /**
   * Access to Apigee using a Pay-As-You-Go plan.
   */
  public const BILLING_TYPE_PAYG = 'PAYG';
  /**
   * Runtime type not specified.
   */
  public const RUNTIME_TYPE_RUNTIME_TYPE_UNSPECIFIED = 'RUNTIME_TYPE_UNSPECIFIED';
  /**
   * Google-managed Apigee runtime.
   */
  public const RUNTIME_TYPE_CLOUD = 'CLOUD';
  /**
   * User-managed Apigee hybrid runtime.
   */
  public const RUNTIME_TYPE_HYBRID = 'HYBRID';
  /**
   * Resource is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is provisioned and ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Subscription plan not specified.
   */
  public const SUBSCRIPTION_PLAN_SUBSCRIPTION_PLAN_UNSPECIFIED = 'SUBSCRIPTION_PLAN_UNSPECIFIED';
  /**
   * Traditional subscription plan.
   */
  public const SUBSCRIPTION_PLAN_SUBSCRIPTION_2021 = 'SUBSCRIPTION_2021';
  /**
   * New subscription plan that provides standard proxy and scaled proxy
   * implementation.
   */
  public const SUBSCRIPTION_PLAN_SUBSCRIPTION_2024 = 'SUBSCRIPTION_2024';
  /**
   * Subscription type not specified.
   */
  public const SUBSCRIPTION_TYPE_SUBSCRIPTION_TYPE_UNSPECIFIED = 'SUBSCRIPTION_TYPE_UNSPECIFIED';
  /**
   * Full subscription to Apigee has been purchased.
   */
  public const SUBSCRIPTION_TYPE_PAID = 'PAID';
  /**
   * Subscription to Apigee is free, limited, and used for evaluation purposes
   * only.
   */
  public const SUBSCRIPTION_TYPE_TRIAL = 'TRIAL';
  /**
   * Subscription type not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Subscription to Apigee is free, limited, and used for evaluation purposes
   * only.
   */
  public const TYPE_TYPE_TRIAL = 'TYPE_TRIAL';
  /**
   * Full subscription to Apigee has been purchased. See [Apigee
   * pricing](https://cloud.google.com/apigee/pricing/).
   */
  public const TYPE_TYPE_PAID = 'TYPE_PAID';
  /**
   * For internal users only.
   */
  public const TYPE_TYPE_INTERNAL = 'TYPE_INTERNAL';
  protected $collection_key = 'environments';
  protected $addonsConfigType = GoogleCloudApigeeV1AddonsConfig::class;
  protected $addonsConfigDataType = '';
  /**
   * Required. DEPRECATED: This field will eventually be deprecated and replaced
   * with a differently-named field. Primary Google Cloud region for analytics
   * data storage. For valid values, see [Create an Apigee
   * organization](https://cloud.google.com/apigee/docs/api-platform/get-
   * started/create-org).
   *
   * @deprecated
   * @var string
   */
  public $analyticsRegion;
  /**
   * Optional. Cloud KMS key name used for encrypting API consumer data. If not
   * specified or [BillingType](#BillingType) is `EVALUATION`, a Google-Managed
   * encryption key will be used. Format:
   * `projects/locations/keyRings/cryptoKeys`
   *
   * @var string
   */
  public $apiConsumerDataEncryptionKeyName;
  /**
   * Optional. This field is needed only for customers using non-default data
   * residency regions. Apigee stores some control plane data only in single
   * region. This field determines which single region Apigee should use. For
   * example: "us-west1" when control plane is in US or "europe-west2" when
   * control plane is in EU.
   *
   * @var string
   */
  public $apiConsumerDataLocation;
  /**
   * Output only. Apigee Project ID associated with the organization. Use this
   * project to allowlist Apigee in the Service Attachment when using private
   * service connect with Apigee.
   *
   * @var string
   */
  public $apigeeProjectId;
  /**
   * Not used by Apigee.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * Optional. Compute Engine network used for Service Networking to be peered
   * with Apigee runtime instances. See [Getting started with the Service
   * Networking API](https://cloud.google.com/service-
   * infrastructure/docs/service-networking/getting-started). Valid only when
   * [RuntimeType](#RuntimeType) is set to `CLOUD`. The value must be set before
   * the creation of a runtime instance and can be updated only when there are
   * no runtime instances. For example: `default`. When changing
   * authorizedNetwork, you must reconfigure VPC peering. After VPC peering with
   * previous network is deleted, [run the following
   * command](https://cloud.google.com/sdk/gcloud/reference/services/vpc-
   * peerings/delete): `gcloud services vpc-peerings delete --network=NETWORK`,
   * where `NETWORK` is the name of the previous network. This will delete the
   * previous Service Networking. Otherwise, you will get the following error:
   * `The resource 'projects/...-tp' is already linked to another shared VPC
   * host 'projects/...-tp`. Apigee also supports shared VPC (that is, the host
   * network project is not the same as the one that is peering with Apigee).
   * See [Shared VPC overview](https://cloud.google.com/vpc/docs/shared-vpc). To
   * use a shared VPC network, use the following format: `projects/{host-
   * project-id}/{region}/networks/{network-name}`. For example: `projects/my-
   * sharedvpc-host/global/networks/mynetwork` **Note:** Not supported for
   * Apigee hybrid.
   *
   * @var string
   */
  public $authorizedNetwork;
  /**
   * Optional. Billing type of the Apigee organization. See [Apigee
   * pricing](https://cloud.google.com/apigee/pricing).
   *
   * @var string
   */
  public $billingType;
  /**
   * Output only. Base64-encoded public certificate for the root CA of the
   * Apigee organization. Valid only when [RuntimeType](#RuntimeType) is
   * `CLOUD`.
   *
   * @var string
   */
  public $caCertificate;
  /**
   * Optional. Cloud KMS key name used for encrypting control plane data that is
   * stored in a multi region. Only used for the data residency region "US" or
   * "EU". If not specified or [BillingType](#BillingType) is `EVALUATION`, a
   * Google-Managed encryption key will be used. Format:
   * `projects/locations/keyRings/cryptoKeys`
   *
   * @var string
   */
  public $controlPlaneEncryptionKeyName;
  /**
   * Output only. Time that the Apigee organization was created in milliseconds
   * since epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Not used by Apigee.
   *
   * @var string
   */
  public $customerName;
  /**
   * Optional. Description of the Apigee organization.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Flag that specifies whether the VPC Peering through Private
   * Google Access should be disabled between the consumer network and Apigee.
   * Valid only when RuntimeType is set to CLOUD. Required if an
   * authorizedNetwork on the consumer project is not provided, in which case
   * the flag should be set to true. The value must be set before the creation
   * of any Apigee runtime instance and can be updated only when there are no
   * runtime instances. **Note:** Apigee will be deprecating the vpc peering
   * model that requires you to provide 'authorizedNetwork', by making the non-
   * peering model as the default way of provisioning Apigee organization in
   * future. So, this will be a temporary flag to enable the transition. Not
   * supported for Apigee hybrid.
   *
   * @var bool
   */
  public $disableVpcPeering;
  /**
   * Optional. Display name for the Apigee organization. Unused, but reserved
   * for future use.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. List of environments in the Apigee organization.
   *
   * @var string[]
   */
  public $environments;
  /**
   * Output only. Time that the Apigee organization is scheduled for deletion.
   *
   * @var string
   */
  public $expiresAt;
  /**
   * Output only. Time that the Apigee organization was last modified in
   * milliseconds since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Output only. Name of the Apigee organization.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Flag that specifies if internet egress is restricted for VPC
   * Service Controls. Valid only when runtime_type is `CLOUD` and
   * disable_vpc_peering is `true`.
   *
   * @var bool
   */
  public $networkEgressRestricted;
  /**
   * Optional. Configuration for the Portals settings.
   *
   * @var bool
   */
  public $portalDisabled;
  /**
   * Output only. Project ID associated with the Apigee organization.
   *
   * @var string
   */
  public $projectId;
  protected $propertiesType = GoogleCloudApigeeV1Properties::class;
  protected $propertiesDataType = '';
  /**
   * Optional. Cloud KMS key name used for encrypting the data that is stored
   * and replicated across runtime instances. Update is not allowed after the
   * organization is created. If not specified or [RuntimeType](#RuntimeType) is
   * `TRIAL`, a Google-Managed encryption key will be used. For example:
   * "projects/foo/locations/us/keyRings/bar/cryptoKeys/baz". **Note:** Not
   * supported for Apigee hybrid.
   *
   * @var string
   */
  public $runtimeDatabaseEncryptionKeyName;
  /**
   * Required. Runtime type of the Apigee organization based on the Apigee
   * subscription purchased.
   *
   * @var string
   */
  public $runtimeType;
  /**
   * Output only. State of the organization. Values other than ACTIVE means the
   * resource is not ready to use.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Subscription plan that the customer has purchased. Output
   * only.
   *
   * @var string
   */
  public $subscriptionPlan;
  /**
   * Output only. DEPRECATED: This will eventually be replaced by BillingType.
   * Subscription type of the Apigee organization. Valid values include trial
   * (free, limited, and for evaluation purposes only) or paid (full
   * subscription has been purchased). See [Apigee
   * pricing](https://cloud.google.com/apigee/pricing/).
   *
   * @deprecated
   * @var string
   */
  public $subscriptionType;
  /**
   * Not used by Apigee.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Addon configurations of the Apigee organization.
   *
   * @param GoogleCloudApigeeV1AddonsConfig $addonsConfig
   */
  public function setAddonsConfig(GoogleCloudApigeeV1AddonsConfig $addonsConfig)
  {
    $this->addonsConfig = $addonsConfig;
  }
  /**
   * @return GoogleCloudApigeeV1AddonsConfig
   */
  public function getAddonsConfig()
  {
    return $this->addonsConfig;
  }
  /**
   * Required. DEPRECATED: This field will eventually be deprecated and replaced
   * with a differently-named field. Primary Google Cloud region for analytics
   * data storage. For valid values, see [Create an Apigee
   * organization](https://cloud.google.com/apigee/docs/api-platform/get-
   * started/create-org).
   *
   * @deprecated
   * @param string $analyticsRegion
   */
  public function setAnalyticsRegion($analyticsRegion)
  {
    $this->analyticsRegion = $analyticsRegion;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getAnalyticsRegion()
  {
    return $this->analyticsRegion;
  }
  /**
   * Optional. Cloud KMS key name used for encrypting API consumer data. If not
   * specified or [BillingType](#BillingType) is `EVALUATION`, a Google-Managed
   * encryption key will be used. Format:
   * `projects/locations/keyRings/cryptoKeys`
   *
   * @param string $apiConsumerDataEncryptionKeyName
   */
  public function setApiConsumerDataEncryptionKeyName($apiConsumerDataEncryptionKeyName)
  {
    $this->apiConsumerDataEncryptionKeyName = $apiConsumerDataEncryptionKeyName;
  }
  /**
   * @return string
   */
  public function getApiConsumerDataEncryptionKeyName()
  {
    return $this->apiConsumerDataEncryptionKeyName;
  }
  /**
   * Optional. This field is needed only for customers using non-default data
   * residency regions. Apigee stores some control plane data only in single
   * region. This field determines which single region Apigee should use. For
   * example: "us-west1" when control plane is in US or "europe-west2" when
   * control plane is in EU.
   *
   * @param string $apiConsumerDataLocation
   */
  public function setApiConsumerDataLocation($apiConsumerDataLocation)
  {
    $this->apiConsumerDataLocation = $apiConsumerDataLocation;
  }
  /**
   * @return string
   */
  public function getApiConsumerDataLocation()
  {
    return $this->apiConsumerDataLocation;
  }
  /**
   * Output only. Apigee Project ID associated with the organization. Use this
   * project to allowlist Apigee in the Service Attachment when using private
   * service connect with Apigee.
   *
   * @param string $apigeeProjectId
   */
  public function setApigeeProjectId($apigeeProjectId)
  {
    $this->apigeeProjectId = $apigeeProjectId;
  }
  /**
   * @return string
   */
  public function getApigeeProjectId()
  {
    return $this->apigeeProjectId;
  }
  /**
   * Not used by Apigee.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Optional. Compute Engine network used for Service Networking to be peered
   * with Apigee runtime instances. See [Getting started with the Service
   * Networking API](https://cloud.google.com/service-
   * infrastructure/docs/service-networking/getting-started). Valid only when
   * [RuntimeType](#RuntimeType) is set to `CLOUD`. The value must be set before
   * the creation of a runtime instance and can be updated only when there are
   * no runtime instances. For example: `default`. When changing
   * authorizedNetwork, you must reconfigure VPC peering. After VPC peering with
   * previous network is deleted, [run the following
   * command](https://cloud.google.com/sdk/gcloud/reference/services/vpc-
   * peerings/delete): `gcloud services vpc-peerings delete --network=NETWORK`,
   * where `NETWORK` is the name of the previous network. This will delete the
   * previous Service Networking. Otherwise, you will get the following error:
   * `The resource 'projects/...-tp' is already linked to another shared VPC
   * host 'projects/...-tp`. Apigee also supports shared VPC (that is, the host
   * network project is not the same as the one that is peering with Apigee).
   * See [Shared VPC overview](https://cloud.google.com/vpc/docs/shared-vpc). To
   * use a shared VPC network, use the following format: `projects/{host-
   * project-id}/{region}/networks/{network-name}`. For example: `projects/my-
   * sharedvpc-host/global/networks/mynetwork` **Note:** Not supported for
   * Apigee hybrid.
   *
   * @param string $authorizedNetwork
   */
  public function setAuthorizedNetwork($authorizedNetwork)
  {
    $this->authorizedNetwork = $authorizedNetwork;
  }
  /**
   * @return string
   */
  public function getAuthorizedNetwork()
  {
    return $this->authorizedNetwork;
  }
  /**
   * Optional. Billing type of the Apigee organization. See [Apigee
   * pricing](https://cloud.google.com/apigee/pricing).
   *
   * Accepted values: BILLING_TYPE_UNSPECIFIED, SUBSCRIPTION, EVALUATION, PAYG
   *
   * @param self::BILLING_TYPE_* $billingType
   */
  public function setBillingType($billingType)
  {
    $this->billingType = $billingType;
  }
  /**
   * @return self::BILLING_TYPE_*
   */
  public function getBillingType()
  {
    return $this->billingType;
  }
  /**
   * Output only. Base64-encoded public certificate for the root CA of the
   * Apigee organization. Valid only when [RuntimeType](#RuntimeType) is
   * `CLOUD`.
   *
   * @param string $caCertificate
   */
  public function setCaCertificate($caCertificate)
  {
    $this->caCertificate = $caCertificate;
  }
  /**
   * @return string
   */
  public function getCaCertificate()
  {
    return $this->caCertificate;
  }
  /**
   * Optional. Cloud KMS key name used for encrypting control plane data that is
   * stored in a multi region. Only used for the data residency region "US" or
   * "EU". If not specified or [BillingType](#BillingType) is `EVALUATION`, a
   * Google-Managed encryption key will be used. Format:
   * `projects/locations/keyRings/cryptoKeys`
   *
   * @param string $controlPlaneEncryptionKeyName
   */
  public function setControlPlaneEncryptionKeyName($controlPlaneEncryptionKeyName)
  {
    $this->controlPlaneEncryptionKeyName = $controlPlaneEncryptionKeyName;
  }
  /**
   * @return string
   */
  public function getControlPlaneEncryptionKeyName()
  {
    return $this->controlPlaneEncryptionKeyName;
  }
  /**
   * Output only. Time that the Apigee organization was created in milliseconds
   * since epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Not used by Apigee.
   *
   * @param string $customerName
   */
  public function setCustomerName($customerName)
  {
    $this->customerName = $customerName;
  }
  /**
   * @return string
   */
  public function getCustomerName()
  {
    return $this->customerName;
  }
  /**
   * Optional. Description of the Apigee organization.
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
   * Optional. Flag that specifies whether the VPC Peering through Private
   * Google Access should be disabled between the consumer network and Apigee.
   * Valid only when RuntimeType is set to CLOUD. Required if an
   * authorizedNetwork on the consumer project is not provided, in which case
   * the flag should be set to true. The value must be set before the creation
   * of any Apigee runtime instance and can be updated only when there are no
   * runtime instances. **Note:** Apigee will be deprecating the vpc peering
   * model that requires you to provide 'authorizedNetwork', by making the non-
   * peering model as the default way of provisioning Apigee organization in
   * future. So, this will be a temporary flag to enable the transition. Not
   * supported for Apigee hybrid.
   *
   * @param bool $disableVpcPeering
   */
  public function setDisableVpcPeering($disableVpcPeering)
  {
    $this->disableVpcPeering = $disableVpcPeering;
  }
  /**
   * @return bool
   */
  public function getDisableVpcPeering()
  {
    return $this->disableVpcPeering;
  }
  /**
   * Optional. Display name for the Apigee organization. Unused, but reserved
   * for future use.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. List of environments in the Apigee organization.
   *
   * @param string[] $environments
   */
  public function setEnvironments($environments)
  {
    $this->environments = $environments;
  }
  /**
   * @return string[]
   */
  public function getEnvironments()
  {
    return $this->environments;
  }
  /**
   * Output only. Time that the Apigee organization is scheduled for deletion.
   *
   * @param string $expiresAt
   */
  public function setExpiresAt($expiresAt)
  {
    $this->expiresAt = $expiresAt;
  }
  /**
   * @return string
   */
  public function getExpiresAt()
  {
    return $this->expiresAt;
  }
  /**
   * Output only. Time that the Apigee organization was last modified in
   * milliseconds since epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * Output only. Name of the Apigee organization.
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
   * Optional. Flag that specifies if internet egress is restricted for VPC
   * Service Controls. Valid only when runtime_type is `CLOUD` and
   * disable_vpc_peering is `true`.
   *
   * @param bool $networkEgressRestricted
   */
  public function setNetworkEgressRestricted($networkEgressRestricted)
  {
    $this->networkEgressRestricted = $networkEgressRestricted;
  }
  /**
   * @return bool
   */
  public function getNetworkEgressRestricted()
  {
    return $this->networkEgressRestricted;
  }
  /**
   * Optional. Configuration for the Portals settings.
   *
   * @param bool $portalDisabled
   */
  public function setPortalDisabled($portalDisabled)
  {
    $this->portalDisabled = $portalDisabled;
  }
  /**
   * @return bool
   */
  public function getPortalDisabled()
  {
    return $this->portalDisabled;
  }
  /**
   * Output only. Project ID associated with the Apigee organization.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Optional. Properties defined in the Apigee organization profile.
   *
   * @param GoogleCloudApigeeV1Properties $properties
   */
  public function setProperties(GoogleCloudApigeeV1Properties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudApigeeV1Properties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Optional. Cloud KMS key name used for encrypting the data that is stored
   * and replicated across runtime instances. Update is not allowed after the
   * organization is created. If not specified or [RuntimeType](#RuntimeType) is
   * `TRIAL`, a Google-Managed encryption key will be used. For example:
   * "projects/foo/locations/us/keyRings/bar/cryptoKeys/baz". **Note:** Not
   * supported for Apigee hybrid.
   *
   * @param string $runtimeDatabaseEncryptionKeyName
   */
  public function setRuntimeDatabaseEncryptionKeyName($runtimeDatabaseEncryptionKeyName)
  {
    $this->runtimeDatabaseEncryptionKeyName = $runtimeDatabaseEncryptionKeyName;
  }
  /**
   * @return string
   */
  public function getRuntimeDatabaseEncryptionKeyName()
  {
    return $this->runtimeDatabaseEncryptionKeyName;
  }
  /**
   * Required. Runtime type of the Apigee organization based on the Apigee
   * subscription purchased.
   *
   * Accepted values: RUNTIME_TYPE_UNSPECIFIED, CLOUD, HYBRID
   *
   * @param self::RUNTIME_TYPE_* $runtimeType
   */
  public function setRuntimeType($runtimeType)
  {
    $this->runtimeType = $runtimeType;
  }
  /**
   * @return self::RUNTIME_TYPE_*
   */
  public function getRuntimeType()
  {
    return $this->runtimeType;
  }
  /**
   * Output only. State of the organization. Values other than ACTIVE means the
   * resource is not ready to use.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, UPDATING
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
   * Output only. Subscription plan that the customer has purchased. Output
   * only.
   *
   * Accepted values: SUBSCRIPTION_PLAN_UNSPECIFIED, SUBSCRIPTION_2021,
   * SUBSCRIPTION_2024
   *
   * @param self::SUBSCRIPTION_PLAN_* $subscriptionPlan
   */
  public function setSubscriptionPlan($subscriptionPlan)
  {
    $this->subscriptionPlan = $subscriptionPlan;
  }
  /**
   * @return self::SUBSCRIPTION_PLAN_*
   */
  public function getSubscriptionPlan()
  {
    return $this->subscriptionPlan;
  }
  /**
   * Output only. DEPRECATED: This will eventually be replaced by BillingType.
   * Subscription type of the Apigee organization. Valid values include trial
   * (free, limited, and for evaluation purposes only) or paid (full
   * subscription has been purchased). See [Apigee
   * pricing](https://cloud.google.com/apigee/pricing/).
   *
   * Accepted values: SUBSCRIPTION_TYPE_UNSPECIFIED, PAID, TRIAL
   *
   * @deprecated
   * @param self::SUBSCRIPTION_TYPE_* $subscriptionType
   */
  public function setSubscriptionType($subscriptionType)
  {
    $this->subscriptionType = $subscriptionType;
  }
  /**
   * @deprecated
   * @return self::SUBSCRIPTION_TYPE_*
   */
  public function getSubscriptionType()
  {
    return $this->subscriptionType;
  }
  /**
   * Not used by Apigee.
   *
   * Accepted values: TYPE_UNSPECIFIED, TYPE_TRIAL, TYPE_PAID, TYPE_INTERNAL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Organization::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Organization');
