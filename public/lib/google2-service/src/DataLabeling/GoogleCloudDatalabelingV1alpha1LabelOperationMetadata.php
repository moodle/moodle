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

class GoogleCloudDatalabelingV1alpha1LabelOperationMetadata extends \Google\Collection
{
  protected $collection_key = 'partialFailures';
  /**
   * Output only. The name of annotated dataset in format
   * "projects/datasets/annotatedDatasets".
   *
   * @var string
   */
  public $annotatedDataset;
  /**
   * Output only. Timestamp when labeling request was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The name of dataset to be labeled. "projects/datasets"
   *
   * @var string
   */
  public $dataset;
  protected $imageBoundingBoxDetailsType = GoogleCloudDatalabelingV1alpha1LabelImageBoundingBoxOperationMetadata::class;
  protected $imageBoundingBoxDetailsDataType = '';
  protected $imageBoundingPolyDetailsType = GoogleCloudDatalabelingV1alpha1LabelImageBoundingPolyOperationMetadata::class;
  protected $imageBoundingPolyDetailsDataType = '';
  protected $imageClassificationDetailsType = GoogleCloudDatalabelingV1alpha1LabelImageClassificationOperationMetadata::class;
  protected $imageClassificationDetailsDataType = '';
  protected $imageOrientedBoundingBoxDetailsType = GoogleCloudDatalabelingV1alpha1LabelImageOrientedBoundingBoxOperationMetadata::class;
  protected $imageOrientedBoundingBoxDetailsDataType = '';
  protected $imagePolylineDetailsType = GoogleCloudDatalabelingV1alpha1LabelImagePolylineOperationMetadata::class;
  protected $imagePolylineDetailsDataType = '';
  protected $imageSegmentationDetailsType = GoogleCloudDatalabelingV1alpha1LabelImageSegmentationOperationMetadata::class;
  protected $imageSegmentationDetailsDataType = '';
  protected $partialFailuresType = GoogleRpcStatus::class;
  protected $partialFailuresDataType = 'array';
  /**
   * Output only. Progress of label operation. Range: [0, 100].
   *
   * @var int
   */
  public $progressPercent;
  protected $textClassificationDetailsType = GoogleCloudDatalabelingV1alpha1LabelTextClassificationOperationMetadata::class;
  protected $textClassificationDetailsDataType = '';
  protected $textEntityExtractionDetailsType = GoogleCloudDatalabelingV1alpha1LabelTextEntityExtractionOperationMetadata::class;
  protected $textEntityExtractionDetailsDataType = '';
  protected $videoClassificationDetailsType = GoogleCloudDatalabelingV1alpha1LabelVideoClassificationOperationMetadata::class;
  protected $videoClassificationDetailsDataType = '';
  protected $videoEventDetailsType = GoogleCloudDatalabelingV1alpha1LabelVideoEventOperationMetadata::class;
  protected $videoEventDetailsDataType = '';
  protected $videoObjectDetectionDetailsType = GoogleCloudDatalabelingV1alpha1LabelVideoObjectDetectionOperationMetadata::class;
  protected $videoObjectDetectionDetailsDataType = '';
  protected $videoObjectTrackingDetailsType = GoogleCloudDatalabelingV1alpha1LabelVideoObjectTrackingOperationMetadata::class;
  protected $videoObjectTrackingDetailsDataType = '';

