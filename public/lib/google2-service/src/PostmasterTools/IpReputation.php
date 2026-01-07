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

namespace Google\Service\PostmasterTools;

class IpReputation extends \Google\Collection
{
  /**
   * The default value which should never be used explicitly. This represents
   * the state where no reputation information is available.
   */
  public const REPUTATION_REPUTATION_CATEGORY_UNSPECIFIED = 'REPUTATION_CATEGORY_UNSPECIFIED';
  /**
   * Has a good track record of a very low spam rate, and complies with Gmail's
   * sender guidelines. Mail will rarely be marked by the spam filter.
   */
  public const REPUTATION_HIGH = 'HIGH';
  /**
   * Known to send good mail, but is prone to sending a low volume of spam
   * intermittently. Most of the email from this entity will have a fair
   * deliverability rate, except when there is a notable increase in spam
   * levels.
   */
  public const REPUTATION_MEDIUM = 'MEDIUM';
  /**
   * Known to send a considerable volume of spam regularly, and mail from this
   * sender will likely be marked as spam.
   */
  public const REPUTATION_LOW = 'LOW';
  /**
   * History of sending an enormously high volume of spam. Mail coming from this
   * entity will almost always be rejected at SMTP level or marked as spam.
   */
  public const REPUTATION_BAD = 'BAD';
  protected $collection_key = 'sampleIps';
  /**
   * Total number of unique IPs in this reputation category. This metric only
   * pertains to traffic that passed [SPF](http://www.openspf.org/) or
   * [DKIM](http://www.dkim.org/).
   *
   * @var string
   */
  public $ipCount;
  /**
   * The reputation category this IP reputation represents.
   *
   * @var string
   */
  public $reputation;
  /**
   * A sample of IPs in this reputation category.
   *
   * @var string[]
   */
  public $sampleIps;

  /**
   * Total number of unique IPs in this reputation category. This metric only
   * pertains to traffic that passed [SPF](http://www.openspf.org/) or
   * [DKIM](http://www.dkim.org/).
   *
   * @param string $ipCount
   */
  public function setIpCount($ipCount)
  {
    $this->ipCount = $ipCount;
  }
  /**
   * @return string
   */
  public function getIpCount()
  {
    return $this->ipCount;
  }
  /**
   * The reputation category this IP reputation represents.
   *
   * Accepted values: REPUTATION_CATEGORY_UNSPECIFIED, HIGH, MEDIUM, LOW, BAD
   *
   * @param self::REPUTATION_* $reputation
   */
  public function setReputation($reputation)
  {
    $this->reputation = $reputation;
  }
  /**
   * @return self::REPUTATION_*
   */
  public function getReputation()
  {
    return $this->reputation;
  }
  /**
   * A sample of IPs in this reputation category.
   *
   * @param string[] $sampleIps
   */
  public function setSampleIps($sampleIps)
  {
    $this->sampleIps = $sampleIps;
  }
  /**
   * @return string[]
   */
  public function getSampleIps()
  {
    return $this->sampleIps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IpReputation::class, 'Google_Service_PostmasterTools_IpReputation');
