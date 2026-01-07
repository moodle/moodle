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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1p3beta1StreamingAnnotateVideoResponse extends \Google\Model
{
  protected $annotationResultsType = GoogleCloudVideointelligenceV1p3beta1StreamingVideoAnnotationResults::class;
  protected $annotationResultsDataType = '';
  /**
   * Google Cloud Storage URI that stores annotation results of one streaming
   * session in JSON format. It is the annotation_result_storage_directory from
   * the request followed by '/cloud_project_number-session_id'.
   *
   * @var string
   */
  public $annotationResultsUri;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';

  /**
   * Streaming annotation results.
   *
   * @param GoogleCloudVideointelligenceV1p3beta1StreamingVideoAnnotationResults $annotationResults
   */
  public function setAnnotationResults(GoogleCloudVideointelligenceV1p3beta1StreamingVideoAnnotationResults $annotationResults)
  {
    $this->annotationResults = $annotationResults;
  }
  /**
   * @return GoogleCloudVideointelligenceV1p3beta1StreamingVideoAnnotationResults
   */
  public function getAnnotationResults()
  {
    return $this->annotationResults;
  }
  /**
   * Google Cloud Storage URI that stores annotation results of one streaming
   * session in JSON format. It is the annotation_result_storage_directory from
   * the request followed by '/cloud_project_number-session_id'.
   *
   * @param string $annotationResultsUri
   */
  public function setAnnotationResultsUri($annotationResultsUri)
  {
    $this->annotationResultsUri = $annotationResultsUri;
  }
  /**
   * @return string
   */
  public function getAnnotationResultsUri()
  {
    return $this->annotationResultsUri;
  }
  /**
   * If set, returns a google.rpc.Status message that specifies the error for
   * the operation.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1p3beta1StreamingAnnotateVideoResponse::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1p3beta1StreamingAnnotateVideoResponse');
