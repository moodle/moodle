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

namespace Google\Service\Parallelstore;

class ImportDataRequest extends \Google\Model
{
  protected $destinationParallelstoreType = DestinationParallelstore::class;
  protected $destinationParallelstoreDataType = '';
  protected $metadataOptionsType = TransferMetadataOptions::class;
  protected $metadataOptionsDataType = '';
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes since the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;
  /**
   * Optional. User-specified service account credentials to be used when
   * performing the transfer. Use one of the following formats: *
   * `{EMAIL_ADDRESS_OR_UNIQUE_ID}` * `projects/{PROJECT_ID_OR_NUMBER}/serviceAc
   * counts/{EMAIL_ADDRESS_OR_UNIQUE_ID}` *
   * `projects/-/serviceAccounts/{EMAIL_ADDRESS_OR_UNIQUE_ID}` If unspecified,
   * the Parallelstore service agent is used: `service-@gcp-sa-
   * parallelstore.iam.gserviceaccount.com`
   *
   * @var string
   */
  public $serviceAccount;
  protected $sourceGcsBucketType = SourceGcsBucket::class;
  protected $sourceGcsBucketDataType = '';

  /**
   * Parallelstore destination.
   *
   * @param DestinationParallelstore $destinationParallelstore
   */
  public function setDestinationParallelstore(DestinationParallelstore $destinationParallelstore)
  {
    $this->destinationParallelstore = $destinationParallelstore;
  }
  /**
   * @return DestinationParallelstore
   */
  public function getDestinationParallelstore()
  {
    return $this->destinationParallelstore;
  }
  /**
   * Optional. The transfer metadata options for the import data.
   *
   * @param TransferMetadataOptions $metadataOptions
   */
  public function setMetadataOptions(TransferMetadataOptions $metadataOptions)
  {
    $this->metadataOptions = $metadataOptions;
  }
  /**
   * @return TransferMetadataOptions
   */
  public function getMetadataOptions()
  {
    return $this->metadataOptions;
  }
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes since the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Optional. User-specified service account credentials to be used when
   * performing the transfer. Use one of the following formats: *
   * `{EMAIL_ADDRESS_OR_UNIQUE_ID}` * `projects/{PROJECT_ID_OR_NUMBER}/serviceAc
   * counts/{EMAIL_ADDRESS_OR_UNIQUE_ID}` *
   * `projects/-/serviceAccounts/{EMAIL_ADDRESS_OR_UNIQUE_ID}` If unspecified,
   * the Parallelstore service agent is used: `service-@gcp-sa-
   * parallelstore.iam.gserviceaccount.com`
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * The Cloud Storage source bucket and, optionally, path inside the bucket.
   *
   * @param SourceGcsBucket $sourceGcsBucket
   */
  public function setSourceGcsBucket(SourceGcsBucket $sourceGcsBucket)
  {
    $this->sourceGcsBucket = $sourceGcsBucket;
  }
  /**
   * @return SourceGcsBucket
   */
  public function getSourceGcsBucket()
  {
    return $this->sourceGcsBucket;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportDataRequest::class, 'Google_Service_Parallelstore_ImportDataRequest');
