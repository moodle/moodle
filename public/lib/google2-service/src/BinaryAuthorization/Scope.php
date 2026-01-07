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

class Scope extends \Google\Model
{
  /**
   * Optional. Matches all Kubernetes service accounts in the provided
   * namespace, unless a more specific `kubernetes_service_account` scope
   * already matched.
   *
   * @var string
   */
  public $kubernetesNamespace;
  /**
   * Optional. Matches a single Kubernetes service account, e.g. `my-
   * namespace:my-service-account`. `kubernetes_service_account` scope is always
   * more specific than `kubernetes_namespace` scope for the same namespace.
   *
   * @var string
   */
  public $kubernetesServiceAccount;

  /**
   * Optional. Matches all Kubernetes service accounts in the provided
   * namespace, unless a more specific `kubernetes_service_account` scope
   * already matched.
   *
   * @param string $kubernetesNamespace
   */
  public function setKubernetesNamespace($kubernetesNamespace)
  {
    $this->kubernetesNamespace = $kubernetesNamespace;
  }
  /**
   * @return string
   */
  public function getKubernetesNamespace()
  {
    return $this->kubernetesNamespace;
  }
  /**
   * Optional. Matches a single Kubernetes service account, e.g. `my-
   * namespace:my-service-account`. `kubernetes_service_account` scope is always
   * more specific than `kubernetes_namespace` scope for the same namespace.
   *
   * @param string $kubernetesServiceAccount
   */
  public function setKubernetesServiceAccount($kubernetesServiceAccount)
  {
    $this->kubernetesServiceAccount = $kubernetesServiceAccount;
  }
  /**
   * @return string
   */
  public function getKubernetesServiceAccount()
  {
    return $this->kubernetesServiceAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Scope::class, 'Google_Service_BinaryAuthorization_Scope');
