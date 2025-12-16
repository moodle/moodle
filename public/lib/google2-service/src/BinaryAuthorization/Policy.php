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

namespace Google\Service\BinaryAuthorization;

class Policy extends \Google\Collection
{
  /**
   * Not specified: `DISABLE` is assumed.
   */
  public const GLOBAL_POLICY_EVALUATION_MODE_GLOBAL_POLICY_EVALUATION_MODE_UNSPECIFIED = 'GLOBAL_POLICY_EVALUATION_MODE_UNSPECIFIED';
  /**
   * Enables system policy evaluation.
   */
  public const GLOBAL_POLICY_EVALUATION_MODE_ENABLE = 'ENABLE';
  /**
   * Disables system policy evaluation.
   */
  public const GLOBAL_POLICY_EVALUATION_MODE_DISABLE = 'DISABLE';
  protected $collection_key = 'admissionWhitelistPatterns';
  protected $admissionWhitelistPatternsType = AdmissionWhitelistPattern::class;
  protected $admissionWhitelistPatternsDataType = 'array';
  protected $clusterAdmissionRulesType = AdmissionRule::class;
  protected $clusterAdmissionRulesDataType = 'map';
  protected $defaultAdmissionRuleType = AdmissionRule::class;
  protected $defaultAdmissionRuleDataType = '';
  /**
   * Optional. A descriptive comment.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. A checksum, returned by the server, that can be sent on update
   * requests to ensure the policy has an up-to-date value before attempting to
   * update it. See https://google.aip.dev/154.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Controls the evaluation of a Google-maintained global admission
   * policy for common system-level images. Images not covered by the global
   * policy will be subject to the project admission policy. This setting has no
   * effect when specified inside a global admission policy.
   *
   * @var string
   */
  public $globalPolicyEvaluationMode;
  protected $istioServiceIdentityAdmissionRulesType = AdmissionRule::class;
  protected $istioServiceIdentityAdmissionRulesDataType = 'map';
  protected $kubernetesNamespaceAdmissionRulesType = AdmissionRule::class;
  protected $kubernetesNamespaceAdmissionRulesDataType = 'map';
  protected $kubernetesServiceAccountAdmissionRulesType = AdmissionRule::class;
  protected $kubernetesServiceAccountAdmissionRulesDataType = 'map';
  /**
   * Output only. The resource name, in the format `projects/policy`. There is
   * at most one policy per project.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Time when the policy was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Admission policy allowlisting. A matching admission request will
   * always be permitted. This feature is typically used to exclude Google or
   * third-party infrastructure images from Binary Authorization policies.
   *
   * @param AdmissionWhitelistPattern[] $admissionWhitelistPatterns
   */
  public function setAdmissionWhitelistPatterns($admissionWhitelistPatterns)
  {
    $this->admissionWhitelistPatterns = $admissionWhitelistPatterns;
  }
  /**
   * @return AdmissionWhitelistPattern[]
   */
  public function getAdmissionWhitelistPatterns()
  {
    return $this->admissionWhitelistPatterns;
  }
  /**
   * Optional. A valid policy has only one of the following rule maps non-empty,
   * i.e. only one of `cluster_admission_rules`,
   * `kubernetes_namespace_admission_rules`,
   * `kubernetes_service_account_admission_rules`, or
   * `istio_service_identity_admission_rules` can be non-empty. Per-cluster
   * admission rules. Cluster spec format: `location.clusterId`. There can be at
   * most one admission rule per cluster spec. A `location` is either a compute
   * zone (e.g. us-central1-a) or a region (e.g. us-central1). For `clusterId`
   * syntax restrictions see https://cloud.google.com/container-
   * engine/reference/rest/v1/projects.zones.clusters.
   *
   * @param AdmissionRule[] $clusterAdmissionRules
   */
  public function setClusterAdmissionRules($clusterAdmissionRules)
  {
    $this->clusterAdmissionRules = $clusterAdmissionRules;
  }
  /**
   * @return AdmissionRule[]
   */
  public function getClusterAdmissionRules()
  {
    return $this->clusterAdmissionRules;
  }
  /**
   * Required. Default admission rule for a cluster without a per-cluster, per-
   * kubernetes-service-account, or per-istio-service-identity admission rule.
   *
   * @param AdmissionRule $defaultAdmissionRule
   */
  public function setDefaultAdmissionRule(AdmissionRule $defaultAdmissionRule)
  {
    $this->defaultAdmissionRule = $defaultAdmissionRule;
  }
  /**
   * @return AdmissionRule
   */
  public function getDefaultAdmissionRule()
  {
    return $this->defaultAdmissionRule;
  }
  /**
   * Optional. A descriptive comment.
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
   * Optional. A checksum, returned by the server, that can be sent on update
   * requests to ensure the policy has an up-to-date value before attempting to
   * update it. See https://google.aip.dev/154.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Controls the evaluation of a Google-maintained global admission
   * policy for common system-level images. Images not covered by the global
   * policy will be subject to the project admission policy. This setting has no
   * effect when specified inside a global admission policy.
   *
   * Accepted values: GLOBAL_POLICY_EVALUATION_MODE_UNSPECIFIED, ENABLE, DISABLE
   *
   * @param self::GLOBAL_POLICY_EVALUATION_MODE_* $globalPolicyEvaluationMode
   */
  public function setGlobalPolicyEvaluationMode($globalPolicyEvaluationMode)
  {
    $this->globalPolicyEvaluationMode = $globalPolicyEvaluationMode;
  }
  /**
   * @return self::GLOBAL_POLICY_EVALUATION_MODE_*
   */
  public function getGlobalPolicyEvaluationMode()
  {
    return $this->globalPolicyEvaluationMode;
  }
  /**
   * Optional. Per-istio-service-identity admission rules. Istio service
   * identity spec format: `spiffe:ns//sa/` or `/ns//sa/` e.g.
   * `spiffe://example.com/ns/test-ns/sa/default`
   *
   * @param AdmissionRule[] $istioServiceIdentityAdmissionRules
   */
  public function setIstioServiceIdentityAdmissionRules($istioServiceIdentityAdmissionRules)
  {
    $this->istioServiceIdentityAdmissionRules = $istioServiceIdentityAdmissionRules;
  }
  /**
   * @return AdmissionRule[]
   */
  public function getIstioServiceIdentityAdmissionRules()
  {
    return $this->istioServiceIdentityAdmissionRules;
  }
  /**
   * Optional. Per-kubernetes-namespace admission rules. K8s namespace spec
   * format: `[a-z.-]+`, e.g. `some-namespace`
   *
   * @param AdmissionRule[] $kubernetesNamespaceAdmissionRules
   */
  public function setKubernetesNamespaceAdmissionRules($kubernetesNamespaceAdmissionRules)
  {
    $this->kubernetesNamespaceAdmissionRules = $kubernetesNamespaceAdmissionRules;
  }
  /**
   * @return AdmissionRule[]
   */
  public function getKubernetesNamespaceAdmissionRules()
  {
    return $this->kubernetesNamespaceAdmissionRules;
  }
  /**
   * Optional. Per-kubernetes-service-account admission rules. Service account
   * spec format: `namespace:serviceaccount`. e.g. `test-ns:default`
   *
   * @param AdmissionRule[] $kubernetesServiceAccountAdmissionRules
   */
  public function setKubernetesServiceAccountAdmissionRules($kubernetesServiceAccountAdmissionRules)
  {
    $this->kubernetesServiceAccountAdmissionRules = $kubernetesServiceAccountAdmissionRules;
  }
  /**
   * @return AdmissionRule[]
   */
  public function getKubernetesServiceAccountAdmissionRules()
  {
    return $this->kubernetesServiceAccountAdmissionRules;
  }
  /**
   * Output only. The resource name, in the format `projects/policy`. There is
   * at most one policy per project.
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
   * Output only. Time when the policy was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policy::class, 'Google_Service_BinaryAuthorization_Policy');
