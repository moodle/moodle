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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ApiDocDocumentation extends \Google\Model
{
  protected $graphqlDocumentationType = GoogleCloudApigeeV1GraphqlDocumentation::class;
  protected $graphqlDocumentationDataType = '';
  protected $oasDocumentationType = GoogleCloudApigeeV1OASDocumentation::class;
  protected $oasDocumentationDataType = '';

  /**
   * Optional. GraphQL documentation.
   *
   * @param GoogleCloudApigeeV1GraphqlDocumentation $graphqlDocumentation
   */
  public function setGraphqlDocumentation(GoogleCloudApigeeV1GraphqlDocumentation $graphqlDocumentation)
  {
    $this->graphqlDocumentation = $graphqlDocumentation;
  }
  /**
   * @return GoogleCloudApigeeV1GraphqlDocumentation
   */
  public function getGraphqlDocumentation()
  {
    return $this->graphqlDocumentation;
  }
  /**
   * Optional. OpenAPI Specification documentation.
   *
   * @param GoogleCloudApigeeV1OASDocumentation $oasDocumentation
   */
  public function setOasDocumentation(GoogleCloudApigeeV1OASDocumentation $oasDocumentation)
  {
    $this->oasDocumentation = $oasDocumentation;
  }
  /**
   * @return GoogleCloudApigeeV1OASDocumentation
   */
  public function getOasDocumentation()
  {
    return $this->oasDocumentation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ApiDocDocumentation::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ApiDocDocumentation');
