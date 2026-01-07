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

namespace Google\Service\AccessApproval;

class AccessApprovalSettings extends \Google\Collection
{
  /**
   * Default value, defaults to ORGANIZATION if not set. This value is not able
   * to be configured by the user, do not use.
   */
  public const REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_UNSPECIFIED = 'REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_UNSPECIFIED';
  /**
   * This is the widest scope possible. It means the customer has no scope
   * restriction when it comes to Access Approval requests.
   */
  public const REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_ORGANIZATION = 'ORGANIZATION';
  /**
   * Customer allows the scope of Access Approval requests as broad as the
   * Folder level.
   */
  public const REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_FOLDER = 'FOLDER';
  /**
   * Customer allows the scope of Access Approval requests as broad as the
   * Project level.
   */
  public const REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_PROJECT = 'PROJECT';
  protected $collection_key = 'notificationEmails';
  /**
   * The asymmetric crypto key version to use for signing approval requests.
   * Empty active_key_version indicates that a Google-managed key should be used
   * for signing. This property will be ignored if set by an ancestor of this
   * resource, and new non-empty values may not be set.
   *
   * @var string
   */
  public $activeKeyVersion;
  /**
   * Output only. This field is read only (not settable via
   * UpdateAccessApprovalSettings method). If the field is true, that indicates
   * that an ancestor of this Project or Folder has set active_key_version (this
   * field will always be unset for the organization since organizations do not
   * have ancestors).
   *
   * @var bool
   */
  public $ancestorHasActiveKeyVersion;
  protected $approvalPolicyType = CustomerApprovalApprovalPolicy::class;
  protected $approvalPolicyDataType = '';
  protected $effectiveApprovalPolicyType = CustomerApprovalApprovalPolicy::class;
  protected $effectiveApprovalPolicyDataType = '';
  /**
   * Output only. This field is read only (not settable via
   * UpdateAccessApprovalSettings method). If the field is true, that indicates
   * that at least one service is enrolled for Access Approval in one or more
   * ancestors of the Project or Folder (this field will always be unset for the
   * organization since organizations do not have ancestors).
   *
   * @var bool
   */
  public $enrolledAncestor;
  protected $enrolledServicesType = EnrolledService::class;
  protected $enrolledServicesDataType = 'array';
  /**
   * Output only. This field is read only (not settable via
   * UpdateAccessApprovalSettings method). If the field is true, that indicates
   * that there is some configuration issue with the active_key_version
   * configured at this level in the resource hierarchy (e.g. it doesn't exist
   * or the Access Approval service account doesn't have the correct permissions
   * on it, etc.) This key version is not necessarily the effective key version
   * at this level, as key versions are inherited top-down.
   *
   * @var bool
   */
  public $invalidKeyVersion;
  /**
   * The resource name of the settings. Format is one of: *
   * "projects/{project}/accessApprovalSettings" *
   * "folders/{folder}/accessApprovalSettings" *
   * "organizations/{organization}/accessApprovalSettings"
   *
   * @var string
   */
  public $name;
  /**
   * A list of email addresses to which notifications relating to approval
   * requests should be sent. Notifications relating to a resource will be sent
   * to all emails in the settings of ancestor resources of that resource. A
   * maximum of 50 email addresses are allowed.
   *
   * @var string[]
   */
  public $notificationEmails;
  /**
   * Optional. A pubsub topic that notifications relating to access approval are
   * published to. Notifications include pre-approved accesses.
   *
   * @var string
   */
  public $notificationPubsubTopic;
  /**
   * This field is used to set a preference for granularity of an access
   * approval request. If true, Google personnel will be asked to send resource-
   * level requests when possible. If false, Google personnel will be asked to
   * send requests at the project level.
   *
   * @var bool
   */
  public $preferNoBroadApprovalRequests;
  /**
   * Set the default access approval request expiration time. This value is able
   * to be set directly by the customer at the time of approval, overriding this
   * suggested value. We recommend setting this value to 30 days.
   *
   * @var int
   */
  public $preferredRequestExpirationDays;
  /**
   * Optional. A setting that indicates the maximum scope of an Access Approval
   * request: either organization, folder, or project. Google administrators
   * will be asked to send requests no broader than the configured scope.
   *
   * @var string
   */
  public $requestScopeMaxWidthPreference;
  /**
   * Optional. When enabled, Google will only be able to send approval requests
   * for access reasons with a customer accessible case ID in the reason detail.
   * Also known as "Require customer initiated support case justification"
   *
   * @var bool
   */
  public $requireCustomerVisibleJustification;

