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

namespace Google\Service\Appengine;

class SslSettings extends \Google\Model
{
  /**
   * Defaults to AUTOMATIC.
   */
  public const SSL_MANAGEMENT_TYPE_SSL_MANAGEMENT_TYPE_UNSPECIFIED = 'SSL_MANAGEMENT_TYPE_UNSPECIFIED';
  /**
   * SSL support for this domain is configured automatically. The mapped SSL
   * certificate will be automatically renewed.
   */
  public const SSL_MANAGEMENT_TYPE_AUTOMATIC = 'AUTOMATIC';
  /**
   * SSL support for this domain is configured manually by the user. Either the
   * domain has no SSL support or a user-obtained SSL certificate has been
   * explicitly mapped to this domain.
   */
  public const SSL_MANAGEMENT_TYPE_MANUAL = 'MANUAL';
  /**
   * ID of the AuthorizedCertificate resource configuring SSL for the
   * application. Clearing this field will remove SSL support.By default, a
   * managed certificate is automatically created for every domain mapping. To
   * omit SSL support or to configure SSL manually, specify
   * SslManagementType.MANUAL on a CREATE or UPDATE request. You must be
   * authorized to administer the AuthorizedCertificate resource to manually map
   * it to a DomainMapping resource. Example: 12345.
   *
   * @var string
   */
  public $certificateId;
  /**
   * Output only. ID of the managed AuthorizedCertificate resource currently
   * being provisioned, if applicable. Until the new managed certificate has
   * been successfully provisioned, the previous SSL state will be preserved.
   * Once the provisioning process completes, the certificate_id field will
   * reflect the new managed certificate and this field will be left empty. To
   * remove SSL support while there is still a pending managed certificate,
   * clear the certificate_id field with an
   * UpdateDomainMappingRequest.@OutputOnly
   *
   * @var string
   */
  public $pendingManagedCertificateId;
  /**
   * SSL management type for this domain. If AUTOMATIC, a managed certificate is
   * automatically provisioned. If MANUAL, certificate_id must be manually
   * specified in order to configure SSL for this domain.
   *
   * @var string
   */
  public $sslManagementType;

  /**
   * ID of the AuthorizedCertificate resource configuring SSL for the
   * application. Clearing this field will remove SSL support.By default, a
   * managed certificate is automatically created for every domain mapping. To
   * omit SSL support or to configure SSL manually, specify
   * SslManagementType.MANUAL on a CREATE or UPDATE request. You must be
   * authorized to administer the AuthorizedCertificate resource to manually map
   * it to a DomainMapping resource. Example: 12345.
   *
   * @param string $certificateId
   */
  public function setCertificateId($certificateId)
  {
    $this->certificateId = $certificateId;
  }
  /**
   * @return string
   */
  public function getCertificateId()
  {
    return $this->certificateId;
  }
  /**
   * Output only. ID of the managed AuthorizedCertificate resource currently
   * being provisioned, if applicable. Until the new managed certificate has
   * been successfully provisioned, the previous SSL state will be preserved.
   * Once the provisioning process completes, the certificate_id field will
   * reflect the new managed certificate and this field will be left empty. To
   * remove SSL support while there is still a pending managed certificate,
   * clear the certificate_id field with an
   * UpdateDomainMappingRequest.@OutputOnly
   *
   * @param string $pendingManagedCertificateId
   */
  public function setPendingManagedCertificateId($pendingManagedCertificateId)
  {
    $this->pendingManagedCertificateId = $pendingManagedCertificateId;
  }
  /**
   * @return string
   */
  public function getPendingManagedCertificateId()
  {
    return $this->pendingManagedCertificateId;
  }
  /**
   * SSL management type for this domain. If AUTOMATIC, a managed certificate is
   * automatically provisioned. If MANUAL, certificate_id must be manually
   * specified in order to configure SSL for this domain.
   *
   * Accepted values: SSL_MANAGEMENT_TYPE_UNSPECIFIED, AUTOMATIC, MANUAL
   *
   * @param self::SSL_MANAGEMENT_TYPE_* $sslManagementType
   */
  public function setSslManagementType($sslManagementType)
  {
    $this->sslManagementType = $sslManagementType;
  }
  /**
   * @return self::SSL_MANAGEMENT_TYPE_*
   */
  public function getSslManagementType()
  {
    return $this->sslManagementType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SslSettings::class, 'Google_Service_Appengine_SslSettings');
