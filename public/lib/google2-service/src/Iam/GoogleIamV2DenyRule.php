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

namespace Google\Service\Iam;

class GoogleIamV2DenyRule extends \Google\Collection
{
  protected $collection_key = 'exceptionPrincipals';
  protected $denialConditionType = GoogleTypeExpr::class;
  protected $denialConditionDataType = '';
  /**
   * The permissions that are explicitly denied by this rule. Each permission
   * uses the format `{service_fqdn}/{resource}.{verb}`, where `{service_fqdn}`
   * is the fully qualified domain name for the service. For example,
   * `iam.googleapis.com/roles.list`.
   *
   * @var string[]
   */
  public $deniedPermissions;
  /**
   * The identities that are prevented from using one or more permissions on
   * Google Cloud resources. This field can contain the following values: *
   * `principal://goog/subject/{email_id}`: A specific Google Account. Includes
   * Gmail, Cloud Identity, and Google Workspace user accounts. For example,
   * `principal://goog/subject/alice@example.com`. * `principal://iam.googleapis
   * .com/projects/-/serviceAccounts/{service_account_id}`: A Google Cloud
   * service account. For example,
   * `principal://iam.googleapis.com/projects/-/serviceAccounts/my-service-
   * account@iam.gserviceaccount.com`. * `principalSet://goog/group/{group_id}`:
   * A Google group. For example,
   * `principalSet://goog/group/admins@example.com`. *
   * `principalSet://goog/public:all`: A special identifier that represents any
   * principal that is on the internet, even if they do not have a Google
   * Account or are not logged in. *
   * `principalSet://goog/cloudIdentityCustomerId/{customer_id}`: All of the
   * principals associated with the specified Google Workspace or Cloud Identity
   * customer ID. For example,
   * `principalSet://goog/cloudIdentityCustomerId/C01Abc35`. * `principal://iam.
   * googleapis.com/locations/global/workforcePools/{pool_id}/subject/{subject_a
   * ttribute_value}`: A single identity in a workforce identity pool. * `princi
   * palSet://iam.googleapis.com/locations/global/workforcePools/{pool_id}/group
   * /{group_id}`: All workforce identities in a group. * `principalSet://iam.go
   * ogleapis.com/locations/global/workforcePools/{pool_id}/attribute.{attribute
   * _name}/{attribute_value}`: All workforce identities with a specific
   * attribute value. * `principalSet://iam.googleapis.com/locations/global/work
   * forcePools/{pool_id}`: All identities in a workforce identity pool. * `prin
   * cipal://iam.googleapis.com/projects/{project_number}/locations/global/workl
   * oadIdentityPools/{pool_id}/subject/{subject_attribute_value}`: A single
   * identity in a workload identity pool. * `principalSet://iam.googleapis.com/
   * projects/{project_number}/locations/global/workloadIdentityPools/{pool_id}/
   * group/{group_id}`: A workload identity pool group. * `principalSet://iam.go
   * ogleapis.com/projects/{project_number}/locations/global/workloadIdentityPoo
   * ls/{pool_id}/attribute.{attribute_name}/{attribute_value}`: All identities
   * in a workload identity pool with a certain attribute. * `principalSet://iam
   * .googleapis.com/projects/{project_number}/locations/global/workloadIdentity
   * Pools/{pool_id}`: All identities in a workload identity pool. * `principalS
   * et://cloudresourcemanager.googleapis.com/[projects|folders|organizations]/{
   * project_number|folder_number|org_number}/type/ServiceAccount`: All service
   * accounts grouped under a resource (project, folder, or organization). * `pr
   * incipalSet://cloudresourcemanager.googleapis.com/[projects|folders|organiza
   * tions]/{project_number|folder_number|org_number}/type/ServiceAgent`: All
   * service agents grouped under a resource (project, folder, or organization).
   * * `deleted:principal://goog/subject/{email_id}?uid={uid}`: A specific
   * Google Account that was deleted recently. For example,
   * `deleted:principal://goog/subject/alice@example.com?uid=1234567890`. If the
   * Google Account is recovered, this identifier reverts to the standard
   * identifier for a Google Account. *
   * `deleted:principalSet://goog/group/{group_id}?uid={uid}`: A Google group
   * that was deleted recently. For example,
   * `deleted:principalSet://goog/group/admins@example.com?uid=1234567890`. If
   * the Google group is restored, this identifier reverts to the standard
   * identifier for a Google group. * `deleted:principal://iam.googleapis.com/pr
   * ojects/-/serviceAccounts/{service_account_id}?uid={uid}`: A Google Cloud
   * service account that was deleted recently. For example,
   * `deleted:principal://iam.googleapis.com/projects/-/serviceAccounts/my-
   * service-account@iam.gserviceaccount.com?uid=1234567890`. If the service
   * account is undeleted, this identifier reverts to the standard identifier
   * for a service account. * `deleted:principal://iam.googleapis.com/locations/
   * global/workforcePools/{pool_id}/subject/{subject_attribute_value}`: Deleted
   * single identity in a workforce identity pool. For example,
   * `deleted:principal://iam.googleapis.com/locations/global/workforcePools/my-
   * pool-id/subject/my-subject-attribute-value`.
   *
   * @var string[]
   */
  public $deniedPrincipals;
  /**
   * Specifies the permissions that this rule excludes from the set of denied
   * permissions given by `denied_permissions`. If a permission appears in
   * `denied_permissions` _and_ in `exception_permissions` then it will _not_ be
   * denied. The excluded permissions can be specified using the same syntax as
   * `denied_permissions`.
   *
   * @var string[]
   */
  public $exceptionPermissions;
  /**
   * The identities that are excluded from the deny rule, even if they are
   * listed in the `denied_principals`. For example, you could add a Google
   * group to the `denied_principals`, then exclude specific users who belong to
   * that group. This field can contain the same values as the
   * `denied_principals` field, excluding `principalSet://goog/public:all`,
   * which represents all users on the internet.
   *
   * @var string[]
   */
  public $exceptionPrincipals;

