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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ExportTestCasesRequest extends \Google\Model
{
  /**
   * Unspecified format.
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Raw bytes.
   */
  public const DATA_FORMAT_BLOB = 'BLOB';
  /**
   * JSON format.
   */
  public const DATA_FORMAT_JSON = 'JSON';
  /**
   * The data format of the exported test cases. If not specified, `BLOB` is
   * assumed.
   *
   * @var string
   */
  public $dataFormat;
  /**
   * The filter expression used to filter exported test cases, see [API
   * Filtering](https://aip.dev/160). The expression is case insensitive and
   * supports the following syntax: name = [OR name = ] ... For example: * "name
   * = t1 OR name = t2" matches the test case with the exact resource name "t1"
   * or "t2".
   *
   * @var string
   */
  public $filter;
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * export the test cases to. The format of this URI must be `gs:`. If
   * unspecified, the serialized test cases is returned inline. Dialogflow
   * performs a write operation for the Cloud Storage object on the caller's
   * behalf, so your request authentication must have write permissions for the
   * object. For more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $gcsUri;

  /**
   * The data format of the exported test cases. If not specified, `BLOB` is
   * assumed.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, BLOB, JSON
   *
   * @param self::DATA_FORMAT_* $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return self::DATA_FORMAT_*
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * The filter expression used to filter exported test cases, see [API
   * Filtering](https://aip.dev/160). The expression is case insensitive and
   * supports the following syntax: name = [OR name = ] ... For example: * "name
   * = t1 OR name = t2" matches the test case with the exact resource name "t1"
   * or "t2".
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * export the test cases to. The format of this URI must be `gs:`. If
   * unspecified, the serialized test cases is returned inline. Dialogflow
   * performs a write operation for the Cloud Storage object on the caller's
   * behalf, so your request authentication must have write permissions for the
   * object. For more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportTestCasesRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportTestCasesRequest');
