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

namespace Google\Service\BigQueryDataPolicyService;

class Binding extends \Google\Collection
{
  protected $collection_key = 'members';
  protected $conditionType = Expr::class;
  protected $conditionDataType = '';
  /**
   * Specifies the principals requesting access for a Google Cloud resource.
   * `members` can have the following values: * `allUsers`: A special identifier
   * that represents anyone who is on the internet; with or without a Google
   * account. * `allAuthenticatedUsers`: A special identifier that represents
   * anyone who is authenticated with a Google account or a service account.
   * Does not include identities that come from external identity providers
   * (IdPs) through identity federation. * `user:{emailid}`: An email address
   * that represents a specific Google account. For example, `alice@example.com`
   * . * `serviceAccount:{emailid}`: An email address that represents a Google
   * service account. For example, `my-other-app@appspot.gserviceaccount.com`. *
   * `serviceAccount:{projectid}.svc.id.goog[{namespace}/{kubernetes-sa}]`: An
   * identifier for a [Kubernetes service
   * account](https://cloud.google.com/kubernetes-engine/docs/how-to/kubernetes-
   * service-accounts). For example, `my-project.svc.id.goog[my-namespace/my-
   * kubernetes-sa]`. * `group:{emailid}`: An email address that represents a
   * Google group. For example, `admins@example.com`. * `domain:{domain}`: The G
   * Suite domain (primary) that represents all the users of that domain. For
   * example, `google.com` or `example.com`. * `principal://iam.googleapis.com/l
   * ocations/global/workforcePools/{pool_id}/subject/{subject_attribute_value}`
   * : A single identity in a workforce identity pool. * `principalSet://iam.goo
   * gleapis.com/locations/global/workforcePools/{pool_id}/group/{group_id}`:
   * All workforce identities in a group. * `principalSet://iam.googleapis.com/l
   * ocations/global/workforcePools/{pool_id}/attribute.{attribute_name}/{attrib
   * ute_value}`: All workforce identities with a specific attribute value. * `p
   * rincipalSet://iam.googleapis.com/locations/global/workforcePools/{pool_id}`
   * : All identities in a workforce identity pool. * `principal://iam.googleapi
   * s.com/projects/{project_number}/locations/global/workloadIdentityPools/{poo
   * l_id}/subject/{subject_attribute_value}`: A single identity in a workload
   * identity pool. * `principalSet://iam.googleapis.com/projects/{project_numbe
   * r}/locations/global/workloadIdentityPools/{pool_id}/group/{group_id}`: A
   * workload identity pool group. * `principalSet://iam.googleapis.com/projects
   * /{project_number}/locations/global/workloadIdentityPools/{pool_id}/attribut
   * e.{attribute_name}/{attribute_value}`: All identities in a workload
   * identity pool with a certain attribute. * `principalSet://iam.googleapis.co
   * m/projects/{project_number}/locations/global/workloadIdentityPools/{pool_id
   * }`: All identities in a workload identity pool. *
   * `deleted:user:{emailid}?uid={uniqueid}`: An email address (plus unique
   * identifier) representing a user that has been recently deleted. For
   * example, `alice@example.com?uid=123456789012345678901`. If the user is
   * recovered, this value reverts to `user:{emailid}` and the recovered user
   * retains the role in the binding. *
   * `deleted:serviceAccount:{emailid}?uid={uniqueid}`: An email address (plus
   * unique identifier) representing a service account that has been recently
   * deleted. For example, `my-other-
   * app@appspot.gserviceaccount.com?uid=123456789012345678901`. If the service
   * account is undeleted, this value reverts to `serviceAccount:{emailid}` and
   * the undeleted service account retains the role in the binding. *
   * `deleted:group:{emailid}?uid={uniqueid}`: An email address (plus unique
   * identifier) representing a Google group that has been recently deleted. For
   * example, `admins@example.com?uid=123456789012345678901`. If the group is
   * recovered, this value reverts to `group:{emailid}` and the recovered group
   * retains the role in the binding. * `deleted:principal://iam.googleapis.com/
   * locations/global/workforcePools/{pool_id}/subject/{subject_attribute_value}
   * `: Deleted single identity in a workforce identity pool. For example,
   * `deleted:principal://iam.googleapis.com/locations/global/workforcePools/my-
   * pool-id/subject/my-subject-attribute-value`.
   *
   * @var string[]
   */
  public $members;
  /**
   * Role that is assigned to the list of `members`, or principals. For example,
   * `roles/viewer`, `roles/editor`, or `roles/owner`. For an overview of the
   * IAM roles and permissions, see the [IAM
   * documentation](https://cloud.google.com/iam/docs/roles-overview). For a
   * list of the available pre-defined roles, see
   * [here](https://cloud.google.com/iam/docs/understanding-roles).
   *
   * @var string
   */
  public $role;

