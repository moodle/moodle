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

namespace Google\Service\AnalyticsHub\Resource;

use Google\Service\AnalyticsHub\AnalyticshubEmpty;
use Google\Service\AnalyticsHub\ApproveQueryTemplateRequest;
use Google\Service\AnalyticsHub\ListQueryTemplatesResponse;
use Google\Service\AnalyticsHub\QueryTemplate;
use Google\Service\AnalyticsHub\SubmitQueryTemplateRequest;

/**
 * The "queryTemplates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $analyticshubService = new Google\Service\AnalyticsHub(...);
 *   $queryTemplates = $analyticshubService->projects_locations_dataExchanges_queryTemplates;
 *  </code>
 */
class ProjectsLocationsDataExchangesQueryTemplates extends \Google\Service\Resource
{
  /**
   * Approves a query template. (queryTemplates.approve)
   *
   * @param string $name Required. The resource path of the QueryTemplate. e.g. `p
   * rojects/myproject/locations/us/dataExchanges/123/queryTemplates/myqueryTempla
   * te`.
   * @param ApproveQueryTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return QueryTemplate
   * @throws \Google\Service\Exception
   */
  public function approve($name, ApproveQueryTemplateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('approve', [$params], QueryTemplate::class);
  }
  /**
   * Creates a new QueryTemplate (queryTemplates.create)
   *
   * @param string $parent Required. The parent resource path of the
   * QueryTemplate. e.g. `projects/myproject/locations/us/dataExchanges/123/queryT
   * emplates/myQueryTemplate`.
   * @param QueryTemplate $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string queryTemplateId Required. The ID of the QueryTemplate to
   * create. Must contain only Unicode letters, numbers (0-9), underscores (_).
   * Max length: 100 bytes.
   * @return QueryTemplate
   * @throws \Google\Service\Exception
   */
  public function create($parent, QueryTemplate $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], QueryTemplate::class);
  }
  /**
   * Deletes a query template. (queryTemplates.delete)
   *
   * @param string $name Required. The resource path of the QueryTemplate. e.g. `p
   * rojects/myproject/locations/us/dataExchanges/123/queryTemplates/myqueryTempla
   * te`.
   * @param array $optParams Optional parameters.
   * @return AnalyticshubEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], AnalyticshubEmpty::class);
  }
  /**
   * Gets a QueryTemplate (queryTemplates.get)
   *
   * @param string $name Required. The parent resource path of the QueryTemplate.
   * e.g. `projects/myproject/locations/us/dataExchanges/123/queryTemplates/myquer
   * yTemplate`.
   * @param array $optParams Optional parameters.
   * @return QueryTemplate
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], QueryTemplate::class);
  }
  /**
   * Lists all QueryTemplates in a given project and location.
   * (queryTemplates.listProjectsLocationsDataExchangesQueryTemplates)
   *
   * @param string $parent Required. The parent resource path of the
   * QueryTemplates. e.g. `projects/myproject/locations/us/dataExchanges/123`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of results to return in
   * a single response page. Leverage the page tokens to iterate through the
   * entire collection.
   * @opt_param string pageToken Optional. Page token, returned by a previous
   * call, to request the next page of results.
   * @return ListQueryTemplatesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDataExchangesQueryTemplates($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListQueryTemplatesResponse::class);
  }
  /**
   * Updates an existing QueryTemplate (queryTemplates.patch)
   *
   * @param string $name Output only. The resource name of the QueryTemplate. e.g.
   * `projects/myproject/locations/us/dataExchanges/123/queryTemplates/456`
   * @param QueryTemplate $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask specifies the fields to
   * update in the query template resource. The fields specified in the
   * `updateMask` are relative to the resource and are not a full request.
   * @return QueryTemplate
   * @throws \Google\Service\Exception
   */
  public function patch($name, QueryTemplate $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], QueryTemplate::class);
  }
  /**
   * Submits a query template for approval. (queryTemplates.submit)
   *
   * @param string $name Required. The resource path of the QueryTemplate. e.g. `p
   * rojects/myproject/locations/us/dataExchanges/123/queryTemplates/myqueryTempla
   * te`.
   * @param SubmitQueryTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return QueryTemplate
   * @throws \Google\Service\Exception
   */
  public function submit($name, SubmitQueryTemplateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('submit', [$params], QueryTemplate::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDataExchangesQueryTemplates::class, 'Google_Service_AnalyticsHub_Resource_ProjectsLocationsDataExchangesQueryTemplates');
