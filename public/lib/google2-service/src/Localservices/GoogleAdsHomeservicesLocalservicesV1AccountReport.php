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

namespace Google\Service\Localservices;

class GoogleAdsHomeservicesLocalservicesV1AccountReport extends \Google\Model
{
  /**
   * Unique identifier of the GLS account.
   *
   * @var string
   */
  public $accountId;
  protected $aggregatorInfoType = GoogleAdsHomeservicesLocalservicesV1AggregatorInfo::class;
  protected $aggregatorInfoDataType = '';
  /**
   * Average review rating score from 1-5 stars.
   *
   * @var 
   */
  public $averageFiveStarRating;
  /**
   * Average weekly budget in the currency code of the account.
   *
   * @var 
   */
  public $averageWeeklyBudget;
  /**
   * Business name of the account.
   *
   * @var string
   */
  public $businessName;
  /**
   * Currency code of the account.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Number of charged leads the account received in current specified period.
   *
   * @var string
   */
  public $currentPeriodChargedLeads;
  /**
   * Number of connected phone calls (duration over 30s) in current specified
   * period.
   *
   * @var string
   */
  public $currentPeriodConnectedPhoneCalls;
  /**
   * Number of phone calls in current specified period, including both connected
   * and unconnected calls.
   *
   * @var string
   */
  public $currentPeriodPhoneCalls;
  /**
   * Total cost of the account in current specified period in the account's
   * specified currency.
   *
   * @var 
   */
  public $currentPeriodTotalCost;
  /**
   * Number of impressions that customers have had in the past 2 days.
   *
   * @var string
   */
  public $impressionsLastTwoDays;
  /**
   * Phone lead responsiveness of the account for the past 90 days from current
   * date. This is computed by taking the total number of connected calls from
   * charged phone leads and dividing by the total number of calls received.
   *
   * @var 
   */
  public $phoneLeadResponsiveness;
  /**
   * Number of charged leads the account received in previous specified period.
   *
   * @var string
   */
  public $previousPeriodChargedLeads;
  /**
   * Number of connected phone calls (duration over 30s) in previous specified
   * period.
   *
   * @var string
   */
  public $previousPeriodConnectedPhoneCalls;
  /**
   * Number of phone calls in previous specified period, including both
   * connected and unconnected calls.
   *
   * @var string
   */
  public $previousPeriodPhoneCalls;
  /**
   * Total cost of the account in previous specified period in the account's
   * specified currency.
   *
   * @var 
   */
  public $previousPeriodTotalCost;
  /**
   * Total number of reviews the account has up to current date.
   *
   * @var int
   */
  public $totalReview;

