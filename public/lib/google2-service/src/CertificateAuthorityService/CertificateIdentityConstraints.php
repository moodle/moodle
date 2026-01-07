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

namespace Google\Service\CertificateAuthorityService;

class CertificateIdentityConstraints extends \Google\Model
{
  /**
   * Required. If this is true, the SubjectAltNames extension may be copied from
   * a certificate request into the signed certificate. Otherwise, the requested
   * SubjectAltNames will be discarded.
   *
   * @var bool
   */
  public $allowSubjectAltNamesPassthrough;
  /**
   * Required. If this is true, the Subject field may be copied from a
   * certificate request into the signed certificate. Otherwise, the requested
   * Subject will be discarded.
   *
   * @var bool
   */
  public $allowSubjectPassthrough;
  protected $celExpressionType = Expr::class;
  protected $celExpressionDataType = '';

  /**
   * Required. If this is true, the SubjectAltNames extension may be copied from
   * a certificate request into the signed certificate. Otherwise, the requested
   * SubjectAltNames will be discarded.
   *
   * @param bool $allowSubjectAltNamesPassthrough
   */
  public function setAllowSubjectAltNamesPassthrough($allowSubjectAltNamesPassthrough)
  {
    $this->allowSubjectAltNamesPassthrough = $allowSubjectAltNamesPassthrough;
  }
  /**
   * @return bool
   */
  public function getAllowSubjectAltNamesPassthrough()
  {
    return $this->allowSubjectAltNamesPassthrough;
  }
  /**
   * Required. If this is true, the Subject field may be copied from a
   * certificate request into the signed certificate. Otherwise, the requested
   * Subject will be discarded.
   *
   * @param bool $allowSubjectPassthrough
   */
  public function setAllowSubjectPassthrough($allowSubjectPassthrough)
  {
    $this->allowSubjectPassthrough = $allowSubjectPassthrough;
  }
  /**
   * @return bool
   */
  public function getAllowSubjectPassthrough()
  {
    return $this->allowSubjectPassthrough;
  }
  /**
   * Optional. A CEL expression that may be used to validate the resolved X.509
   * Subject and/or Subject Alternative Name before a certificate is signed. To
   * see the full allowed syntax and some examples, see
   * https://cloud.google.com/certificate-authority-service/docs/using-cel
   *
   * @param Expr $celExpression
   */
  public function setCelExpression(Expr $celExpression)
  {
    $this->celExpression = $celExpression;
  }
  /**
   * @return Expr
   */
  public function getCelExpression()
  {
    return $this->celExpression;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateIdentityConstraints::class, 'Google_Service_CertificateAuthorityService_CertificateIdentityConstraints');
