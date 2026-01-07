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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1ConversationDataSource extends \Google\Model
{
  protected $dialogflowSourceType = GoogleCloudContactcenterinsightsV1alpha1DialogflowSource::class;
  protected $dialogflowSourceDataType = '';
  protected $gcsSourceType = GoogleCloudContactcenterinsightsV1alpha1GcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * Cloud Storage URI that points to a file that contains the conversation
   * metadata.
   *
   * @var string
   */
  public $metadataUri;

  /**
   * The source when the conversation comes from Dialogflow.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DialogflowSource $dialogflowSource
   */
  public function setDialogflowSource(GoogleCloudContactcenterinsightsV1alpha1DialogflowSource $dialogflowSource)
  {
    $this->dialogflowSource = $dialogflowSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DialogflowSource
   */
  public function getDialogflowSource()
  {
    return $this->dialogflowSource;
  }
  /**
   * A Cloud Storage location specification for the audio and transcript.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudContactcenterinsightsV1alpha1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Cloud Storage URI that points to a file that contains the conversation
   * metadata.
   *
   * @param string $metadataUri
   */
  public function setMetadataUri($metadataUri)
  {
    $this->metadataUri = $metadataUri;
  }
  /**
   * @return string
   */
  public function getMetadataUri()
  {
    return $this->metadataUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1ConversationDataSource::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1ConversationDataSource');