  /**
   * Output only. The name of annotated dataset in format
   * "projects/datasets/annotatedDatasets".
   *
   * @param string $annotatedDataset
   */
  public function setAnnotatedDataset($annotatedDataset)
  {
    $this->annotatedDataset = $annotatedDataset;
  }
  /**
   * @return string
   */
  public function getAnnotatedDataset()
  {
    return $this->annotatedDataset;
  }
  /**
   * Output only. Timestamp when labeling request was created.
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
   * Output only. The name of dataset to be labeled. "projects/datasets"
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Details of label image bounding box operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelImageBoundingBoxOperationMetadata $imageBoundingBoxDetails
   */
  public function setImageBoundingBoxDetails(GoogleCloudDatalabelingV1alpha1LabelImageBoundingBoxOperationMetadata $imageBoundingBoxDetails)
  {
    $this->imageBoundingBoxDetails = $imageBoundingBoxDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelImageBoundingBoxOperationMetadata
   */
  public function getImageBoundingBoxDetails()
  {
    return $this->imageBoundingBoxDetails;
  }
  /**
   * Details of label image bounding poly operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelImageBoundingPolyOperationMetadata $imageBoundingPolyDetails
   */
  public function setImageBoundingPolyDetails(GoogleCloudDatalabelingV1alpha1LabelImageBoundingPolyOperationMetadata $imageBoundingPolyDetails)
  {
    $this->imageBoundingPolyDetails = $imageBoundingPolyDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelImageBoundingPolyOperationMetadata
   */
  public function getImageBoundingPolyDetails()
  {
    return $this->imageBoundingPolyDetails;
  }
  /**
   * Details of label image classification operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelImageClassificationOperationMetadata $imageClassificationDetails
   */
  public function setImageClassificationDetails(GoogleCloudDatalabelingV1alpha1LabelImageClassificationOperationMetadata $imageClassificationDetails)
  {
    $this->imageClassificationDetails = $imageClassificationDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelImageClassificationOperationMetadata
   */
  public function getImageClassificationDetails()
  {
    return $this->imageClassificationDetails;
  }
  /**
   * Details of label image oriented bounding box operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelImageOrientedBoundingBoxOperationMetadata $imageOrientedBoundingBoxDetails
   */
  public function setImageOrientedBoundingBoxDetails(GoogleCloudDatalabelingV1alpha1LabelImageOrientedBoundingBoxOperationMetadata $imageOrientedBoundingBoxDetails)
  {
    $this->imageOrientedBoundingBoxDetails = $imageOrientedBoundingBoxDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelImageOrientedBoundingBoxOperationMetadata
   */
  public function getImageOrientedBoundingBoxDetails()
  {
    return $this->imageOrientedBoundingBoxDetails;
  }
  /**
   * Details of label image polyline operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelImagePolylineOperationMetadata $imagePolylineDetails
   */
  public function setImagePolylineDetails(GoogleCloudDatalabelingV1alpha1LabelImagePolylineOperationMetadata $imagePolylineDetails)
  {
    $this->imagePolylineDetails = $imagePolylineDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelImagePolylineOperationMetadata
   */
  public function getImagePolylineDetails()
  {
    return $this->imagePolylineDetails;
  }
  /**
   * Details of label image segmentation operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelImageSegmentationOperationMetadata $imageSegmentationDetails
   */
  public function setImageSegmentationDetails(GoogleCloudDatalabelingV1alpha1LabelImageSegmentationOperationMetadata $imageSegmentationDetails)
  {
    $this->imageSegmentationDetails = $imageSegmentationDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelImageSegmentationOperationMetadata
   */
  public function getImageSegmentationDetails()
  {
    return $this->imageSegmentationDetails;
  }
  /**
   * Output only. Partial failures encountered. E.g. single files that couldn't
   * be read. Status details field will contain standard GCP error details.
   *
   * @param GoogleRpcStatus[] $partialFailures
   */
  public function setPartialFailures($partialFailures)
  {
    $this->partialFailures = $partialFailures;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getPartialFailures()
  {
    return $this->partialFailures;
  }
  /**
   * Output only. Progress of label operation. Range: [0, 100].
   *
   * @param int $progressPercent
   */
  public function setProgressPercent($progressPercent)
  {
    $this->progressPercent = $progressPercent;
  }
  /**
   * @return int
   */
  public function getProgressPercent()
  {
    return $this->progressPercent;
  }
  /**
   * Details of label text classification operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelTextClassificationOperationMetadata $textClassificationDetails
   */
  public function setTextClassificationDetails(GoogleCloudDatalabelingV1alpha1LabelTextClassificationOperationMetadata $textClassificationDetails)
  {
    $this->textClassificationDetails = $textClassificationDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelTextClassificationOperationMetadata
   */
  public function getTextClassificationDetails()
  {
    return $this->textClassificationDetails;
  }
  /**
   * Details of label text entity extraction operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelTextEntityExtractionOperationMetadata $textEntityExtractionDetails
   */
  public function setTextEntityExtractionDetails(GoogleCloudDatalabelingV1alpha1LabelTextEntityExtractionOperationMetadata $textEntityExtractionDetails)
  {
    $this->textEntityExtractionDetails = $textEntityExtractionDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelTextEntityExtractionOperationMetadata
   */
  public function getTextEntityExtractionDetails()
  {
    return $this->textEntityExtractionDetails;
  }
  /**
   * Details of label video classification operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelVideoClassificationOperationMetadata $videoClassificationDetails
   */
  public function setVideoClassificationDetails(GoogleCloudDatalabelingV1alpha1LabelVideoClassificationOperationMetadata $videoClassificationDetails)
  {
    $this->videoClassificationDetails = $videoClassificationDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelVideoClassificationOperationMetadata
   */
  public function getVideoClassificationDetails()
  {
    return $this->videoClassificationDetails;
  }
  /**
   * Details of label video event operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelVideoEventOperationMetadata $videoEventDetails
   */
  public function setVideoEventDetails(GoogleCloudDatalabelingV1alpha1LabelVideoEventOperationMetadata $videoEventDetails)
  {
    $this->videoEventDetails = $videoEventDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelVideoEventOperationMetadata
   */
  public function getVideoEventDetails()
  {
    return $this->videoEventDetails;
  }
  /**
   * Details of label video object detection operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelVideoObjectDetectionOperationMetadata $videoObjectDetectionDetails
   */
  public function setVideoObjectDetectionDetails(GoogleCloudDatalabelingV1alpha1LabelVideoObjectDetectionOperationMetadata $videoObjectDetectionDetails)
  {
    $this->videoObjectDetectionDetails = $videoObjectDetectionDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelVideoObjectDetectionOperationMetadata
   */
  public function getVideoObjectDetectionDetails()
  {
    return $this->videoObjectDetectionDetails;
  }
  /**
   * Details of label video object tracking operation.
   *
   * @param GoogleCloudDatalabelingV1alpha1LabelVideoObjectTrackingOperationMetadata $videoObjectTrackingDetails
   */
  public function setVideoObjectTrackingDetails(GoogleCloudDatalabelingV1alpha1LabelVideoObjectTrackingOperationMetadata $videoObjectTrackingDetails)
  {
    $this->videoObjectTrackingDetails = $videoObjectTrackingDetails;
  }
  /**
   * @return GoogleCloudDatalabelingV1alpha1LabelVideoObjectTrackingOperationMetadata
   */
  public function getVideoObjectTrackingDetails()
  {
    return $this->videoObjectTrackingDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1alpha1LabelOperationMetadata::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1alpha1LabelOperationMetadata');