  /**
   * The asymmetric crypto key version to use for signing approval requests.
   * Empty active_key_version indicates that a Google-managed key should be used
   * for signing. This property will be ignored if set by an ancestor of this
   * resource, and new non-empty values may not be set.
   *
   * @param string $activeKeyVersion
   */
  public function setActiveKeyVersion($activeKeyVersion)
  {
    $this->activeKeyVersion = $activeKeyVersion;
  }
  /**
   * @return string
   */
  public function getActiveKeyVersion()
  {
    return $this->activeKeyVersion;
  }
  /**
   * Output only. This field is read only (not settable via
   * UpdateAccessApprovalSettings method). If the field is true, that indicates
   * that an ancestor of this Project or Folder has set active_key_version (this
   * field will always be unset for the organization since organizations do not
   * have ancestors).
   *
   * @param bool $ancestorHasActiveKeyVersion
   */
  public function setAncestorHasActiveKeyVersion($ancestorHasActiveKeyVersion)
  {
    $this->ancestorHasActiveKeyVersion = $ancestorHasActiveKeyVersion;
  }
  /**
   * @return bool
   */
  public function getAncestorHasActiveKeyVersion()
  {
    return $this->ancestorHasActiveKeyVersion;
  }
  /**
   * Optional. Policy configuration for Access Approval that sets the operating
   * mode. The available policies are Transparency, Streamlined Support, and
   * Approval Required.
   *
   * @param CustomerApprovalApprovalPolicy $approvalPolicy
   */
  public function setApprovalPolicy(CustomerApprovalApprovalPolicy $approvalPolicy)
  {
    $this->approvalPolicy = $approvalPolicy;
  }
  /**
   * @return CustomerApprovalApprovalPolicy
   */
  public function getApprovalPolicy()
  {
    return $this->approvalPolicy;
  }
  /**
   * Output only. Effective policy applied for Access Approval, inclusive of
   * inheritance.
   *
   * @param CustomerApprovalApprovalPolicy $effectiveApprovalPolicy
   */
  public function setEffectiveApprovalPolicy(CustomerApprovalApprovalPolicy $effectiveApprovalPolicy)
  {
    $this->effectiveApprovalPolicy = $effectiveApprovalPolicy;
  }
  /**
   * @return CustomerApprovalApprovalPolicy
   */
  public function getEffectiveApprovalPolicy()
  {
    return $this->effectiveApprovalPolicy;
  }
  /**
   * Output only. This field is read only (not settable via
   * UpdateAccessApprovalSettings method). If the field is true, that indicates
   * that at least one service is enrolled for Access Approval in one or more
   * ancestors of the Project or Folder (this field will always be unset for the
   * organization since organizations do not have ancestors).
   *
   * @param bool $enrolledAncestor
   */
  public function setEnrolledAncestor($enrolledAncestor)
  {
    $this->enrolledAncestor = $enrolledAncestor;
  }
  /**
   * @return bool
   */
  public function getEnrolledAncestor()
  {
    return $this->enrolledAncestor;
  }
  /**
   * A list of Google Cloud Services for which the given resource has Access
   * Approval enrolled. Access requests for the resource given by name against
   * any of these services contained here will be required to have explicit
   * approval. If name refers to an organization, enrollment can be done for
   * individual services. If name refers to a folder or project, enrollment can
   * only be done on an all or nothing basis. If a cloud_product is repeated in
   * this list, the first entry will be honored and all following entries will
   * be discarded.
   *
   * @param EnrolledService[] $enrolledServices
   */
  public function setEnrolledServices($enrolledServices)
  {
    $this->enrolledServices = $enrolledServices;
  }
  /**
   * @return EnrolledService[]
   */
  public function getEnrolledServices()
  {
    return $this->enrolledServices;
  }
  /**
   * Output only. This field is read only (not settable via
   * UpdateAccessApprovalSettings method). If the field is true, that indicates
   * that there is some configuration issue with the active_key_version
   * configured at this level in the resource hierarchy (e.g. it doesn't exist
   * or the Access Approval service account doesn't have the correct permissions
   * on it, etc.) This key version is not necessarily the effective key version
   * at this level, as key versions are inherited top-down.
   *
   * @param bool $invalidKeyVersion
   */
  public function setInvalidKeyVersion($invalidKeyVersion)
  {
    $this->invalidKeyVersion = $invalidKeyVersion;
  }
  /**
   * @return bool
   */
  public function getInvalidKeyVersion()
  {
    return $this->invalidKeyVersion;
  }
  /**
   * The resource name of the settings. Format is one of: *
   * "projects/{project}/accessApprovalSettings" *
   * "folders/{folder}/accessApprovalSettings" *
   * "organizations/{organization}/accessApprovalSettings"
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
   * A list of email addresses to which notifications relating to approval
   * requests should be sent. Notifications relating to a resource will be sent
   * to all emails in the settings of ancestor resources of that resource. A
   * maximum of 50 email addresses are allowed.
   *
   * @param string[] $notificationEmails
   */
  public function setNotificationEmails($notificationEmails)
  {
    $this->notificationEmails = $notificationEmails;
  }
  /**
   * @return string[]
   */
  public function getNotificationEmails()
  {
    return $this->notificationEmails;
  }
  /**
   * Optional. A pubsub topic that notifications relating to access approval are
   * published to. Notifications include pre-approved accesses.
   *
   * @param string $notificationPubsubTopic
   */
  public function setNotificationPubsubTopic($notificationPubsubTopic)
  {
    $this->notificationPubsubTopic = $notificationPubsubTopic;
  }
  /**
   * @return string
   */
  public function getNotificationPubsubTopic()
  {
    return $this->notificationPubsubTopic;
  }
  /**
   * This field is used to set a preference for granularity of an access
   * approval request. If true, Google personnel will be asked to send resource-
   * level requests when possible. If false, Google personnel will be asked to
   * send requests at the project level.
   *
   * @param bool $preferNoBroadApprovalRequests
   */
  public function setPreferNoBroadApprovalRequests($preferNoBroadApprovalRequests)
  {
    $this->preferNoBroadApprovalRequests = $preferNoBroadApprovalRequests;
  }
  /**
   * @return bool
   */
  public function getPreferNoBroadApprovalRequests()
  {
    return $this->preferNoBroadApprovalRequests;
  }
  /**
   * Set the default access approval request expiration time. This value is able
   * to be set directly by the customer at the time of approval, overriding this
   * suggested value. We recommend setting this value to 30 days.
   *
   * @param int $preferredRequestExpirationDays
   */
  public function setPreferredRequestExpirationDays($preferredRequestExpirationDays)
  {
    $this->preferredRequestExpirationDays = $preferredRequestExpirationDays;
  }
  /**
   * @return int
   */
  public function getPreferredRequestExpirationDays()
  {
    return $this->preferredRequestExpirationDays;
  }
  /**
   * Optional. A setting that indicates the maximum scope of an Access Approval
   * request: either organization, folder, or project. Google administrators
   * will be asked to send requests no broader than the configured scope.
   *
   * Accepted values: REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_UNSPECIFIED,
   * ORGANIZATION, FOLDER, PROJECT
   *
   * @param self::REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_* $requestScopeMaxWidthPreference
   */
  public function setRequestScopeMaxWidthPreference($requestScopeMaxWidthPreference)
  {
    $this->requestScopeMaxWidthPreference = $requestScopeMaxWidthPreference;
  }
  /**
   * @return self::REQUEST_SCOPE_MAX_WIDTH_PREFERENCE_*
   */
  public function getRequestScopeMaxWidthPreference()
  {
    return $this->requestScopeMaxWidthPreference;
  }
  /**
   * Optional. When enabled, Google will only be able to send approval requests
   * for access reasons with a customer accessible case ID in the reason detail.
   * Also known as "Require customer initiated support case justification"
   *
   * @param bool $requireCustomerVisibleJustification
   */
  public function setRequireCustomerVisibleJustification($requireCustomerVisibleJustification)
  {
    $this->requireCustomerVisibleJustification = $requireCustomerVisibleJustification;
  }
  /**
   * @return bool
   */
  public function getRequireCustomerVisibleJustification()
  {
    return $this->requireCustomerVisibleJustification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessApprovalSettings::class, 'Google_Service_AccessApproval_AccessApprovalSettings');
