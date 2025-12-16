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

namespace Google\Service\AdExchangeBuyerII;

class ImpressionMetricsRow extends \Google\Model
{
  protected $availableImpressionsType = MetricValue::class;
  protected $availableImpressionsDataType = '';
  protected $bidRequestsType = MetricValue::class;
  protected $bidRequestsDataType = '';
  protected $inventoryMatchesType = MetricValue::class;
  protected $inventoryMatchesDataType = '';
  protected $responsesWithBidsType = MetricValue::class;
  protected $responsesWithBidsDataType = '';
  protected $rowDimensionsType = RowDimensions::class;
  protected $rowDimensionsDataType = '';
  protected $successfulResponsesType = MetricValue::class;
  protected $successfulResponsesDataType = '';

  /**
   * The number of impressions available to the buyer on Ad Exchange. In some
   * cases this value may be unavailable.
   *
   * @param MetricValue $availableImpressions
   */
  public function setAvailableImpressions(MetricValue $availableImpressions)
  {
    $this->availableImpressions = $availableImpressions;
  }
  /**
   * @return MetricValue
   */
  public function getAvailableImpressions()
  {
    return $this->availableImpressions;
  }
  /**
   * The number of impressions for which Ad Exchange sent the buyer a bid
   * request.
   *
   * @param MetricValue $bidRequests
   */
  public function setBidRequests(MetricValue $bidRequests)
  {
    $this->bidRequests = $bidRequests;
  }
  /**
   * @return MetricValue
   */
  public function getBidRequests()
  {
    return $this->bidRequests;
  }
  /**
   * The number of impressions that match the buyer's inventory pretargeting.
   *
   * @param MetricValue $inventoryMatches
   */
  public function setInventoryMatches(MetricValue $inventoryMatches)
  {
    $this->inventoryMatches = $inventoryMatches;
  }
  /**
   * @return MetricValue
   */
  public function getInventoryMatches()
  {
    return $this->inventoryMatches;
  }
  /**
   * The number of impressions for which Ad Exchange received a response from
   * the buyer that contained at least one applicable bid.
   *
   * @param MetricValue $responsesWithBids
   */
  public function setResponsesWithBids(MetricValue $responsesWithBids)
  {
    $this->responsesWithBids = $responsesWithBids;
  }
  /**
   * @return MetricValue
   */
  public function getResponsesWithBids()
  {
    return $this->responsesWithBids;
  }
  /**
   * The values of all dimensions associated with metric values in this row.
   *
   * @param RowDimensions $rowDimensions
   */
  public function setRowDimensions(RowDimensions $rowDimensions)
  {
    $this->rowDimensions = $rowDimensions;
  }
  /**
   * @return RowDimensions
   */
  public function getRowDimensions()
  {
    return $this->rowDimensions;
  }
  /**
   * The number of impressions for which the buyer successfully sent a response
   * to Ad Exchange.
   *
   * @param MetricValue $successfulResponses
   */
  public function setSuccessfulResponses(MetricValue $successfulResponses)
  {
    $this->successfulResponses = $successfulResponses;
  }
  /**
   * @return MetricValue
   */
  public function getSuccessfulResponses()
  {
    return $this->successfulResponses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImpressionMetricsRow::class, 'Google_Service_AdExchangeBuyerII_ImpressionMetricsRow');
