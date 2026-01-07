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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsResponse extends \Google\Collection
{
  protected $collection_key = 'securityAssessmentResults';
  /**
   * The time of the assessment api call.
   *
   * @var string
   */
  public $assessmentTime;
  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is blank, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $securityAssessmentResultsType = GoogleCloudApigeeV1SecurityAssessmentResult::class;
  protected $securityAssessmentResultsDataType = 'array';

  /**
   * The time of the assessment api call.
   *
   * @param string $assessmentTime
   */
  public function setAssessmentTime($assessmentTime)
  {
    $this->assessmentTime = $assessmentTime;
  }
  /**
   * @return string
   */
  public function getAssessmentTime()
  {
    return $this->assessmentTime;
  }
  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is blank, there are no subsequent pages.
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
   * Default sort order is by resource name in alphabetic order.
   *
   * @param GoogleCloudApigeeV1SecurityAssessmentResult[] $securityAssessmentResults
   */
  public function setSecurityAssessmentResults($securityAssessmentResults)
  {
    $this->securityAssessmentResults = $securityAssessmentResults;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityAssessmentResult[]
   */
  public function getSecurityAssessmentResults()
  {
    return $this->securityAssessmentResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsResponse::class, 'Google_Service_Apigee_GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsResponse');
