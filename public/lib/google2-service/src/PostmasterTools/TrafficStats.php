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

class TrafficStats extends \Google\Collection
{
  /**
   * The default value which should never be used explicitly. This represents
   * the state where no reputation information is available.
   */
  public const DOMAIN_REPUTATION_REPUTATION_CATEGORY_UNSPECIFIED = 'REPUTATION_CATEGORY_UNSPECIFIED';
  /**
   * Has a good track record of a very low spam rate, and complies with Gmail's
   * sender guidelines. Mail will rarely be marked by the spam filter.
   */
  public const DOMAIN_REPUTATION_HIGH = 'HIGH';
  /**
   * Known to send good mail, but is prone to sending a low volume of spam
   * intermittently. Most of the email from this entity will have a fair
   * deliverability rate, except when there is a notable increase in spam
   * levels.
   */
  public const DOMAIN_REPUTATION_MEDIUM = 'MEDIUM';
  /**
   * Known to send a considerable volume of spam regularly, and mail from this
   * sender will likely be marked as spam.
   */
  public const DOMAIN_REPUTATION_LOW = 'LOW';
  /**
   * History of sending an enormously high volume of spam. Mail coming from this
   * entity will almost always be rejected at SMTP level or marked as spam.
   */
  public const DOMAIN_REPUTATION_BAD = 'BAD';
  protected $collection_key = 'spammyFeedbackLoops';
  protected $deliveryErrorsType = DeliveryError::class;
  protected $deliveryErrorsDataType = 'array';
  /**
   * The ratio of mail that successfully authenticated with DKIM vs. all mail
   * that attempted to authenticate with [DKIM](http://www.dkim.org/). Spoofed
   * mail is excluded.
   *
   * @var 
   */
  public $dkimSuccessRatio;
  /**
   * The ratio of mail that passed [DMARC](https://dmarc.org/) alignment checks
   * vs all mail received from the domain that successfully authenticated with
   * either of [SPF](http://www.openspf.org/) or [DKIM](http://www.dkim.org/).
   *
   * @var 
   */
  public $dmarcSuccessRatio;
  /**
   * Reputation of the domain.
   *
   * @var string
   */
  public $domainReputation;
  /**
   * The ratio of incoming mail (to Gmail), that passed secure transport (TLS)
   * vs all mail received from that domain. This metric only pertains to traffic
   * that passed [SPF](http://www.openspf.org/) or [DKIM](http://www.dkim.org/).
   *
   * @var 
   */
  public $inboundEncryptionRatio;
  protected $ipReputationsType = IpReputation::class;
  protected $ipReputationsDataType = 'array';
  /**
   * The resource name of the traffic statistics. Traffic statistic names have
   * the form `domains/{domain}/trafficStats/{date}`, where domain_name is the
   * fully qualified domain name (i.e., mymail.mydomain.com) of the domain this
   * traffic statistics pertains to and date is the date in yyyymmdd format that
   * these statistics corresponds to. For example:
   * domains/mymail.mydomain.com/trafficStats/20160807
   *
   * @var string
   */
  public $name;
  /**
   * The ratio of outgoing mail (from Gmail) that was accepted over secure
   * transport (TLS).
   *
   * @var 
   */
  public $outboundEncryptionRatio;
  protected $spammyFeedbackLoopsType = FeedbackLoop::class;
  protected $spammyFeedbackLoopsDataType = 'array';
  /**
   * The ratio of mail that successfully authenticated with SPF vs. all mail
   * that attempted to authenticate with [SPF](http://www.openspf.org/). Spoofed
   * mail is excluded.
   *
   * @var 
   */
  public $spfSuccessRatio;
  /**
   * The ratio of user-report spam vs. email that was sent to the inbox. This is
   * potentially inexact -- users may want to refer to the description of the
   * interval fields userReportedSpamRatioLowerBound and
   * userReportedSpamRatioUpperBound for more explicit accuracy guarantees. This
   * metric only pertains to emails authenticated by
   * [DKIM](http://www.dkim.org/).
   *
   * @var 
   */
  public $userReportedSpamRatio;
  /**
   * The lower bound of the confidence interval for the user reported spam
   * ratio. If this field is set, then the value of userReportedSpamRatio is set
   * to the midpoint of this interval and is thus inexact. However, the true
   * ratio is guaranteed to be in between this lower bound and the corresponding
   * upper bound 95% of the time. This metric only pertains to emails
   * authenticated by [DKIM](http://www.dkim.org/).
   *
   * @var 
   */
  public $userReportedSpamRatioLowerBound;
  /**
   * The upper bound of the confidence interval for the user reported spam
   * ratio. If this field is set, then the value of userReportedSpamRatio is set
   * to the midpoint of this interval and is thus inexact. However, the true
   * ratio is guaranteed to be in between this upper bound and the corresponding
   * lower bound 95% of the time. This metric only pertains to emails
   * authenticated by [DKIM](http://www.dkim.org/).
   *
   * @var 
   */
  public $userReportedSpamRatioUpperBound;

