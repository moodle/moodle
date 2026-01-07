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

namespace Google\Service\OnDemandScanning;

class SbomReferenceIntotoPredicate extends \Google\Model
{
  /**
   * A map of algorithm to digest of the contents of the SBOM.
   *
   * @var string[]
   */
  public $digest;
  /**
   * The location of the SBOM.
   *
   * @var string
   */
  public $location;
  /**
   * The mime type of the SBOM.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The person or system referring this predicate to the consumer.
   *
   * @var string
   */
  public $referrerId;

  /**
   * A map of algorithm to digest of the contents of the SBOM.
   *
   * @param string[] $digest
   */
  public function setDigest($digest)
  {
    $this->digest = $digest;
  }
  /**
   * @return string[]
   */
  public function getDigest()
  {
    return $this->digest;
  }
  /**
   * The location of the SBOM.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The mime type of the SBOM.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The person or system referring this predicate to the consumer.
   *
   * @param string $referrerId
   */
  public function setReferrerId($referrerId)
  {
    $this->referrerId = $referrerId;
  }
  /**
   * @return string
   */
  public function getReferrerId()
  {
    return $this->referrerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SbomReferenceIntotoPredicate::class, 'Google_Service_OnDemandScanning_SbomReferenceIntotoPredicate');
