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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2TransformationDetails extends \Google\Collection
{
  protected $collection_key = 'transformation';
  /**
   * The top level name of the container where the transformation is located
   * (this will be the source file name or table name).
   *
   * @var string
   */
  public $containerName;
  /**
   * The name of the job that completed the transformation.
   *
   * @var string
   */
  public $resourceName;
  protected $statusDetailsType = GooglePrivacyDlpV2TransformationResultStatus::class;
  protected $statusDetailsDataType = '';
  protected $transformationType = GooglePrivacyDlpV2TransformationDescription::class;
  protected $transformationDataType = 'array';
  protected $transformationLocationType = GooglePrivacyDlpV2TransformationLocation::class;
  protected $transformationLocationDataType = '';
  /**
   * The number of bytes that were transformed. If transformation was
   * unsuccessful or did not take place because there was no content to
   * transform, this will be zero.
   *
   * @var string
   */
  public $transformedBytes;

  /**
   * The top level name of the container where the transformation is located
   * (this will be the source file name or table name).
   *
   * @param string $containerName
   */
  public function setContainerName($containerName)
  {
    $this->containerName = $containerName;
  }
  /**
   * @return string
   */
  public function getContainerName()
  {
    return $this->containerName;
  }
  /**
   * The name of the job that completed the transformation.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Status of the transformation, if transformation was not successful, this
   * will specify what caused it to fail, otherwise it will show that the
   * transformation was successful.
   *
   * @param GooglePrivacyDlpV2TransformationResultStatus $statusDetails
   */
  public function setStatusDetails(GooglePrivacyDlpV2TransformationResultStatus $statusDetails)
  {
    $this->statusDetails = $statusDetails;
  }
  /**
   * @return GooglePrivacyDlpV2TransformationResultStatus
   */
  public function getStatusDetails()
  {
    return $this->statusDetails;
  }
  /**
   * Description of transformation. This would only contain more than one
   * element if there were multiple matching transformations and which one to
   * apply was ambiguous. Not set for states that contain no transformation,
   * currently only state that contains no transformation is
   * TransformationResultStateType.METADATA_UNRETRIEVABLE.
   *
   * @param GooglePrivacyDlpV2TransformationDescription[] $transformation
   */
  public function setTransformation($transformation)
  {
    $this->transformation = $transformation;
  }
  /**
   * @return GooglePrivacyDlpV2TransformationDescription[]
   */
  public function getTransformation()
  {
    return $this->transformation;
  }
  /**
   * The precise location of the transformed content in the original container.
   *
   * @param GooglePrivacyDlpV2TransformationLocation $transformationLocation
   */
  public function setTransformationLocation(GooglePrivacyDlpV2TransformationLocation $transformationLocation)
  {
    $this->transformationLocation = $transformationLocation;
  }
  /**
   * @return GooglePrivacyDlpV2TransformationLocation
   */
  public function getTransformationLocation()
  {
    return $this->transformationLocation;
  }
  /**
   * The number of bytes that were transformed. If transformation was
   * unsuccessful or did not take place because there was no content to
   * transform, this will be zero.
   *
   * @param string $transformedBytes
   */
  public function setTransformedBytes($transformedBytes)
  {
    $this->transformedBytes = $transformedBytes;
  }
  /**
   * @return string
   */
  public function getTransformedBytes()
  {
    return $this->transformedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TransformationDetails::class, 'Google_Service_DLP_GooglePrivacyDlpV2TransformationDetails');
