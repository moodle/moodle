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

class Cve extends \Google\Collection
{
  /**
   * Invalid or empty value.
   */
  public const EXPLOITATION_ACTIVITY_EXPLOITATION_ACTIVITY_UNSPECIFIED = 'EXPLOITATION_ACTIVITY_UNSPECIFIED';
  /**
   * Exploitation has been reported or confirmed to widely occur.
   */
  public const EXPLOITATION_ACTIVITY_WIDE = 'WIDE';
  /**
   * Limited reported or confirmed exploitation activities.
   */
  public const EXPLOITATION_ACTIVITY_CONFIRMED = 'CONFIRMED';
  /**
   * Exploit is publicly available.
   */
  public const EXPLOITATION_ACTIVITY_AVAILABLE = 'AVAILABLE';
  /**
   * No known exploitation activity, but has a high potential for exploitation.
   */
  public const EXPLOITATION_ACTIVITY_ANTICIPATED = 'ANTICIPATED';
  /**
   * No known exploitation activity.
   */
  public const EXPLOITATION_ACTIVITY_NO_KNOWN = 'NO_KNOWN';
  /**
   * Invalid or empty value.
   */
  public const IMPACT_RISK_RATING_UNSPECIFIED = 'RISK_RATING_UNSPECIFIED';
  /**
   * Exploitation would have little to no security impact.
   */
  public const IMPACT_LOW = 'LOW';
  /**
   * Exploitation would enable attackers to perform activities, or could allow
   * attackers to have a direct impact, but would require additional steps.
   */
  public const IMPACT_MEDIUM = 'MEDIUM';
  /**
   * Exploitation would enable attackers to have a notable direct impact without
   * needing to overcome any major mitigating factors.
   */
  public const IMPACT_HIGH = 'HIGH';
  /**
   * Exploitation would fundamentally undermine the security of affected
   * systems, enable actors to perform significant attacks with minimal effort,
   * with little to no mitigating factors to overcome.
   */
  public const IMPACT_CRITICAL = 'CRITICAL';
  protected $collection_key = 'references';
  protected $cvssv3Type = Cvssv3::class;
  protected $cvssv3DataType = '';
  /**
   * Date the first publicly available exploit or PoC was released.
   *
   * @var string
   */
  public $exploitReleaseDate;
  /**
   * The exploitation activity of the vulnerability in the wild.
   *
   * @var string
   */
  public $exploitationActivity;
  /**
   * Date of the earliest known exploitation.
   *
   * @var string
   */
  public $firstExploitationDate;
  /**
   * The unique identifier for the vulnerability. e.g. CVE-2021-34527
   *
   * @var string
   */
  public $id;
  /**
   * The potential impact of the vulnerability if it was to be exploited.
   *
   * @var string
   */
  public $impact;
  /**
   * Whether or not the vulnerability has been observed in the wild.
   *
   * @var bool
   */
  public $observedInTheWild;
  protected $referencesType = Reference::class;
  protected $referencesDataType = 'array';
  /**
   * Whether upstream fix is available for the CVE.
   *
   * @var bool
   */
  public $upstreamFixAvailable;
  /**
   * Whether or not the vulnerability was zero day when the finding was
   * published.
   *
   * @var bool
   */
  public $zeroDay;

  /**
   * Describe Common Vulnerability Scoring System specified at
   * https://www.first.org/cvss/v3.1/specification-document
   *
   * @param Cvssv3 $cvssv3
   */
  public function setCvssv3(Cvssv3 $cvssv3)
  {
    $this->cvssv3 = $cvssv3;
  }
  /**
   * @return Cvssv3
   */
  public function getCvssv3()
  {
    return $this->cvssv3;
  }
  /**
   * Date the first publicly available exploit or PoC was released.
   *
   * @param string $exploitReleaseDate
   */
  public function setExploitReleaseDate($exploitReleaseDate)
  {
    $this->exploitReleaseDate = $exploitReleaseDate;
  }
  /**
   * @return string
   */
  public function getExploitReleaseDate()
  {
    return $this->exploitReleaseDate;
  }
  /**
   * The exploitation activity of the vulnerability in the wild.
   *
   * Accepted values: EXPLOITATION_ACTIVITY_UNSPECIFIED, WIDE, CONFIRMED,
   * AVAILABLE, ANTICIPATED, NO_KNOWN
   *
   * @param self::EXPLOITATION_ACTIVITY_* $exploitationActivity
   */
  public function setExploitationActivity($exploitationActivity)
  {
    $this->exploitationActivity = $exploitationActivity;
  }
  /**
   * @return self::EXPLOITATION_ACTIVITY_*
   */
  public function getExploitationActivity()
  {
    return $this->exploitationActivity;
  }
  /**
   * Date of the earliest known exploitation.
   *
   * @param string $firstExploitationDate
   */
  public function setFirstExploitationDate($firstExploitationDate)
  {
    $this->firstExploitationDate = $firstExploitationDate;
  }
  /**
   * @return string
   */
  public function getFirstExploitationDate()
  {
    return $this->firstExploitationDate;
  }
  /**
   * The unique identifier for the vulnerability. e.g. CVE-2021-34527
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The potential impact of the vulnerability if it was to be exploited.
   *
   * Accepted values: RISK_RATING_UNSPECIFIED, LOW, MEDIUM, HIGH, CRITICAL
   *
   * @param self::IMPACT_* $impact
   */
  public function setImpact($impact)
  {
    $this->impact = $impact;
  }
  /**
   * @return self::IMPACT_*
   */
  public function getImpact()
  {
    return $this->impact;
  }
  /**
   * Whether or not the vulnerability has been observed in the wild.
   *
   * @param bool $observedInTheWild
   */
  public function setObservedInTheWild($observedInTheWild)
  {
    $this->observedInTheWild = $observedInTheWild;
  }
  /**
   * @return bool
   */
  public function getObservedInTheWild()
  {
    return $this->observedInTheWild;
  }
  /**
   * Additional information about the CVE. e.g. https://cve.mitre.org/cgi-
   * bin/cvename.cgi?name=CVE-2021-34527
   *
   * @param Reference[] $references
   */
  public function setReferences($references)
  {
    $this->references = $references;
  }
  /**
   * @return Reference[]
   */
  public function getReferences()
  {
    return $this->references;
  }
  /**
   * Whether upstream fix is available for the CVE.
   *
   * @param bool $upstreamFixAvailable
   */
  public function setUpstreamFixAvailable($upstreamFixAvailable)
  {
    $this->upstreamFixAvailable = $upstreamFixAvailable;
  }
  /**
   * @return bool
   */
  public function getUpstreamFixAvailable()
  {
    return $this->upstreamFixAvailable;
  }
  /**
   * Whether or not the vulnerability was zero day when the finding was
   * published.
   *
   * @param bool $zeroDay
   */
  public function setZeroDay($zeroDay)
  {
    $this->zeroDay = $zeroDay;
  }
  /**
   * @return bool
   */
  public function getZeroDay()
  {
    return $this->zeroDay;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cve::class, 'Google_Service_SecurityCommandCenter_Cve');
