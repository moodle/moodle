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

class PodResult extends \Google\Collection
{
  /**
   * Not specified. This should never be used.
   */
  public const VERDICT_POD_VERDICT_UNSPECIFIED = 'POD_VERDICT_UNSPECIFIED';
  /**
   * All images conform to the policy.
   */
  public const VERDICT_CONFORMANT = 'CONFORMANT';
  /**
   * At least one image does not conform to the policy.
   */
  public const VERDICT_NON_CONFORMANT = 'NON_CONFORMANT';
  /**
   * Encountered at least one error evaluating an image and all other images
   * with non-error verdicts conform to the policy. Non-conformance has
   * precedence over errors.
   */
  public const VERDICT_ERROR = 'ERROR';
  protected $collection_key = 'imageResults';
  protected $imageResultsType = ImageResult::class;
  protected $imageResultsDataType = 'array';
  /**
   * The Kubernetes namespace of the Pod.
   *
   * @var string
   */
  public $kubernetesNamespace;
  /**
   * The Kubernetes service account of the Pod.
   *
   * @var string
   */
  public $kubernetesServiceAccount;
  /**
   * The name of the Pod.
   *
   * @var string
   */
  public $podName;
  /**
   * The result of evaluating this Pod.
   *
   * @var string
   */
  public $verdict;

  /**
   * Per-image details.
   *
   * @param ImageResult[] $imageResults
   */
  public function setImageResults($imageResults)
  {
    $this->imageResults = $imageResults;
  }
  /**
   * @return ImageResult[]
   */
  public function getImageResults()
  {
    return $this->imageResults;
  }
  /**
   * The Kubernetes namespace of the Pod.
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
   * The Kubernetes service account of the Pod.
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
  /**
   * The name of the Pod.
   *
   * @param string $podName
   */
  public function setPodName($podName)
  {
    $this->podName = $podName;
  }
  /**
   * @return string
   */
  public function getPodName()
  {
    return $this->podName;
  }
  /**
   * The result of evaluating this Pod.
   *
   * Accepted values: POD_VERDICT_UNSPECIFIED, CONFORMANT, NON_CONFORMANT, ERROR
   *
   * @param self::VERDICT_* $verdict
   */
  public function setVerdict($verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return self::VERDICT_*
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PodResult::class, 'Google_Service_BinaryAuthorization_PodResult');