  /**
   * The condition that determines whether this deny rule applies to a request.
   * If the condition expression evaluates to `true`, then the deny rule is
   * applied; otherwise, the deny rule is not applied. Each deny rule is
   * evaluated independently. If this deny rule does not apply to a request,
   * other deny rules might still apply. The condition can use CEL functions
   * that evaluate [resource
   * tags](https://cloud.google.com/iam/help/conditions/resource-tags). Other
   * functions and operators are not supported.
   *
   * @param GoogleTypeExpr $denialCondition
   */
  public function setDenialCondition(GoogleTypeExpr $denialCondition)
  {
    $this->denialCondition = $denialCondition;
  }
  /**
   * @return GoogleTypeExpr
   */
  public function getDenialCondition()
  {
    return $this->denialCondition;
  }
  /**
   * The permissions that are explicitly denied by this rule. Each permission
   * uses the format `{service_fqdn}/{resource}.{verb}`, where `{service_fqdn}`
   * is the fully qualified domain name for the service. For example,
   * `iam.googleapis.com/roles.list`.
   *
   * @param string[] $deniedPermissions
   */
  public function setDeniedPermissions($deniedPermissions)
  {
    $this->deniedPermissions = $deniedPermissions;
  }
  /**
   * @return string[]
   */
  public function getDeniedPermissions()
  {
    return $this->deniedPermissions;
  }
  /**
   * The identities that are prevented from using one or more permissions on
   * Google Cloud resources. This field can contain the following values: *
   * `principal://goog/subject/{email_id}`: A specific Google Account. Includes
   * Gmail, Cloud Identity, and Google Workspace user accounts. For example,
   * `principal://goog/subject/alice@example.com`. * `principal://iam.googleapis
   * .com/projects/-/serviceAccounts/{service_account_id}`: A Google Cloud
   * service account. For example,
   * `principal://iam.googleapis.com/projects/-/serviceAccounts/my-service-
   * account@iam.gserviceaccount.com`. * `principalSet://goog/group/{group_id}`:
   * A Google group. For example,
   * `principalSet://goog/group/admins@example.com`. *
   * `principalSet://goog/public:all`: A special identifier that represents any
   * principal that is on the internet, even if they do not have a Google
   * Account or are not logged in. *
   * `principalSet://goog/cloudIdentityCustomerId/{customer_id}`: All of the
   * principals associated with the specified Google Workspace or Cloud Identity
   * customer ID. For example,
   * `principalSet://goog/cloudIdentityCustomerId/C01Abc35`. * `principal://iam.
   * googleapis.com/locations/global/workforcePools/{pool_id}/subject/{subject_a
   * ttribute_value}`: A single identity in a workforce identity pool. * `princi
   * palSet://iam.googleapis.com/locations/global/workforcePools/{pool_id}/group
   * /{group_id}`: All workforce identities in a group. * `principalSet://iam.go
   * ogleapis.com/locations/global/workforcePools/{pool_id}/attribute.{attribute
   * _name}/{attribute_value}`: All workforce identities with a specific
   * attribute value. * `principalSet://iam.googleapis.com/locations/global/work
   * forcePools/{pool_id}`: All identities in a workforce identity pool. * `prin
   * cipal://iam.googleapis.com/projects/{project_number}/locations/global/workl
   * oadIdentityPools/{pool_id}/subject/{subject_attribute_value}`: A single
   * identity in a workload identity pool. * `principalSet://iam.googleapis.com/
   * projects/{project_number}/locations/global/workloadIdentityPools/{pool_id}/
   * group/{group_id}`: A workload identity pool group. * `principalSet://iam.go
   * ogleapis.com/projects/{project_number}/locations/global/workloadIdentityPoo
   * ls/{pool_id}/attribute.{attribute_name}/{attribute_value}`: All identities
   * in a workload identity pool with a certain attribute. * `principalSet://iam
   * .googleapis.com/projects/{project_number}/locations/global/workloadIdentity
   * Pools/{pool_id}`: All identities in a workload identity pool. * `principalS
   * et://cloudresourcemanager.googleapis.com/[projects|folders|organizations]/{
   * project_number|folder_number|org_number}/type/ServiceAccount`: All service
   * accounts grouped under a resource (project, folder, or organization). * `pr
   * incipalSet://cloudresourcemanager.googleapis.com/[projects|folders|organiza
   * tions]/{project_number|folder_number|org_number}/type/ServiceAgent`: All
   * service agents grouped under a resource (project, folder, or organization).
   * * `deleted:principal://goog/subject/{email_id}?uid={uid}`: A specific
   * Google Account that was deleted recently. For example,
   * `deleted:principal://goog/subject/alice@example.com?uid=1234567890`. If the
   * Google Account is recovered, this identifier reverts to the standard
   * identifier for a Google Account. *
   * `deleted:principalSet://goog/group/{group_id}?uid={uid}`: A Google group
   * that was deleted recently. For example,
   * `deleted:principalSet://goog/group/admins@example.com?uid=1234567890`. If
   * the Google group is restored, this identifier reverts to the standard
   * identifier for a Google group. * `deleted:principal://iam.googleapis.com/pr
   * ojects/-/serviceAccounts/{service_account_id}?uid={uid}`: A Google Cloud
   * service account that was deleted recently. For example,
   * `deleted:principal://iam.googleapis.com/projects/-/serviceAccounts/my-
   * service-account@iam.gserviceaccount.com?uid=1234567890`. If the service
   * account is undeleted, this identifier reverts to the standard identifier
   * for a service account. * `deleted:principal://iam.googleapis.com/locations/
   * global/workforcePools/{pool_id}/subject/{subject_attribute_value}`: Deleted
   * single identity in a workforce identity pool. For example,
   * `deleted:principal://iam.googleapis.com/locations/global/workforcePools/my-
   * pool-id/subject/my-subject-attribute-value`.
   *
   * @param string[] $deniedPrincipals
   */
  public function setDeniedPrincipals($deniedPrincipals)
  {
    $this->deniedPrincipals = $deniedPrincipals;
  }
  /**
   * @return string[]
   */
  public function getDeniedPrincipals()
  {
    return $this->deniedPrincipals;
  }
  /**
   * Specifies the permissions that this rule excludes from the set of denied
   * permissions given by `denied_permissions`. If a permission appears in
   * `denied_permissions` _and_ in `exception_permissions` then it will _not_ be
   * denied. The excluded permissions can be specified using the same syntax as
   * `denied_permissions`.
   *
   * @param string[] $exceptionPermissions
   */
  public function setExceptionPermissions($exceptionPermissions)
  {
    $this->exceptionPermissions = $exceptionPermissions;
  }
  /**
   * @return string[]
   */
  public function getExceptionPermissions()
  {
    return $this->exceptionPermissions;
  }
  /**
   * The identities that are excluded from the deny rule, even if they are
   * listed in the `denied_principals`. For example, you could add a Google
   * group to the `denied_principals`, then exclude specific users who belong to
   * that group. This field can contain the same values as the
   * `denied_principals` field, excluding `principalSet://goog/public:all`,
   * which represents all users on the internet.
   *
   * @param string[] $exceptionPrincipals
   */
  public function setExceptionPrincipals($exceptionPrincipals)
  {
    $this->exceptionPrincipals = $exceptionPrincipals;
  }
  /**
   * @return string[]
   */
  public function getExceptionPrincipals()
  {
    return $this->exceptionPrincipals;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIamV2DenyRule::class, 'Google_Service_Iam_GoogleIamV2DenyRule');
