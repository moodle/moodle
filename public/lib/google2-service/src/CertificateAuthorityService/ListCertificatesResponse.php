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

class ListCertificatesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $certificatesType = Certificate::class;
  protected $certificatesDataType = 'array';
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListCertificatesRequest.page_token to retrieve the next page of results.
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
   * The list of Certificates.
   *
   * @param Certificate[] $certificates
   */
  public function setCertificates($certificates)
  {
    $this->certificates = $certificates;
  }
  /**
   * @return Certificate[]
   */
  public function getCertificates()
  {
    return $this->certificates;
  }
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListCertificatesRequest.page_token to retrieve the next page of results.
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
class_alias(ListCertificatesResponse::class, 'Google_Service_CertificateAuthorityService_ListCertificatesResponse');
