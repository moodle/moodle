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

class GoogleCloudAiplatformV1Tool extends \Google\Collection
{
  protected $collection_key = 'functionDeclarations';
  protected $codeExecutionType = GoogleCloudAiplatformV1ToolCodeExecution::class;
  protected $codeExecutionDataType = '';
  protected $computerUseType = GoogleCloudAiplatformV1ToolComputerUse::class;
  protected $computerUseDataType = '';
  protected $enterpriseWebSearchType = GoogleCloudAiplatformV1EnterpriseWebSearch::class;
  protected $enterpriseWebSearchDataType = '';
  protected $functionDeclarationsType = GoogleCloudAiplatformV1FunctionDeclaration::class;
  protected $functionDeclarationsDataType = 'array';
  protected $googleMapsType = GoogleCloudAiplatformV1GoogleMaps::class;
  protected $googleMapsDataType = '';
  protected $googleSearchType = GoogleCloudAiplatformV1ToolGoogleSearch::class;
  protected $googleSearchDataType = '';
  protected $googleSearchRetrievalType = GoogleCloudAiplatformV1GoogleSearchRetrieval::class;
  protected $googleSearchRetrievalDataType = '';
  protected $retrievalType = GoogleCloudAiplatformV1Retrieval::class;
  protected $retrievalDataType = '';
  protected $urlContextType = GoogleCloudAiplatformV1UrlContext::class;
  protected $urlContextDataType = '';

  /**
   * Optional. CodeExecution tool type. Enables the model to execute code as
   * part of generation.
   *
   * @param GoogleCloudAiplatformV1ToolCodeExecution $codeExecution
   */
  public function setCodeExecution(GoogleCloudAiplatformV1ToolCodeExecution $codeExecution)
  {
    $this->codeExecution = $codeExecution;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolCodeExecution
   */
  public function getCodeExecution()
  {
    return $this->codeExecution;
  }
  /**
   * Optional. Tool to support the model interacting directly with the computer.
   * If enabled, it automatically populates computer-use specific Function
   * Declarations.
   *
   * @param GoogleCloudAiplatformV1ToolComputerUse $computerUse
   */
  public function setComputerUse(GoogleCloudAiplatformV1ToolComputerUse $computerUse)
  {
    $this->computerUse = $computerUse;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolComputerUse
   */
  public function getComputerUse()
  {
    return $this->computerUse;
  }
  /**
   * Optional. Tool to support searching public web data, powered by Vertex AI
   * Search and Sec4 compliance.
   *
   * @param GoogleCloudAiplatformV1EnterpriseWebSearch $enterpriseWebSearch
   */
  public function setEnterpriseWebSearch(GoogleCloudAiplatformV1EnterpriseWebSearch $enterpriseWebSearch)
  {
    $this->enterpriseWebSearch = $enterpriseWebSearch;
  }
  /**
   * @return GoogleCloudAiplatformV1EnterpriseWebSearch
   */
  public function getEnterpriseWebSearch()
  {
    return $this->enterpriseWebSearch;
  }
  /**
   * Optional. Function tool type. One or more function declarations to be
   * passed to the model along with the current user query. Model may decide to
   * call a subset of these functions by populating FunctionCall in the
   * response. User should provide a FunctionResponse for each function call in
   * the next turn. Based on the function responses, Model will generate the
   * final response back to the user. Maximum 512 function declarations can be
   * provided.
   *
   * @param GoogleCloudAiplatformV1FunctionDeclaration[] $functionDeclarations
   */
  public function setFunctionDeclarations($functionDeclarations)
  {
    $this->functionDeclarations = $functionDeclarations;
  }
  /**
   * @return GoogleCloudAiplatformV1FunctionDeclaration[]
   */
  public function getFunctionDeclarations()
  {
    return $this->functionDeclarations;
  }
  /**
   * Optional. GoogleMaps tool type. Tool to support Google Maps in Model.
   *
   * @param GoogleCloudAiplatformV1GoogleMaps $googleMaps
   */
  public function setGoogleMaps(GoogleCloudAiplatformV1GoogleMaps $googleMaps)
  {
    $this->googleMaps = $googleMaps;
  }
  /**
   * @return GoogleCloudAiplatformV1GoogleMaps
   */
  public function getGoogleMaps()
  {
    return $this->googleMaps;
  }
  /**
   * Optional. GoogleSearch tool type. Tool to support Google Search in Model.
   * Powered by Google.
   *
   * @param GoogleCloudAiplatformV1ToolGoogleSearch $googleSearch
   */
  public function setGoogleSearch(GoogleCloudAiplatformV1ToolGoogleSearch $googleSearch)
  {
    $this->googleSearch = $googleSearch;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolGoogleSearch
   */
  public function getGoogleSearch()
  {
    return $this->googleSearch;
  }
  /**
   * Optional. Specialized retrieval tool that is powered by Google Search.
   *
   * @deprecated
   * @param GoogleCloudAiplatformV1GoogleSearchRetrieval $googleSearchRetrieval
   */
  public function setGoogleSearchRetrieval(GoogleCloudAiplatformV1GoogleSearchRetrieval $googleSearchRetrieval)
  {
    $this->googleSearchRetrieval = $googleSearchRetrieval;
  }
  /**
   * @deprecated
   * @return GoogleCloudAiplatformV1GoogleSearchRetrieval
   */
  public function getGoogleSearchRetrieval()
  {
    return $this->googleSearchRetrieval;
  }
  /**
   * Optional. Retrieval tool type. System will always execute the provided
   * retrieval tool(s) to get external knowledge to answer the prompt. Retrieval
   * results are presented to the model for generation.
   *
   * @param GoogleCloudAiplatformV1Retrieval $retrieval
   */
  public function setRetrieval(GoogleCloudAiplatformV1Retrieval $retrieval)
  {
    $this->retrieval = $retrieval;
  }
  /**
   * @return GoogleCloudAiplatformV1Retrieval
   */
  public function getRetrieval()
  {
    return $this->retrieval;
  }
  /**
   * Optional. Tool to support URL context retrieval.
   *
   * @param GoogleCloudAiplatformV1UrlContext $urlContext
   */
  public function setUrlContext(GoogleCloudAiplatformV1UrlContext $urlContext)
  {
    $this->urlContext = $urlContext;
  }
  /**
   * @return GoogleCloudAiplatformV1UrlContext
   */
  public function getUrlContext()
  {
    return $this->urlContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Tool::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Tool');
