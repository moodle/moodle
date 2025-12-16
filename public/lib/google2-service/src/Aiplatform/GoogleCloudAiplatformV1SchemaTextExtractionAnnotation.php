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

class GoogleCloudAiplatformV1SchemaTextExtractionAnnotation extends \Google\Model
{
  /**
   * The resource Id of the AnnotationSpec that this Annotation pertains to.
   *
   * @var string
   */
  public $annotationSpecId;
  /**
   * The display name of the AnnotationSpec that this Annotation pertains to.
   *
   * @var string
   */
  public $displayName;
  protected $textSegmentType = GoogleCloudAiplatformV1SchemaTextSegment::class;
  protected $textSegmentDataType = '';

  /**
   * The resource Id of the AnnotationSpec that this Annotation pertains to.
   *
   * @param string $annotationSpecId
   */
  public function setAnnotationSpecId($annotationSpecId)
  {
    $this->annotationSpecId = $annotationSpecId;
  }
  /**
   * @return string
   */
  public function getAnnotationSpecId()
  {
    return $this->annotationSpecId;
  }
  /**
   * The display name of the AnnotationSpec that this Annotation pertains to.
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
   * The segment of the text content.
   *
   * @param GoogleCloudAiplatformV1SchemaTextSegment $textSegment
   */
  public function setTextSegment(GoogleCloudAiplatformV1SchemaTextSegment $textSegment)
  {
    $this->textSegment = $textSegment;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTextSegment
   */
  public function getTextSegment()
  {
    return $this->textSegment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTextExtractionAnnotation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTextExtractionAnnotation');
