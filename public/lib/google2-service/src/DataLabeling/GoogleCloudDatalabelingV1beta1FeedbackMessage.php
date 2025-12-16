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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1FeedbackMessage extends \Google\Model
{
  /**
   * String content of the feedback. Maximum of 10000 characters.
   *
   * @var string
   */
  public $body;
  /**
   * Create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * The image storing this feedback if the feedback is an image representing
   * operator's comments.
   *
   * @var string
   */
  public $image;
  /**
   * Name of the feedback message in a feedback thread. Format: 'project/{projec
   * t_id}/datasets/{dataset_id}/annotatedDatasets/{annotated_dataset_id}/feedba
   * ckThreads/{feedback_thread_id}/feedbackMessage/{feedback_message_id}'
   *
   * @var string
   */
  public $name;
  protected $operatorFeedbackMetadataType = GoogleCloudDatalabelingV1beta1OperatorFeedbackMetadata::class;
  protected $operatorFeedbackMetadataDataType = '';
  protected $requesterFeedbackMetadataType = GoogleCloudDatalabelingV1beta1RequesterFeedbackMetadata::class;
  protected $requesterFeedbackMetadataDataType = '';

  /**
   * String content of the feedback. Maximum of 10000 characters.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Create time.
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
   * The image storing this feedback if the feedback is an image representing
   * operator's comments.
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Name of the feedback message in a feedback thread. Format: 'project/{projec
   * t_id}/datasets/{dataset_id}/annotatedDatasets/{annotated_dataset_id}/feedba
   * ckThreads/{feedback_thread_id}/feedbackMessage/{feedback_message_id}'
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
  /**
   * @param GoogleCloudDatalabelingV1beta1OperatorFeedbackMetadata $operatorFeedbackMetadata
   */
  public function setOperatorFeedbackMetadata(GoogleCloudDatalabelingV1beta1OperatorFeedbackMetadata $operatorFeedbackMetadata)
  {
    $this->operatorFeedbackMetadata = $operatorFeedbackMetadata;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1OperatorFeedbackMetadata
   */
  public function getOperatorFeedbackMetadata()
  {
    return $this->operatorFeedbackMetadata;
  }
  /**
   * @param GoogleCloudDatalabelingV1beta1RequesterFeedbackMetadata $requesterFeedbackMetadata
   */
  public function setRequesterFeedbackMetadata(GoogleCloudDatalabelingV1beta1RequesterFeedbackMetadata $requesterFeedbackMetadata)
  {
    $this->requesterFeedbackMetadata = $requesterFeedbackMetadata;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1RequesterFeedbackMetadata
   */
  public function getRequesterFeedbackMetadata()
  {
    return $this->requesterFeedbackMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1FeedbackMessage::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1FeedbackMessage');
