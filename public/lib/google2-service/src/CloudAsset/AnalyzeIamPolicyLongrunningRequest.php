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

namespace Google\Service\CloudAsset;

class AnalyzeIamPolicyLongrunningRequest extends \Google\Model
{
  protected $analysisQueryType = IamPolicyAnalysisQuery::class;
  protected $analysisQueryDataType = '';
  protected $outputConfigType = IamPolicyAnalysisOutputConfig::class;
  protected $outputConfigDataType = '';
  /**
   * Optional. The name of a saved query, which must be in the format of: *
   * projects/project_number/savedQueries/saved_query_id *
   * folders/folder_number/savedQueries/saved_query_id *
   * organizations/organization_number/savedQueries/saved_query_id If both
   * `analysis_query` and `saved_analysis_query` are provided, they will be
   * merged together with the `saved_analysis_query` as base and the
   * `analysis_query` as overrides. For more details of the merge behavior,
   * refer to the [MergeFrom](https://developers.google.com/protocol-buffers/doc
   * s/reference/cpp/google.protobuf.message#Message.MergeFrom.details) doc.
   * Note that you cannot override primitive fields with default value, such as
   * 0 or empty string, etc., because we use proto3, which doesn't support field
   * presence yet.
   *
   * @var string
   */
  public $savedAnalysisQuery;

  /**
   * Required. The request query.
   *
   * @param IamPolicyAnalysisQuery $analysisQuery
   */
  public function setAnalysisQuery(IamPolicyAnalysisQuery $analysisQuery)
  {
    $this->analysisQuery = $analysisQuery;
  }
  /**
   * @return IamPolicyAnalysisQuery
   */
  public function getAnalysisQuery()
  {
    return $this->analysisQuery;
  }
  /**
   * Required. Output configuration indicating where the results will be output
   * to.
   *
   * @param IamPolicyAnalysisOutputConfig $outputConfig
   */
  public function setOutputConfig(IamPolicyAnalysisOutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return IamPolicyAnalysisOutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * Optional. The name of a saved query, which must be in the format of: *
   * projects/project_number/savedQueries/saved_query_id *
   * folders/folder_number/savedQueries/saved_query_id *
   * organizations/organization_number/savedQueries/saved_query_id If both
   * `analysis_query` and `saved_analysis_query` are provided, they will be
   * merged together with the `saved_analysis_query` as base and the
   * `analysis_query` as overrides. For more details of the merge behavior,
   * refer to the [MergeFrom](https://developers.google.com/protocol-buffers/doc
   * s/reference/cpp/google.protobuf.message#Message.MergeFrom.details) doc.
   * Note that you cannot override primitive fields with default value, such as
   * 0 or empty string, etc., because we use proto3, which doesn't support field
   * presence yet.
   *
   * @param string $savedAnalysisQuery
   */
  public function setSavedAnalysisQuery($savedAnalysisQuery)
  {
    $this->savedAnalysisQuery = $savedAnalysisQuery;
  }
  /**
   * @return string
   */
  public function getSavedAnalysisQuery()
  {
    return $this->savedAnalysisQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzeIamPolicyLongrunningRequest::class, 'Google_Service_CloudAsset_AnalyzeIamPolicyLongrunningRequest');