  /**
   * The condition that is associated with this binding. If the condition
   * evaluates to `true`, then this binding applies to the current request. If
   * the condition evaluates to `false`, then this binding does not apply to the
   * current request. However, a different role binding might grant the same
   * role to one or more of the principals in this binding. To learn which
   * resources support conditions in their IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   *
   * @param Expr $condition
   */
  public function setCondition(Expr $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return Expr
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Specifies the principals requesting access for a Google Cloud resource.
   * `members` can have the following values: * `allUsers`: A special identifier
   * that represents anyone who is on the internet; with or without a Google
   * account. * `allAuthenticatedUsers`: A special identifier that represents
   * anyone who is authenticated with a Google account or a service account.
   * Does not include identities that come from external identity providers
   * (IdPs) through identity federation. * `user:{emailid}`: An email address
   * that represents a specific Google account. For example, `alice@example.com`
   * . * `serviceAccount:{emailid}`: An email address that represents a Google
   * service account. For example, `my-other-app@appspot.gserviceaccount.com`. *
   * `serviceAccount:{projectid}.svc.id.goog[{namespace}/{kubernetes-sa}]`: An
   * identifier for a [Kubernetes service
   * account](https://cloud.google.com/kubernetes-engine/docs/how-to/kubernetes-
   * service-accounts). For example, `my-project.svc.id.goog[my-namespace/my-
   * kubernetes-sa]`. * `group:{emailid}`: An email address that represents a
   * Google group. For example, `admins@example.com`. * `domain:{domain}`: The G
   * Suite domain (primary) that represents all the users of that domain. For
   * example, `google.com` or `example.com`. * `principal://iam.googleapis.com/l
   * ocations/global/workforcePools/{pool_id}/subject/{subject_attribute_value}`
   * : A single identity in a workforce identity pool. * `principalSet://iam.goo
   * gleapis.com/locations/global/workforcePools/{pool_id}/group/{group_id}`:
   * All workforce identities in a group. * `principalSet://iam.googleapis.com/l
   * ocations/global/workforcePools/{pool_id}/attribute.{attribute_name}/{attrib
   * ute_value}`: All workforce identities with a specific attribute value. * `p
   * rincipalSet://iam.googleapis.com/locations/global/workforcePools/{pool_id}`
   * : All identities in a workforce identity pool. * `principal://iam.googleapi
   * s.com/projects/{project_number}/locations/global/workloadIdentityPools/{poo
   * l_id}/subject/{subject_attribute_value}`: A single identity in a workload
   * identity pool. * `principalSet://iam.googleapis.com/projects/{project_numbe
   * r}/locations/global/workloadIdentityPools/{pool_id}/group/{group_id}`: A
   * workload identity pool group. * `principalSet://iam.googleapis.com/projects
   * /{project_number}/locations/global/workloadIdentityPools/{pool_id}/attribut
   * e.{attribute_name}/{attribute_value}`: All identities in a workload
   * identity pool with a certain attribute. * `principalSet://iam.googleapis.co
   * m/projects/{project_number}/locations/global/workloadIdentityPools/{pool_id
   * }`: All identities in a workload identity pool. *
   * `deleted:user:{emailid}?uid={uniqueid}`: An email address (plus unique
   * identifier) representing a user that has been recently deleted. For
   * example, `alice@example.com?uid=123456789012345678901`. If the user is
   * recovered, this value reverts to `user:{emailid}` and the recovered user
   * retains the role in the binding. *
   * `deleted:serviceAccount:{emailid}?uid={uniqueid}`: An email address (plus
   * unique identifier) representing a service account that has been recently
   * deleted. For example, `my-other-
   * app@appspot.gserviceaccount.com?uid=123456789012345678901`. If the service
   * account is undeleted, this value reverts to `serviceAccount:{emailid}` and
   * the undeleted service account retains the role in the binding. *
   * `deleted:group:{emailid}?uid={uniqueid}`: An email address (plus unique
   * identifier) representing a Google group that has been recently deleted. For
   * example, `admins@example.com?uid=123456789012345678901`. If the group is
   * recovered, this value reverts to `group:{emailid}` and the recovered group
   * retains the role in the binding. * `deleted:principal://iam.googleapis.com/
   * locations/global/workforcePools/{pool_id}/subject/{subject_attribute_value}
   * `: Deleted single identity in a workforce identity pool. For example,
   * `deleted:principal://iam.googleapis.com/locations/global/workforcePools/my-
   * pool-id/subject/my-subject-attribute-value`.
   *
   * @param string[] $members
   */
  public function setMembers($members)
  {
    $this->members = $members;
  }
  /**
   * @return string[]
   */
  public function getMembers()
  {
    return $this->members;
  }
  /**
   * Role that is assigned to the list of `members`, or principals. For example,
   * `roles/viewer`, `roles/editor`, or `roles/owner`. For an overview of the
   * IAM roles and permissions, see the [IAM
   * documentation](https://cloud.google.com/iam/docs/roles-overview). For a
   * list of the available pre-defined roles, see
   * [here](https://cloud.google.com/iam/docs/understanding-roles).
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Binding::class, 'Google_Service_BigQueryDataPolicyService_Binding');
