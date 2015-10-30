<?php
/*
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


  /**
   * The "urlchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $urlchannels = $adsensehostService->urlchannels;
   *  </code>
   */
  class Google_UrlchannelsServiceResource extends Google_ServiceResource {


    /**
     * Add a new URL channel to the host AdSense account. (urlchannels.insert)
     *
     * @param string $adClientId Ad client to which the new URL channel will be added.
     * @param Google_UrlChannel $postBody
     * @param array $optParams Optional parameters.
     * @return Google_UrlChannel
     */
    public function insert($adClientId, Google_UrlChannel $postBody, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_UrlChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * List all host URL channels in the host AdSense account. (urlchannels.list)
     *
     * @param string $adClientId Ad client for which to list URL channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through URL channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param string maxResults The maximum number of URL channels to include in the response, used for paging.
     * @return Google_UrlChannels
     */
    public function listUrlchannels($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_UrlChannels($data);
      } else {
        return $data;
      }
    }
    /**
     * Delete a URL channel from the host AdSense account. (urlchannels.delete)
     *
     * @param string $adClientId Ad client from which to delete the URL channel.
     * @param string $urlChannelId URL channel to delete.
     * @param array $optParams Optional parameters.
     * @return Google_UrlChannel
     */
    public function delete($adClientId, $urlChannelId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'urlChannelId' => $urlChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      if ($this->useObjects()) {
        return new Google_UrlChannel($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "adclients" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $adclients = $adsensehostService->adclients;
   *  </code>
   */
  class Google_AdclientsServiceResource extends Google_ServiceResource {


    /**
     * List all host ad clients in this AdSense account. (adclients.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through ad clients. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param string maxResults The maximum number of ad clients to include in the response, used for paging.
     * @return Google_AdClients
     */
    public function listAdclients($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdClients($data);
      } else {
        return $data;
      }
    }
    /**
     * Get information about one of the ad clients in the Host AdSense account. (adclients.get)
     *
     * @param string $adClientId Ad client to get.
     * @param array $optParams Optional parameters.
     * @return Google_AdClient
     */
    public function get($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AdClient($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "associationsessions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $associationsessions = $adsensehostService->associationsessions;
   *  </code>
   */
  class Google_AssociationsessionsServiceResource extends Google_ServiceResource {


    /**
     * Create an association session for initiating an association with an AdSense user.
     * (associationsessions.start)
     *
     * @param string $productCode Products to associate with the user.
     * @param string $websiteUrl The URL of the user's hosted website.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string websiteLocale The locale of the user's hosted website.
     * @opt_param string userLocale The preferred locale of the user.
     * @return Google_AssociationSession
     */
    public function start($productCode, $websiteUrl, $optParams = array()) {
      $params = array('productCode' => $productCode, 'websiteUrl' => $websiteUrl);
      $params = array_merge($params, $optParams);
      $data = $this->__call('start', array($params));
      if ($this->useObjects()) {
        return new Google_AssociationSession($data);
      } else {
        return $data;
      }
    }
    /**
     * Verify an association session after the association callback returns from AdSense signup.
     * (associationsessions.verify)
     *
     * @param string $token The token returned to the association callback URL.
     * @param array $optParams Optional parameters.
     * @return Google_AssociationSession
     */
    public function verify($token, $optParams = array()) {
      $params = array('token' => $token);
      $params = array_merge($params, $optParams);
      $data = $this->__call('verify', array($params));
      if ($this->useObjects()) {
        return new Google_AssociationSession($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "reports" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $reports = $adsensehostService->reports;
   *  </code>
   */
  class Google_ReportsServiceResource extends Google_ServiceResource {


    /**
     * Generate an AdSense report based on the report request sent in the query parameters. Returns the
     * result as JSON; to retrieve output in CSV format specify "alt=csv" as a query parameter.
     * (reports.generate)
     *
     * @param string $endDate End of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param string $startDate Start of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string sort The name of a dimension or metric to sort the resulting report on, optionally prefixed with "+" to sort ascending or "-" to sort descending. If no prefix is specified, the column is sorted ascending.
     * @opt_param string locale Optional locale to use for translating report output to a local language. Defaults to "en_US" if not specified.
     * @opt_param string metric Numeric columns to include in the report.
     * @opt_param string maxResults The maximum number of rows of report data to return.
     * @opt_param string filter Filters to be run on the report.
     * @opt_param string startIndex Index of the first row of report data to return.
     * @opt_param string dimension Dimensions to base the report on.
     * @return Google_Report
     */
    public function generate($endDate, $startDate, $optParams = array()) {
      $params = array('endDate' => $endDate, 'startDate' => $startDate);
      $params = array_merge($params, $optParams);
      $data = $this->__call('generate', array($params));
      if ($this->useObjects()) {
        return new Google_Report($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "accounts" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $accounts = $adsensehostService->accounts;
   *  </code>
   */
  class Google_AccountsServiceResource extends Google_ServiceResource {


    /**
     * List hosted accounts associated with this AdSense account by ad client id. (accounts.list)
     *
     * @param string $filterAdClientId Ad clients to list accounts for.
     * @param array $optParams Optional parameters.
     * @return Google_Accounts
     */
    public function listAccounts($filterAdClientId, $optParams = array()) {
      $params = array('filterAdClientId' => $filterAdClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Accounts($data);
      } else {
        return $data;
      }
    }
    /**
     * Get information about the selected associated AdSense account. (accounts.get)
     *
     * @param string $accountId Account to get information about.
     * @param array $optParams Optional parameters.
     * @return Google_Account
     */
    public function get($accountId, $optParams = array()) {
      $params = array('accountId' => $accountId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Account($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "adunits" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $adunits = $adsensehostService->adunits;
   *  </code>
   */
  class Google_AccountsAdunitsServiceResource extends Google_ServiceResource {


    /**
     * Insert the supplied ad unit into the specified publisher AdSense account. (adunits.insert)
     *
     * @param string $accountId Account which will contain the ad unit.
     * @param string $adClientId Ad client into which to insert the ad unit.
     * @param Google_AdUnit $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function insert($accountId, $adClientId, Google_AdUnit $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
    /**
     * Get the specified host ad unit in this AdSense account. (adunits.get)
     *
     * @param string $accountId Account which contains the ad unit.
     * @param string $adClientId Ad client for which to get ad unit.
     * @param string $adUnitId Ad unit to get.
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function get($accountId, $adClientId, $adUnitId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
    /**
     * Get ad code for the specified ad unit, attaching the specified host custom channels.
     * (adunits.getAdCode)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client with contains the ad unit.
     * @param string $adUnitId Ad unit to get the code for.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string hostCustomChannelId Host custom channel to attach to the ad code.
     * @return Google_AdCode
     */
    public function getAdCode($accountId, $adClientId, $adUnitId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('getAdCode', array($params));
      if ($this->useObjects()) {
        return new Google_AdCode($data);
      } else {
        return $data;
      }
    }
    /**
     * List all ad units in the specified publisher's AdSense account. (adunits.list)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client for which to list ad units.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeInactive Whether to include inactive ad units. Default: true.
     * @opt_param string pageToken A continuation token, used to page through ad units. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param string maxResults The maximum number of ad units to include in the response, used for paging.
     * @return Google_AdUnits
     */
    public function listAccountsAdunits($accountId, $adClientId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnits($data);
      } else {
        return $data;
      }
    }
    /**
     * Update the supplied ad unit in the specified publisher AdSense account. (adunits.update)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client which contains the ad unit.
     * @param Google_AdUnit $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function update($accountId, $adClientId, Google_AdUnit $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
    /**
     * Update the supplied ad unit in the specified publisher AdSense account. This method supports
     * patch semantics. (adunits.patch)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client which contains the ad unit.
     * @param string $adUnitId Ad unit to get.
     * @param Google_AdUnit $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function patch($accountId, $adClientId, $adUnitId, Google_AdUnit $postBody, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
    /**
     * Delete the specified ad unit from the specified publisher AdSense account. (adunits.delete)
     *
     * @param string $accountId Account which contains the ad unit.
     * @param string $adClientId Ad client for which to get ad unit.
     * @param string $adUnitId Ad unit to delete.
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function delete($accountId, $adClientId, $adUnitId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "adclients" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $adclients = $adsensehostService->adclients;
   *  </code>
   */
  class Google_AccountsAdclientsServiceResource extends Google_ServiceResource {


    /**
     * List all hosted ad clients in the specified hosted account. (adclients.list)
     *
     * @param string $accountId Account for which to list ad clients.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through ad clients. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param string maxResults The maximum number of ad clients to include in the response, used for paging.
     * @return Google_AdClients
     */
    public function listAccountsAdclients($accountId, $optParams = array()) {
      $params = array('accountId' => $accountId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdClients($data);
      } else {
        return $data;
      }
    }
    /**
     * Get information about one of the ad clients in the specified publisher's AdSense account.
     * (adclients.get)
     *
     * @param string $accountId Account which contains the ad client.
     * @param string $adClientId Ad client to get.
     * @param array $optParams Optional parameters.
     * @return Google_AdClient
     */
    public function get($accountId, $adClientId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AdClient($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "reports" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $reports = $adsensehostService->reports;
   *  </code>
   */
  class Google_AccountsReportsServiceResource extends Google_ServiceResource {


    /**
     * Generate an AdSense report based on the report request sent in the query parameters. Returns the
     * result as JSON; to retrieve output in CSV format specify "alt=csv" as a query parameter.
     * (reports.generate)
     *
     * @param string $accountId Hosted account upon which to report.
     * @param string $startDate Start of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param string $endDate End of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string sort The name of a dimension or metric to sort the resulting report on, optionally prefixed with "+" to sort ascending or "-" to sort descending. If no prefix is specified, the column is sorted ascending.
     * @opt_param string locale Optional locale to use for translating report output to a local language. Defaults to "en_US" if not specified.
     * @opt_param string metric Numeric columns to include in the report.
     * @opt_param string maxResults The maximum number of rows of report data to return.
     * @opt_param string filter Filters to be run on the report.
     * @opt_param string startIndex Index of the first row of report data to return.
     * @opt_param string dimension Dimensions to base the report on.
     * @return Google_Report
     */
    public function generate($accountId, $startDate, $endDate, $optParams = array()) {
      $params = array('accountId' => $accountId, 'startDate' => $startDate, 'endDate' => $endDate);
      $params = array_merge($params, $optParams);
      $data = $this->__call('generate', array($params));
      if ($this->useObjects()) {
        return new Google_Report($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "customchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsensehostService = new Google_AdsensehostService(...);
   *   $customchannels = $adsensehostService->customchannels;
   *  </code>
   */
  class Google_CustomchannelsServiceResource extends Google_ServiceResource {


    /**
     * Add a new custom channel to the host AdSense account. (customchannels.insert)
     *
     * @param string $adClientId Ad client to which the new custom channel will be added.
     * @param Google_CustomChannel $postBody
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function insert($adClientId, Google_CustomChannel $postBody, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * Get a specific custom channel from the host AdSense account. (customchannels.get)
     *
     * @param string $adClientId Ad client from which to get the custom channel.
     * @param string $customChannelId Custom channel to get.
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function get($adClientId, $customChannelId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'customChannelId' => $customChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * List all host custom channels in this AdSense account. (customchannels.list)
     *
     * @param string $adClientId Ad client for which to list custom channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through custom channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param string maxResults The maximum number of custom channels to include in the response, used for paging.
     * @return Google_CustomChannels
     */
    public function listCustomchannels($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannels($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a custom channel in the host AdSense account. (customchannels.update)
     *
     * @param string $adClientId Ad client in which the custom channel will be updated.
     * @param Google_CustomChannel $postBody
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function update($adClientId, Google_CustomChannel $postBody, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a custom channel in the host AdSense account. This method supports patch semantics.
     * (customchannels.patch)
     *
     * @param string $adClientId Ad client in which the custom channel will be updated.
     * @param string $customChannelId Custom channel to get.
     * @param Google_CustomChannel $postBody
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function patch($adClientId, $customChannelId, Google_CustomChannel $postBody, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'customChannelId' => $customChannelId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
    /**
     * Delete a specific custom channel from the host AdSense account. (customchannels.delete)
     *
     * @param string $adClientId Ad client from which to delete the custom channel.
     * @param string $customChannelId Custom channel to delete.
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function delete($adClientId, $customChannelId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'customChannelId' => $customChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Adsensehost (v4.1).
 *
 * <p>
 * Gives AdSense Hosts access to report generation, ad code generation, and publisher management capabilities.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/adsense/host/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_AdsensehostService extends Google_Service {
  public $urlchannels;
  public $adclients;
  public $associationsessions;
  public $reports;
  public $accounts;
  public $accounts_adunits;
  public $accounts_adclients;
  public $accounts_reports;
  public $customchannels;
  /**
   * Constructs the internal representation of the Adsensehost service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'adsensehost/v4.1/';
    $this->version = 'v4.1';
    $this->serviceName = 'adsensehost';

    $client->addService($this->serviceName, $this->version);
    $this->urlchannels = new Google_UrlchannelsServiceResource($this, $this->serviceName, 'urlchannels', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "UrlChannel"}, "response": {"$ref": "UrlChannel"}, "httpMethod": "POST", "path": "adclients/{adClientId}/urlchannels", "id": "adsensehost.urlchannels.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "uint32"}}, "id": "adsensehost.urlchannels.list", "httpMethod": "GET", "path": "adclients/{adClientId}/urlchannels", "response": {"$ref": "UrlChannels"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "urlChannelId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.urlchannels.delete", "httpMethod": "DELETE", "path": "adclients/{adClientId}/urlchannels/{urlChannelId}", "response": {"$ref": "UrlChannel"}}}}', true));
    $this->adclients = new Google_AdclientsServiceResource($this, $this->serviceName, 'adclients', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "uint32"}}, "response": {"$ref": "AdClients"}, "httpMethod": "GET", "path": "adclients", "id": "adsensehost.adclients.list"}, "get": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.adclients.get", "httpMethod": "GET", "path": "adclients/{adClientId}", "response": {"$ref": "AdClient"}}}}', true));
    $this->associationsessions = new Google_AssociationsessionsServiceResource($this, $this->serviceName, 'associationsessions', json_decode('{"methods": {"start": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"websiteLocale": {"type": "string", "location": "query"}, "productCode": {"repeated": true, "required": true, "type": "string", "location": "query", "enum": ["AFC", "AFF", "AFMC", "AFS"]}, "userLocale": {"type": "string", "location": "query"}, "websiteUrl": {"required": true, "type": "string", "location": "query"}}, "id": "adsensehost.associationsessions.start", "httpMethod": "GET", "path": "associationsessions/start", "response": {"$ref": "AssociationSession"}}, "verify": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"token": {"required": true, "type": "string", "location": "query"}}, "id": "adsensehost.associationsessions.verify", "httpMethod": "GET", "path": "associationsessions/verify", "response": {"$ref": "AssociationSession"}}}}', true));
    $this->reports = new Google_ReportsServiceResource($this, $this->serviceName, 'reports', json_decode('{"methods": {"generate": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"sort": {"repeated": true, "type": "string", "location": "query"}, "startDate": {"required": true, "type": "string", "location": "query"}, "endDate": {"required": true, "type": "string", "location": "query"}, "locale": {"type": "string", "location": "query"}, "metric": {"repeated": true, "type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "50000", "format": "uint32"}, "filter": {"repeated": true, "type": "string", "location": "query"}, "startIndex": {"location": "query", "minimum": "0", "type": "integer", "maximum": "5000", "format": "uint32"}, "dimension": {"repeated": true, "type": "string", "location": "query"}}, "id": "adsensehost.reports.generate", "httpMethod": "GET", "path": "reports", "response": {"$ref": "Report"}}}}', true));
    $this->accounts = new Google_AccountsServiceResource($this, $this->serviceName, 'accounts', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"filterAdClientId": {"repeated": true, "required": true, "type": "string", "location": "query"}}, "id": "adsensehost.accounts.list", "httpMethod": "GET", "path": "accounts", "response": {"$ref": "Accounts"}}, "get": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.accounts.get", "httpMethod": "GET", "path": "accounts/{accountId}", "response": {"$ref": "Account"}}}}', true));
    $this->accounts_adunits = new Google_AccountsAdunitsServiceResource($this, $this->serviceName, 'adunits', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "AdUnit"}, "response": {"$ref": "AdUnit"}, "httpMethod": "POST", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "id": "adsensehost.accounts.adunits.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "adUnitId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.accounts.adunits.get", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/adunits/{adUnitId}", "response": {"$ref": "AdUnit"}}, "getAdCode": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "hostCustomChannelId": {"repeated": true, "type": "string", "location": "query"}, "adUnitId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.accounts.adunits.getAdCode", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/adunits/{adUnitId}/adcode", "response": {"$ref": "AdCode"}}, "list": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"includeInactive": {"type": "boolean", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "uint32"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.accounts.adunits.list", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "response": {"$ref": "AdUnits"}}, "update": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "AdUnit"}, "response": {"$ref": "AdUnit"}, "httpMethod": "PUT", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "id": "adsensehost.accounts.adunits.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "adUnitId": {"required": true, "type": "string", "location": "query"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "AdUnit"}, "response": {"$ref": "AdUnit"}, "httpMethod": "PATCH", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "id": "adsensehost.accounts.adunits.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "adUnitId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.accounts.adunits.delete", "httpMethod": "DELETE", "path": "accounts/{accountId}/adclients/{adClientId}/adunits/{adUnitId}", "response": {"$ref": "AdUnit"}}}}', true));
    $this->accounts_adclients = new Google_AccountsAdclientsServiceResource($this, $this->serviceName, 'adclients', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "uint32"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.accounts.adclients.list", "httpMethod": "GET", "path": "accounts/{accountId}/adclients", "response": {"$ref": "AdClients"}}, "get": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.accounts.adclients.get", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}", "response": {"$ref": "AdClient"}}}}', true));
    $this->accounts_reports = new Google_AccountsReportsServiceResource($this, $this->serviceName, 'reports', json_decode('{"methods": {"generate": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"sort": {"repeated": true, "type": "string", "location": "query"}, "startDate": {"required": true, "type": "string", "location": "query"}, "endDate": {"required": true, "type": "string", "location": "query"}, "locale": {"type": "string", "location": "query"}, "metric": {"repeated": true, "type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "50000", "format": "uint32"}, "filter": {"repeated": true, "type": "string", "location": "query"}, "startIndex": {"location": "query", "minimum": "0", "type": "integer", "maximum": "5000", "format": "uint32"}, "dimension": {"repeated": true, "type": "string", "location": "query"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.accounts.reports.generate", "httpMethod": "GET", "path": "accounts/{accountId}/reports", "response": {"$ref": "Report"}}}}', true));
    $this->customchannels = new Google_CustomchannelsServiceResource($this, $this->serviceName, 'customchannels', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "CustomChannel"}, "response": {"$ref": "CustomChannel"}, "httpMethod": "POST", "path": "adclients/{adClientId}/customchannels", "id": "adsensehost.customchannels.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"customChannelId": {"required": true, "type": "string", "location": "path"}, "adClientId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.customchannels.get", "httpMethod": "GET", "path": "adclients/{adClientId}/customchannels/{customChannelId}", "response": {"$ref": "CustomChannel"}}, "list": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "uint32"}}, "id": "adsensehost.customchannels.list", "httpMethod": "GET", "path": "adclients/{adClientId}/customchannels", "response": {"$ref": "CustomChannels"}}, "update": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "CustomChannel"}, "response": {"$ref": "CustomChannel"}, "httpMethod": "PUT", "path": "adclients/{adClientId}/customchannels", "id": "adsensehost.customchannels.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"customChannelId": {"required": true, "type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "CustomChannel"}, "response": {"$ref": "CustomChannel"}, "httpMethod": "PATCH", "path": "adclients/{adClientId}/customchannels", "id": "adsensehost.customchannels.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/adsensehost"], "parameters": {"customChannelId": {"required": true, "type": "string", "location": "path"}, "adClientId": {"required": true, "type": "string", "location": "path"}}, "id": "adsensehost.customchannels.delete", "httpMethod": "DELETE", "path": "adclients/{adClientId}/customchannels/{customChannelId}", "response": {"$ref": "CustomChannel"}}}}', true));

  }
}

class Google_Account extends Google_Model {
  public $status;
  public $kind;
  public $id;
  public $name;
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_Accounts extends Google_Model {
  protected $__itemsType = 'Google_Account';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_Account', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_AdClient extends Google_Model {
  public $productCode;
  public $kind;
  public $id;
  public $arcOptIn;
  public $supportsReporting;
  public function setProductCode($productCode) {
    $this->productCode = $productCode;
  }
  public function getProductCode() {
    return $this->productCode;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setArcOptIn($arcOptIn) {
    $this->arcOptIn = $arcOptIn;
  }
  public function getArcOptIn() {
    return $this->arcOptIn;
  }
  public function setSupportsReporting($supportsReporting) {
    $this->supportsReporting = $supportsReporting;
  }
  public function getSupportsReporting() {
    return $this->supportsReporting;
  }
}

class Google_AdClients extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_AdClient';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_AdClient', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_AdCode extends Google_Model {
  public $adCode;
  public $kind;
  public function setAdCode($adCode) {
    $this->adCode = $adCode;
  }
  public function getAdCode() {
    return $this->adCode;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_AdStyle extends Google_Model {
  public $corners;
  protected $__colorsType = 'Google_AdStyleColors';
  protected $__colorsDataType = '';
  public $colors;
  protected $__fontType = 'Google_AdStyleFont';
  protected $__fontDataType = '';
  public $font;
  public $kind;
  public function setCorners($corners) {
    $this->corners = $corners;
  }
  public function getCorners() {
    return $this->corners;
  }
  public function setColors(Google_AdStyleColors $colors) {
    $this->colors = $colors;
  }
  public function getColors() {
    return $this->colors;
  }
  public function setFont(Google_AdStyleFont $font) {
    $this->font = $font;
  }
  public function getFont() {
    return $this->font;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_AdStyleColors extends Google_Model {
  public $url;
  public $text;
  public $border;
  public $background;
  public $title;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setText($text) {
    $this->text = $text;
  }
  public function getText() {
    return $this->text;
  }
  public function setBorder($border) {
    $this->border = $border;
  }
  public function getBorder() {
    return $this->border;
  }
  public function setBackground($background) {
    $this->background = $background;
  }
  public function getBackground() {
    return $this->background;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
}

class Google_AdStyleFont extends Google_Model {
  public $family;
  public $size;
  public function setFamily($family) {
    $this->family = $family;
  }
  public function getFamily() {
    return $this->family;
  }
  public function setSize($size) {
    $this->size = $size;
  }
  public function getSize() {
    return $this->size;
  }
}

class Google_AdUnit extends Google_Model {
  public $status;
  public $kind;
  public $code;
  public $name;
  protected $__contentAdsSettingsType = 'Google_AdUnitContentAdsSettings';
  protected $__contentAdsSettingsDataType = '';
  public $contentAdsSettings;
  public $id;
  protected $__mobileContentAdsSettingsType = 'Google_AdUnitMobileContentAdsSettings';
  protected $__mobileContentAdsSettingsDataType = '';
  public $mobileContentAdsSettings;
  protected $__customStyleType = 'Google_AdStyle';
  protected $__customStyleDataType = '';
  public $customStyle;
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setCode($code) {
    $this->code = $code;
  }
  public function getCode() {
    return $this->code;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setContentAdsSettings(Google_AdUnitContentAdsSettings $contentAdsSettings) {
    $this->contentAdsSettings = $contentAdsSettings;
  }
  public function getContentAdsSettings() {
    return $this->contentAdsSettings;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setMobileContentAdsSettings(Google_AdUnitMobileContentAdsSettings $mobileContentAdsSettings) {
    $this->mobileContentAdsSettings = $mobileContentAdsSettings;
  }
  public function getMobileContentAdsSettings() {
    return $this->mobileContentAdsSettings;
  }
  public function setCustomStyle(Google_AdStyle $customStyle) {
    $this->customStyle = $customStyle;
  }
  public function getCustomStyle() {
    return $this->customStyle;
  }
}

class Google_AdUnitContentAdsSettings extends Google_Model {
  public $type;
  protected $__backupOptionType = 'Google_AdUnitContentAdsSettingsBackupOption';
  protected $__backupOptionDataType = '';
  public $backupOption;
  public $size;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setBackupOption(Google_AdUnitContentAdsSettingsBackupOption $backupOption) {
    $this->backupOption = $backupOption;
  }
  public function getBackupOption() {
    return $this->backupOption;
  }
  public function setSize($size) {
    $this->size = $size;
  }
  public function getSize() {
    return $this->size;
  }
}

class Google_AdUnitContentAdsSettingsBackupOption extends Google_Model {
  public $color;
  public $url;
  public $type;
  public function setColor($color) {
    $this->color = $color;
  }
  public function getColor() {
    return $this->color;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_AdUnitMobileContentAdsSettings extends Google_Model {
  public $scriptingLanguage;
  public $type;
  public $markupLanguage;
  public $size;
  public function setScriptingLanguage($scriptingLanguage) {
    $this->scriptingLanguage = $scriptingLanguage;
  }
  public function getScriptingLanguage() {
    return $this->scriptingLanguage;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setMarkupLanguage($markupLanguage) {
    $this->markupLanguage = $markupLanguage;
  }
  public function getMarkupLanguage() {
    return $this->markupLanguage;
  }
  public function setSize($size) {
    $this->size = $size;
  }
  public function getSize() {
    return $this->size;
  }
}

class Google_AdUnits extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_AdUnit';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_AdUnit', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_AssociationSession extends Google_Model {
  public $status;
  public $productCodes;
  public $kind;
  public $userLocale;
  public $websiteLocale;
  public $redirectUrl;
  public $websiteUrl;
  public $id;
  public $accountId;
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setProductCodes($productCodes) {
    $this->productCodes = $productCodes;
  }
  public function getProductCodes() {
    return $this->productCodes;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setUserLocale($userLocale) {
    $this->userLocale = $userLocale;
  }
  public function getUserLocale() {
    return $this->userLocale;
  }
  public function setWebsiteLocale($websiteLocale) {
    $this->websiteLocale = $websiteLocale;
  }
  public function getWebsiteLocale() {
    return $this->websiteLocale;
  }
  public function setRedirectUrl($redirectUrl) {
    $this->redirectUrl = $redirectUrl;
  }
  public function getRedirectUrl() {
    return $this->redirectUrl;
  }
  public function setWebsiteUrl($websiteUrl) {
    $this->websiteUrl = $websiteUrl;
  }
  public function getWebsiteUrl() {
    return $this->websiteUrl;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class Google_CustomChannel extends Google_Model {
  public $kind;
  public $code;
  public $id;
  public $name;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setCode($code) {
    $this->code = $code;
  }
  public function getCode() {
    return $this->code;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_CustomChannels extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_CustomChannel';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_CustomChannel', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_Report extends Google_Model {
  public $kind;
  public $rows;
  public $warnings;
  public $totals;
  protected $__headersType = 'Google_ReportHeaders';
  protected $__headersDataType = 'array';
  public $headers;
  public $totalMatchedRows;
  public $averages;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRows($rows) {
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setWarnings($warnings) {
    $this->warnings = $warnings;
  }
  public function getWarnings() {
    return $this->warnings;
  }
  public function setTotals($totals) {
    $this->totals = $totals;
  }
  public function getTotals() {
    return $this->totals;
  }
  public function setHeaders($headers) {
    $this->assertIsArray($headers, 'Google_ReportHeaders', __METHOD__);
    $this->headers = $headers;
  }
  public function getHeaders() {
    return $this->headers;
  }
  public function setTotalMatchedRows($totalMatchedRows) {
    $this->totalMatchedRows = $totalMatchedRows;
  }
  public function getTotalMatchedRows() {
    return $this->totalMatchedRows;
  }
  public function setAverages($averages) {
    $this->averages = $averages;
  }
  public function getAverages() {
    return $this->averages;
  }
}

class Google_ReportHeaders extends Google_Model {
  public $currency;
  public $type;
  public $name;
  public function setCurrency($currency) {
    $this->currency = $currency;
  }
  public function getCurrency() {
    return $this->currency;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_UrlChannel extends Google_Model {
  public $kind;
  public $id;
  public $urlPattern;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setUrlPattern($urlPattern) {
    $this->urlPattern = $urlPattern;
  }
  public function getUrlPattern() {
    return $this->urlPattern;
  }
}

class Google_UrlChannels extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_UrlChannel';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_UrlChannel', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}
