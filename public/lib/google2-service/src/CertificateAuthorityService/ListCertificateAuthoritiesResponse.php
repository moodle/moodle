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

class ListCertificateAuthoritiesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $certificateAuthoritiesType = CertificateAuthority::class;
  protected $certificateAuthoritiesDataType = 'array';
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListCertificateAuthoritiesRequest.page_token to retrieve the next page of
   * results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * A list of locations (e.g. "us-west1") that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The list of CertificateAuthorities.
   *
   * @param CertificateAuthority[] $certificateAuthorities
   */
  public function setCertificateAuthorities($certificateAuthorities)
  {
    $this->certificateAuthorities = $certificateAuthorities;
  }
  /**
   * @return CertificateAuthority[]
   */
  public function getCertificateAuthorities()
  {
    return $this->certificateAuthorities;
  }
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListCertificateAuthoritiesRequest.page_token to retrieve the next page of
   * results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * A list of locations (e.g. "us-west1") that could not be reached.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListCertificateAuthoritiesResponse::class, 'Google_Service_CertificateAuthorityService_ListCertificateAuthoritiesResponse');
