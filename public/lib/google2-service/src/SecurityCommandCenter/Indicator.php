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

namespace Google\Service\SecurityCommandCenter;

class Indicator extends \Google\Collection
{
  protected $collection_key = 'uris';
  /**
   * List of domains associated to the Finding.
   *
   * @var string[]
   */
  public $domains;
  /**
   * The list of IP addresses that are associated with the finding.
   *
   * @var string[]
   */
  public $ipAddresses;
  protected $signaturesType = ProcessSignature::class;
  protected $signaturesDataType = 'array';
  /**
   * The list of URIs associated to the Findings.
   *
   * @var string[]
   */
  public $uris;

  /**
   * List of domains associated to the Finding.
   *
   * @param string[] $domains
   */
  public function setDomains($domains)
  {
    $this->domains = $domains;
  }
  /**
   * @return string[]
   */
  public function getDomains()
  {
    return $this->domains;
  }
  /**
   * The list of IP addresses that are associated with the finding.
   *
   * @param string[] $ipAddresses
   */
  public function setIpAddresses($ipAddresses)
  {
    $this->ipAddresses = $ipAddresses;
  }
  /**
   * @return string[]
   */
  public function getIpAddresses()
  {
    return $this->ipAddresses;
  }
  /**
   * The list of matched signatures indicating that the given process is present
   * in the environment.
   *
   * @param ProcessSignature[] $signatures
   */
  public function setSignatures($signatures)
  {
    $this->signatures = $signatures;
  }
  /**
   * @return ProcessSignature[]
   */
  public function getSignatures()
  {
    return $this->signatures;
  }
  /**
   * The list of URIs associated to the Findings.
   *
   * @param string[] $uris
   */
  public function setUris($uris)
  {
    $this->uris = $uris;
  }
  /**
   * @return string[]
   */
  public function getUris()
  {
    return $this->uris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Indicator::class, 'Google_Service_SecurityCommandCenter_Indicator');