  /**
   * Unique identifier of the GLS account.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Aggregator specific information related to the account.
   *
   * @param GoogleAdsHomeservicesLocalservicesV1AggregatorInfo $aggregatorInfo
   */
  public function setAggregatorInfo(GoogleAdsHomeservicesLocalservicesV1AggregatorInfo $aggregatorInfo)
  {
    $this->aggregatorInfo = $aggregatorInfo;
  }
  /**
   * @return GoogleAdsHomeservicesLocalservicesV1AggregatorInfo
   */
  public function getAggregatorInfo()
  {
    return $this->aggregatorInfo;
  }
  public function setAverageFiveStarRating($averageFiveStarRating)
  {
    $this->averageFiveStarRating = $averageFiveStarRating;
  }
  public function getAverageFiveStarRating()
  {
    return $this->averageFiveStarRating;
  }
  public function setAverageWeeklyBudget($averageWeeklyBudget)
  {
    $this->averageWeeklyBudget = $averageWeeklyBudget;
  }
  public function getAverageWeeklyBudget()
  {
    return $this->averageWeeklyBudget;
  }
  /**
   * Business name of the account.
   *
   * @param string $businessName
   */
  public function setBusinessName($businessName)
  {
    $this->businessName = $businessName;
  }
  /**
   * @return string
   */
  public function getBusinessName()
  {
    return $this->businessName;
  }
  /**
   * Currency code of the account.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Number of charged leads the account received in current specified period.
   *
   * @param string $currentPeriodChargedLeads
   */
  public function setCurrentPeriodChargedLeads($currentPeriodChargedLeads)
  {
    $this->currentPeriodChargedLeads = $currentPeriodChargedLeads;
  }
  /**
   * @return string
   */
  public function getCurrentPeriodChargedLeads()
  {
    return $this->currentPeriodChargedLeads;
  }
  /**
   * Number of connected phone calls (duration over 30s) in current specified
   * period.
   *
   * @param string $currentPeriodConnectedPhoneCalls
   */
  public function setCurrentPeriodConnectedPhoneCalls($currentPeriodConnectedPhoneCalls)
  {
    $this->currentPeriodConnectedPhoneCalls = $currentPeriodConnectedPhoneCalls;
  }
  /**
   * @return string
   */
  public function getCurrentPeriodConnectedPhoneCalls()
  {
    return $this->currentPeriodConnectedPhoneCalls;
  }
  /**
   * Number of phone calls in current specified period, including both connected
   * and unconnected calls.
   *
   * @param string $currentPeriodPhoneCalls
   */
  public function setCurrentPeriodPhoneCalls($currentPeriodPhoneCalls)
  {
    $this->currentPeriodPhoneCalls = $currentPeriodPhoneCalls;
  }
  /**
   * @return string
   */
  public function getCurrentPeriodPhoneCalls()
  {
    return $this->currentPeriodPhoneCalls;
  }
  public function setCurrentPeriodTotalCost($currentPeriodTotalCost)
  {
    $this->currentPeriodTotalCost = $currentPeriodTotalCost;
  }
  public function getCurrentPeriodTotalCost()
  {
    return $this->currentPeriodTotalCost;
  }
  /**
   * Number of impressions that customers have had in the past 2 days.
   *
   * @param string $impressionsLastTwoDays
   */
  public function setImpressionsLastTwoDays($impressionsLastTwoDays)
  {
    $this->impressionsLastTwoDays = $impressionsLastTwoDays;
  }
  /**
   * @return string
   */
  public function getImpressionsLastTwoDays()
  {
    return $this->impressionsLastTwoDays;
  }
  public function setPhoneLeadResponsiveness($phoneLeadResponsiveness)
  {
    $this->phoneLeadResponsiveness = $phoneLeadResponsiveness;
  }
  public function getPhoneLeadResponsiveness()
  {
    return $this->phoneLeadResponsiveness;
  }
  /**
   * Number of charged leads the account received in previous specified period.
   *
   * @param string $previousPeriodChargedLeads
   */
  public function setPreviousPeriodChargedLeads($previousPeriodChargedLeads)
  {
    $this->previousPeriodChargedLeads = $previousPeriodChargedLeads;
  }
  /**
   * @return string
   */
  public function getPreviousPeriodChargedLeads()
  {
    return $this->previousPeriodChargedLeads;
  }
  /**
   * Number of connected phone calls (duration over 30s) in previous specified
   * period.
   *
   * @param string $previousPeriodConnectedPhoneCalls
   */
  public function setPreviousPeriodConnectedPhoneCalls($previousPeriodConnectedPhoneCalls)
  {
    $this->previousPeriodConnectedPhoneCalls = $previousPeriodConnectedPhoneCalls;
  }
  /**
   * @return string
   */
  public function getPreviousPeriodConnectedPhoneCalls()
  {
    return $this->previousPeriodConnectedPhoneCalls;
  }
  /**
   * Number of phone calls in previous specified period, including both
   * connected and unconnected calls.
   *
   * @param string $previousPeriodPhoneCalls
   */
  public function setPreviousPeriodPhoneCalls($previousPeriodPhoneCalls)
  {
    $this->previousPeriodPhoneCalls = $previousPeriodPhoneCalls;
  }
  /**
   * @return string
   */
  public function getPreviousPeriodPhoneCalls()
  {
    return $this->previousPeriodPhoneCalls;
  }
  public function setPreviousPeriodTotalCost($previousPeriodTotalCost)
  {
    $this->previousPeriodTotalCost = $previousPeriodTotalCost;
  }
  public function getPreviousPeriodTotalCost()
  {
    return $this->previousPeriodTotalCost;
  }
  /**
   * Total number of reviews the account has up to current date.
   *
   * @param int $totalReview
   */
  public function setTotalReview($totalReview)
  {
    $this->totalReview = $totalReview;
  }
  /**
   * @return int
   */
  public function getTotalReview()
  {
    return $this->totalReview;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsHomeservicesLocalservicesV1AccountReport::class, 'Google_Service_Localservices_GoogleAdsHomeservicesLocalservicesV1AccountReport');
