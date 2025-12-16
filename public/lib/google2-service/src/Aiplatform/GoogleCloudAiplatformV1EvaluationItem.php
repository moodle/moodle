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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1EvaluationItem extends \Google\Model
{
  /**
   * The default value. This value is unused.
   */
  public const EVALUATION_ITEM_TYPE_EVALUATION_ITEM_TYPE_UNSPECIFIED = 'EVALUATION_ITEM_TYPE_UNSPECIFIED';
  /**
   * The EvaluationItem is a request to evaluate.
   */
  public const EVALUATION_ITEM_TYPE_REQUEST = 'REQUEST';
  /**
   * The EvaluationItem is the result of evaluation.
   */
  public const EVALUATION_ITEM_TYPE_RESULT = 'RESULT';
  /**
   * Output only. Timestamp when this item was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The display name of the EvaluationItem.
   *
   * @var string
   */
  public $displayName;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * Required. The type of the EvaluationItem.
   *
   * @var string
   */
  public $evaluationItemType;
  protected $evaluationRequestType = GoogleCloudAiplatformV1EvaluationRequest::class;
  protected $evaluationRequestDataType = '';
  protected $evaluationResponseType = GoogleCloudAiplatformV1EvaluationResult::class;
  protected $evaluationResponseDataType = '';
  /**
   * The Cloud Storage object where the request or response is stored.
   *
   * @var string
   */
  public $gcsUri;
  /**
   * Optional. Labels for the EvaluationItem.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Metadata for the EvaluationItem.
   *
   * @var array
   */
  public $metadata;
  /**
   * Identifier. The resource name of the EvaluationItem. Format:
   * `projects/{project}/locations/{location}/evaluationItems/{evaluation_item}`
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Timestamp when this item was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The display name of the EvaluationItem.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Error for the evaluation item.
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
  /**
   * Required. The type of the EvaluationItem.
   *
   * Accepted values: EVALUATION_ITEM_TYPE_UNSPECIFIED, REQUEST, RESULT
   *
   * @param self::EVALUATION_ITEM_TYPE_* $evaluationItemType
   */
  public function setEvaluationItemType($evaluationItemType)
  {
    $this->evaluationItemType = $evaluationItemType;
  }
  /**
   * @return self::EVALUATION_ITEM_TYPE_*
   */
  public function getEvaluationItemType()
  {
    return $this->evaluationItemType;
  }
  /**
   * The request to evaluate.
   *
   * @param GoogleCloudAiplatformV1EvaluationRequest $evaluationRequest
   */
  public function setEvaluationRequest(GoogleCloudAiplatformV1EvaluationRequest $evaluationRequest)
  {
    $this->evaluationRequest = $evaluationRequest;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRequest
   */
  public function getEvaluationRequest()
  {
    return $this->evaluationRequest;
  }
  /**
   * Output only. The response from evaluation.
   *
   * @param GoogleCloudAiplatformV1EvaluationResult $evaluationResponse
   */
  public function setEvaluationResponse(GoogleCloudAiplatformV1EvaluationResult $evaluationResponse)
  {
    $this->evaluationResponse = $evaluationResponse;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationResult
   */
  public function getEvaluationResponse()
  {
    return $this->evaluationResponse;
  }
  /**
   * The Cloud Storage object where the request or response is stored.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
  /**
   * Optional. Labels for the EvaluationItem.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. Metadata for the EvaluationItem.
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Identifier. The resource name of the EvaluationItem. Format:
   * `projects/{project}/locations/{location}/evaluationItems/{evaluation_item}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationItem::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationItem');
