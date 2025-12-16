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

namespace Google\Service\ACMEDNS\Resource;

use Google\Service\ACMEDNS\AcmeChallengeSet;
use Google\Service\ACMEDNS\RotateChallengesRequest;

/**
 * The "acmeChallengeSets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $acmednsService = new Google\Service\ACMEDNS(...);
 *   $acmeChallengeSets = $acmednsService->acmeChallengeSets;
 *  </code>
 */
class AcmeChallengeSets extends \Google\Service\Resource
{
  /**
   * Gets the ACME challenge set for a given domain name. Domain names must be
   * provided in Punycode. (acmeChallengeSets.get)
   *
   * @param string $rootDomain Required. SLD + TLD domain name to list challenges.
   * For example, this would be "google.com" for any FQDN under "google.com". That
   * includes challenges for "subdomain.google.com". This MAY be Unicode or
   * Punycode.
   * @param array $optParams Optional parameters.
   * @return AcmeChallengeSet
   * @throws \Google\Service\Exception
   */
  public function get($rootDomain, $optParams = [])
  {
    $params = ['rootDomain' => $rootDomain];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AcmeChallengeSet::class);
  }
  /**
   * Rotate the ACME challenges for a given domain name. By default, removes any
   * challenges that are older than 30 days. Domain names must be provided in
   * Punycode. (acmeChallengeSets.rotateChallenges)
   *
   * @param string $rootDomain Required. SLD + TLD domain name to update records
   * for. For example, this would be "google.com" for any FQDN under "google.com".
   * That includes challenges for "subdomain.google.com". This MAY be Unicode or
   * Punycode.
   * @param RotateChallengesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AcmeChallengeSet
   * @throws \Google\Service\Exception
   */
  public function rotateChallenges($rootDomain, RotateChallengesRequest $postBody, $optParams = [])
  {
    $params = ['rootDomain' => $rootDomain, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rotateChallenges', [$params], AcmeChallengeSet::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AcmeChallengeSets::class, 'Google_Service_ACMEDNS_Resource_AcmeChallengeSets');
