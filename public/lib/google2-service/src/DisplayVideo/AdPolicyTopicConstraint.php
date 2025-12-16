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

namespace Google\Service\DisplayVideo;

class AdPolicyTopicConstraint extends \Google\Model
{
  protected $certificateDomainMismatchCountryListType = AdPolicyTopicConstraintAdPolicyCountryConstraintList::class;
  protected $certificateDomainMismatchCountryListDataType = '';
  protected $certificateMissingCountryListType = AdPolicyTopicConstraintAdPolicyCountryConstraintList::class;
  protected $certificateMissingCountryListDataType = '';
  protected $countryConstraintType = AdPolicyTopicConstraintAdPolicyCountryConstraintList::class;
  protected $countryConstraintDataType = '';
  protected $globalCertificateDomainMismatchType = AdPolicyTopicConstraintAdPolicyGlobalCertificateDomainMismatchConstraint::class;
  protected $globalCertificateDomainMismatchDataType = '';
  protected $globalCertificateMissingType = AdPolicyTopicConstraintAdPolicyGlobalCertificateMissingConstraint::class;
  protected $globalCertificateMissingDataType = '';
  /**
   * Link to the form to request a certificate for the constraint.
   *
   * @var string
   */
  public $requestCertificateFormLink;
  protected $resellerConstraintType = AdPolicyTopicConstraintAdPolicyResellerConstraint::class;
  protected $resellerConstraintDataType = '';

  /**
   * Countries where the resource's domain is not covered by the certificates
   * associated with it.
   *
   * @param AdPolicyTopicConstraintAdPolicyCountryConstraintList $certificateDomainMismatchCountryList
   */
  public function setCertificateDomainMismatchCountryList(AdPolicyTopicConstraintAdPolicyCountryConstraintList $certificateDomainMismatchCountryList)
  {
    $this->certificateDomainMismatchCountryList = $certificateDomainMismatchCountryList;
  }
  /**
   * @return AdPolicyTopicConstraintAdPolicyCountryConstraintList
   */
  public function getCertificateDomainMismatchCountryList()
  {
    return $this->certificateDomainMismatchCountryList;
  }
  /**
   * Countries where a certificate is required for serving.
   *
   * @param AdPolicyTopicConstraintAdPolicyCountryConstraintList $certificateMissingCountryList
   */
  public function setCertificateMissingCountryList(AdPolicyTopicConstraintAdPolicyCountryConstraintList $certificateMissingCountryList)
  {
    $this->certificateMissingCountryList = $certificateMissingCountryList;
  }
  /**
   * @return AdPolicyTopicConstraintAdPolicyCountryConstraintList
   */
  public function getCertificateMissingCountryList()
  {
    return $this->certificateMissingCountryList;
  }
  /**
   * Countries where the ad cannot serve.
   *
   * @param AdPolicyTopicConstraintAdPolicyCountryConstraintList $countryConstraint
   */
  public function setCountryConstraint(AdPolicyTopicConstraintAdPolicyCountryConstraintList $countryConstraint)
  {
    $this->countryConstraint = $countryConstraint;
  }
  /**
   * @return AdPolicyTopicConstraintAdPolicyCountryConstraintList
   */
  public function getCountryConstraint()
  {
    return $this->countryConstraint;
  }
  /**
   * Certificate is required to serve in any country and the existing
   * certificate does not cover the ad's domain.
   *
   * @param AdPolicyTopicConstraintAdPolicyGlobalCertificateDomainMismatchConstraint $globalCertificateDomainMismatch
   */
  public function setGlobalCertificateDomainMismatch(AdPolicyTopicConstraintAdPolicyGlobalCertificateDomainMismatchConstraint $globalCertificateDomainMismatch)
  {
    $this->globalCertificateDomainMismatch = $globalCertificateDomainMismatch;
  }
  /**
   * @return AdPolicyTopicConstraintAdPolicyGlobalCertificateDomainMismatchConstraint
   */
  public function getGlobalCertificateDomainMismatch()
  {
    return $this->globalCertificateDomainMismatch;
  }
  /**
   * Certificate is required to serve in any country.
   *
   * @param AdPolicyTopicConstraintAdPolicyGlobalCertificateMissingConstraint $globalCertificateMissing
   */
  public function setGlobalCertificateMissing(AdPolicyTopicConstraintAdPolicyGlobalCertificateMissingConstraint $globalCertificateMissing)
  {
    $this->globalCertificateMissing = $globalCertificateMissing;
  }
  /**
   * @return AdPolicyTopicConstraintAdPolicyGlobalCertificateMissingConstraint
   */
  public function getGlobalCertificateMissing()
  {
    return $this->globalCertificateMissing;
  }
  /**
   * Link to the form to request a certificate for the constraint.
   *
   * @param string $requestCertificateFormLink
   */
  public function setRequestCertificateFormLink($requestCertificateFormLink)
  {
    $this->requestCertificateFormLink = $requestCertificateFormLink;
  }
  /**
   * @return string
   */
  public function getRequestCertificateFormLink()
  {
    return $this->requestCertificateFormLink;
  }
  /**
   * Reseller constraint.
   *
   * @param AdPolicyTopicConstraintAdPolicyResellerConstraint $resellerConstraint
   */
  public function setResellerConstraint(AdPolicyTopicConstraintAdPolicyResellerConstraint $resellerConstraint)
  {
    $this->resellerConstraint = $resellerConstraint;
  }
  /**
   * @return AdPolicyTopicConstraintAdPolicyResellerConstraint
   */
  public function getResellerConstraint()
  {
    return $this->resellerConstraint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicyTopicConstraint::class, 'Google_Service_DisplayVideo_AdPolicyTopicConstraint');
