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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1MetadataJobExportJobSpec extends \Google\Model
{
  /**
   * Required. The root path of the Cloud Storage bucket to export the metadata
   * to, in the format gs://{bucket}/. You can optionally specify a custom
   * prefix after the bucket name, in the format gs://{bucket}/{prefix}/. The
   * maximum length of the custom prefix is 128 characters. Dataplex Universal
   * Catalog constructs the object path for the exported files by using the
   * bucket name and prefix that you provide, followed by a system-generated
   * path.The bucket must be in the same VPC Service Controls perimeter as the
   * job.
   *
   * @var string
   */
  public $outputPath;
  protected $scopeType = GoogleCloudDataplexV1MetadataJobExportJobSpecExportJobScope::class;
  protected $scopeDataType = '';

  /**
   * Required. The root path of the Cloud Storage bucket to export the metadata
   * to, in the format gs://{bucket}/. You can optionally specify a custom
   * prefix after the bucket name, in the format gs://{bucket}/{prefix}/. The
   * maximum length of the custom prefix is 128 characters. Dataplex Universal
   * Catalog constructs the object path for the exported files by using the
   * bucket name and prefix that you provide, followed by a system-generated
   * path.The bucket must be in the same VPC Service Controls perimeter as the
   * job.
   *
   * @param string $outputPath
   */
  public function setOutputPath($outputPath)
  {
    $this->outputPath = $outputPath;
  }
  /**
   * @return string
   */
  public function getOutputPath()
  {
    return $this->outputPath;
  }
  /**
   * Required. The scope of the export job.
   *
   * @param GoogleCloudDataplexV1MetadataJobExportJobSpecExportJobScope $scope
   */
  public function setScope(GoogleCloudDataplexV1MetadataJobExportJobSpecExportJobScope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJobExportJobSpecExportJobScope
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1MetadataJobExportJobSpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1MetadataJobExportJobSpec');