  /**
   * Delivery errors for the domain. This metric only pertains to traffic that
   * passed [SPF](http://www.openspf.org/) or [DKIM](http://www.dkim.org/).
   *
   * @param DeliveryError[] $deliveryErrors
   */
  public function setDeliveryErrors($deliveryErrors)
  {
    $this->deliveryErrors = $deliveryErrors;
  }
  /**
   * @return DeliveryError[]
   */
  public function getDeliveryErrors()
  {
    return $this->deliveryErrors;
  }
  public function setDkimSuccessRatio($dkimSuccessRatio)
  {
    $this->dkimSuccessRatio = $dkimSuccessRatio;
  }
  public function getDkimSuccessRatio()
  {
    return $this->dkimSuccessRatio;
  }
  public function setDmarcSuccessRatio($dmarcSuccessRatio)
  {
    $this->dmarcSuccessRatio = $dmarcSuccessRatio;
  }
  public function getDmarcSuccessRatio()
  {
    return $this->dmarcSuccessRatio;
  }
  /**
   * Reputation of the domain.
   *
   * Accepted values: REPUTATION_CATEGORY_UNSPECIFIED, HIGH, MEDIUM, LOW, BAD
   *
   * @param self::DOMAIN_REPUTATION_* $domainReputation
   */
  public function setDomainReputation($domainReputation)
  {
    $this->domainReputation = $domainReputation;
  }
  /**
   * @return self::DOMAIN_REPUTATION_*
   */
  public function getDomainReputation()
  {
    return $this->domainReputation;
  }
  public function setInboundEncryptionRatio($inboundEncryptionRatio)
  {
    $this->inboundEncryptionRatio = $inboundEncryptionRatio;
  }
  public function getInboundEncryptionRatio()
  {
    return $this->inboundEncryptionRatio;
  }
  /**
   * Reputation information pertaining to the IP addresses of the email servers
   * for the domain. There is exactly one entry for each reputation category
   * except REPUTATION_CATEGORY_UNSPECIFIED.
   *
   * @param IpReputation[] $ipReputations
   */
  public function setIpReputations($ipReputations)
  {
    $this->ipReputations = $ipReputations;
  }
  /**
   * @return IpReputation[]
   */
  public function getIpReputations()
  {
    return $this->ipReputations;
  }
  /**
   * The resource name of the traffic statistics. Traffic statistic names have
   * the form `domains/{domain}/trafficStats/{date}`, where domain_name is the
   * fully qualified domain name (i.e., mymail.mydomain.com) of the domain this
   * traffic statistics pertains to and date is the date in yyyymmdd format that
   * these statistics corresponds to. For example:
   * domains/mymail.mydomain.com/trafficStats/20160807
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  public function setOutboundEncryptionRatio($outboundEncryptionRatio)
  {
    $this->outboundEncryptionRatio = $outboundEncryptionRatio;
  }
  public function getOutboundEncryptionRatio()
  {
    return $this->outboundEncryptionRatio;
  }
  /**
   * Spammy [Feedback loop identifiers]
   * (https://support.google.com/mail/answer/6254652) with their individual spam
   * rates. This metric only pertains to traffic that is authenticated by
   * [DKIM](http://www.dkim.org/).
   *
   * @param FeedbackLoop[] $spammyFeedbackLoops
   */
  public function setSpammyFeedbackLoops($spammyFeedbackLoops)
  {
    $this->spammyFeedbackLoops = $spammyFeedbackLoops;
  }
  /**
   * @return FeedbackLoop[]
   */
  public function getSpammyFeedbackLoops()
  {
    return $this->spammyFeedbackLoops;
  }
  public function setSpfSuccessRatio($spfSuccessRatio)
  {
    $this->spfSuccessRatio = $spfSuccessRatio;
  }
  public function getSpfSuccessRatio()
  {
    return $this->spfSuccessRatio;
  }
  public function setUserReportedSpamRatio($userReportedSpamRatio)
  {
    $this->userReportedSpamRatio = $userReportedSpamRatio;
  }
  public function getUserReportedSpamRatio()
  {
    return $this->userReportedSpamRatio;
  }
  public function setUserReportedSpamRatioLowerBound($userReportedSpamRatioLowerBound)
  {
    $this->userReportedSpamRatioLowerBound = $userReportedSpamRatioLowerBound;
  }
  public function getUserReportedSpamRatioLowerBound()
  {
    return $this->userReportedSpamRatioLowerBound;
  }
  public function setUserReportedSpamRatioUpperBound($userReportedSpamRatioUpperBound)
  {
    $this->userReportedSpamRatioUpperBound = $userReportedSpamRatioUpperBound;
  }
  public function getUserReportedSpamRatioUpperBound()
  {
    return $this->userReportedSpamRatioUpperBound;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrafficStats::class, 'Google_Service_PostmasterTools_TrafficStats');
