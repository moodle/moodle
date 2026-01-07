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

namespace Google\Service\AnalyticsData;

class ResponseMetaData extends \Google\Collection
{
  protected $collection_key = 'samplingMetadatas';
  /**
   * The currency code used in this report. Intended to be used in formatting
   * currency metrics like `purchaseRevenue` for visualization. If currency_code
   * was specified in the request, this response parameter will echo the request
   * parameter; otherwise, this response parameter is the property's current
   * currency_code. Currency codes are string encodings of currency types from
   * the ISO 4217 standard (https://en.wikipedia.org/wiki/ISO_4217); for example
   * "USD", "EUR", "JPY". To learn more, see
   * https://support.google.com/analytics/answer/9796179.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * If true, indicates some buckets of dimension combinations are rolled into
   * "(other)" row. This can happen for high cardinality reports. The metadata
   * parameter dataLossFromOtherRow is populated based on the aggregated data
   * table used in the report. The parameter will be accurately populated
   * regardless of the filters and limits in the report. For example, the
   * (other) row could be dropped from the report because the request contains a
   * filter on sessionSource = google. This parameter will still be populated if
   * data loss from other row was present in the input aggregate data used to
   * generate this report. To learn more, see [About the (other) row and data
   * sampling](https://support.google.com/analytics/answer/13208658#reports).
   *
   * @var bool
   */
  public $dataLossFromOtherRow;
  /**
   * If empty reason is specified, the report is empty for this reason.
   *
   * @var string
   */
  public $emptyReason;
  protected $samplingMetadatasType = SamplingMetadata::class;
  protected $samplingMetadatasDataType = 'array';
  protected $schemaRestrictionResponseType = SchemaRestrictionResponse::class;
  protected $schemaRestrictionResponseDataType = '';
  /**
   * If `subjectToThresholding` is true, this report is subject to thresholding
   * and only returns data that meets the minimum aggregation thresholds. It is
   * possible for a request to be subject to thresholding thresholding and no
   * data is absent from the report, and this happens when all data is above the
   * thresholds. To learn more, see [Data
   * thresholds](https://support.google.com/analytics/answer/9383630).
   *
   * @var bool
   */
  public $subjectToThresholding;
  /**
   * The property's current timezone. Intended to be used to interpret time-
   * based dimensions like `hour` and `minute`. Formatted as strings from the
   * IANA Time Zone database (https://www.iana.org/time-zones); for example
   * "America/New_York" or "Asia/Tokyo".
   *
   * @var string
   */
  public $timeZone;

  /**
   * The currency code used in this report. Intended to be used in formatting
   * currency metrics like `purchaseRevenue` for visualization. If currency_code
   * was specified in the request, this response parameter will echo the request
   * parameter; otherwise, this response parameter is the property's current
   * currency_code. Currency codes are string encodings of currency types from
   * the ISO 4217 standard (https://en.wikipedia.org/wiki/ISO_4217); for example
   * "USD", "EUR", "JPY". To learn more, see
   * https://support.google.com/analytics/answer/9796179.
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
   * If true, indicates some buckets of dimension combinations are rolled into
   * "(other)" row. This can happen for high cardinality reports. The metadata
   * parameter dataLossFromOtherRow is populated based on the aggregated data
   * table used in the report. The parameter will be accurately populated
   * regardless of the filters and limits in the report. For example, the
   * (other) row could be dropped from the report because the request contains a
   * filter on sessionSource = google. This parameter will still be populated if
   * data loss from other row was present in the input aggregate data used to
   * generate this report. To learn more, see [About the (other) row and data
   * sampling](https://support.google.com/analytics/answer/13208658#reports).
   *
   * @param bool $dataLossFromOtherRow
   */
  public function setDataLossFromOtherRow($dataLossFromOtherRow)
  {
    $this->dataLossFromOtherRow = $dataLossFromOtherRow;
  }
  /**
   * @return bool
   */
  public function getDataLossFromOtherRow()
  {
    return $this->dataLossFromOtherRow;
  }
  /**
   * If empty reason is specified, the report is empty for this reason.
   *
   * @param string $emptyReason
   */
  public function setEmptyReason($emptyReason)
  {
    $this->emptyReason = $emptyReason;
  }
  /**
   * @return string
   */
  public function getEmptyReason()
  {
    return $this->emptyReason;
  }
  /**
   * If this report results is
   * [sampled](https://support.google.com/analytics/answer/13331292), this
   * describes the percentage of events used in this report. One
   * `samplingMetadatas` is populated for each date range. Each
   * `samplingMetadatas` corresponds to a date range in order that date ranges
   * were specified in the request. However if the results are not sampled, this
   * field will not be defined.
   *
   * @param SamplingMetadata[] $samplingMetadatas
   */
  public function setSamplingMetadatas($samplingMetadatas)
  {
    $this->samplingMetadatas = $samplingMetadatas;
  }
  /**
   * @return SamplingMetadata[]
   */
  public function getSamplingMetadatas()
  {
    return $this->samplingMetadatas;
  }
  /**
   * Describes the schema restrictions actively enforced in creating this
   * report. To learn more, see [Access and data-restriction
   * management](https://support.google.com/analytics/answer/10851388).
   *
   * @param SchemaRestrictionResponse $schemaRestrictionResponse
   */
  public function setSchemaRestrictionResponse(SchemaRestrictionResponse $schemaRestrictionResponse)
  {
    $this->schemaRestrictionResponse = $schemaRestrictionResponse;
  }
  /**
   * @return SchemaRestrictionResponse
   */
  public function getSchemaRestrictionResponse()
  {
    return $this->schemaRestrictionResponse;
  }
  /**
   * If `subjectToThresholding` is true, this report is subject to thresholding
   * and only returns data that meets the minimum aggregation thresholds. It is
   * possible for a request to be subject to thresholding thresholding and no
   * data is absent from the report, and this happens when all data is above the
   * thresholds. To learn more, see [Data
   * thresholds](https://support.google.com/analytics/answer/9383630).
   *
   * @param bool $subjectToThresholding
   */
  public function setSubjectToThresholding($subjectToThresholding)
  {
    $this->subjectToThresholding = $subjectToThresholding;
  }
  /**
   * @return bool
   */
  public function getSubjectToThresholding()
  {
    return $this->subjectToThresholding;
  }
  /**
   * The property's current timezone. Intended to be used to interpret time-
   * based dimensions like `hour` and `minute`. Formatted as strings from the
   * IANA Time Zone database (https://www.iana.org/time-zones); for example
   * "America/New_York" or "Asia/Tokyo".
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResponseMetaData::class, 'Google_Service_AnalyticsData_ResponseMetaData');
