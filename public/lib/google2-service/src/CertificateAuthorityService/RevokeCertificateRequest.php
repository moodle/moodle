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

class RevokeCertificateRequest extends \Google\Model
{
  /**
   * Default unspecified value. This value does indicate that a Certificate has
   * been revoked, but that a reason has not been recorded.
   */
  public const REASON_REVOCATION_REASON_UNSPECIFIED = 'REVOCATION_REASON_UNSPECIFIED';
  /**
   * Key material for this Certificate may have leaked.
   */
  public const REASON_KEY_COMPROMISE = 'KEY_COMPROMISE';
  /**
   * The key material for a certificate authority in the issuing path may have
   * leaked.
   */
  public const REASON_CERTIFICATE_AUTHORITY_COMPROMISE = 'CERTIFICATE_AUTHORITY_COMPROMISE';
  /**
   * The subject or other attributes in this Certificate have changed.
   */
  public const REASON_AFFILIATION_CHANGED = 'AFFILIATION_CHANGED';
  /**
   * This Certificate has been superseded.
   */
  public const REASON_SUPERSEDED = 'SUPERSEDED';
  /**
   * This Certificate or entities in the issuing path have ceased to operate.
   */
  public const REASON_CESSATION_OF_OPERATION = 'CESSATION_OF_OPERATION';
  /**
   * This Certificate should not be considered valid, it is expected that it may
   * become valid in the future.
   */
  public const REASON_CERTIFICATE_HOLD = 'CERTIFICATE_HOLD';
  /**
   * This Certificate no longer has permission to assert the listed attributes.
   */
  public const REASON_PRIVILEGE_WITHDRAWN = 'PRIVILEGE_WITHDRAWN';
  /**
   * The authority which determines appropriate attributes for a Certificate may
   * have been compromised.
   */
  public const REASON_ATTRIBUTE_AUTHORITY_COMPROMISE = 'ATTRIBUTE_AUTHORITY_COMPROMISE';
  /**
   * Required. The RevocationReason for revoking this certificate.
   *
   * @var string
   */
  public $reason;
  /**
   * Optional. An ID to identify requests. Specify a unique request ID so that
   * if you must retry your request, the server will know to ignore the request
   * if it has already been completed. The server will guarantee that for at
   * least 60 minutes since the first request. For example, consider a situation
   * where you make an initial request and the request times out. If you make
   * the request again with the same request ID, the server can check if
   * original operation with the same request ID was received, and if so, will
   * ignore the second request. This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;

  /**
   * Required. The RevocationReason for revoking this certificate.
   *
   * Accepted values: REVOCATION_REASON_UNSPECIFIED, KEY_COMPROMISE,
   * CERTIFICATE_AUTHORITY_COMPROMISE, AFFILIATION_CHANGED, SUPERSEDED,
   * CESSATION_OF_OPERATION, CERTIFICATE_HOLD, PRIVILEGE_WITHDRAWN,
   * ATTRIBUTE_AUTHORITY_COMPROMISE
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Optional. An ID to identify requests. Specify a unique request ID so that
   * if you must retry your request, the server will know to ignore the request
   * if it has already been completed. The server will guarantee that for at
   * least 60 minutes since the first request. For example, consider a situation
   * where you make an initial request and the request times out. If you make
   * the request again with the same request ID, the server can check if
   * original operation with the same request ID was received, and if so, will
   * ignore the second request. This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevokeCertificateRequest::class, 'Google_Service_CertificateAuthorityService_RevokeCertificateRequest');
