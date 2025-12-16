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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaQualityMetrics extends \Google\Model
{
  protected $docNdcgType = GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics::class;
  protected $docNdcgDataType = '';
  protected $docPrecisionType = GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics::class;
  protected $docPrecisionDataType = '';
  protected $docRecallType = GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics::class;
  protected $docRecallDataType = '';
  protected $pageNdcgType = GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics::class;
  protected $pageNdcgDataType = '';
  protected $pageRecallType = GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics::class;
  protected $pageRecallDataType = '';

  /**
   * Normalized discounted cumulative gain (NDCG) per document, at various top-k
   * cutoff levels. NDCG measures the ranking quality, giving higher relevance
   * to top results. Example (top-3): Suppose SampleQuery with three retrieved
   * documents (D1, D2, D3) and binary relevance judgements (1 for relevant, 0
   * for not relevant): Retrieved: [D3 (0), D1 (1), D2 (1)] Ideal: [D1 (1), D2
   * (1), D3 (0)] Calculate NDCG@3 for each SampleQuery: * DCG@3: 0/log2(1+1) +
   * 1/log2(2+1) + 1/log2(3+1) = 1.13 * Ideal DCG@3: 1/log2(1+1) + 1/log2(2+1) +
   * 0/log2(3+1) = 1.63 * NDCG@3: 1.13/1.63 = 0.693
   *
   * @param GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $docNdcg
   */
  public function setDocNdcg(GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $docNdcg)
  {
    $this->docNdcg = $docNdcg;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics
   */
  public function getDocNdcg()
  {
    return $this->docNdcg;
  }
  /**
   * Precision per document, at various top-k cutoff levels. Precision is the
   * fraction of retrieved documents that are relevant. Example (top-5): * For a
   * single SampleQuery, If 4 out of 5 retrieved documents in the top-5 are
   * relevant, precision@5 = 4/5 = 0.8
   *
   * @param GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $docPrecision
   */
  public function setDocPrecision(GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $docPrecision)
  {
    $this->docPrecision = $docPrecision;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics
   */
  public function getDocPrecision()
  {
    return $this->docPrecision;
  }
  /**
   * Recall per document, at various top-k cutoff levels. Recall is the fraction
   * of relevant documents retrieved out of all relevant documents. Example
   * (top-5): * For a single SampleQuery, If 3 out of 5 relevant documents are
   * retrieved in the top-5, recall@5 = 3/5 = 0.6
   *
   * @param GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $docRecall
   */
  public function setDocRecall(GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $docRecall)
  {
    $this->docRecall = $docRecall;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics
   */
  public function getDocRecall()
  {
    return $this->docRecall;
  }
  /**
   * Normalized discounted cumulative gain (NDCG) per page, at various top-k
   * cutoff levels. NDCG measures the ranking quality, giving higher relevance
   * to top results. Example (top-3): Suppose SampleQuery with three retrieved
   * pages (P1, P2, P3) and binary relevance judgements (1 for relevant, 0 for
   * not relevant): Retrieved: [P3 (0), P1 (1), P2 (1)] Ideal: [P1 (1), P2 (1),
   * P3 (0)] Calculate NDCG@3 for SampleQuery: * DCG@3: 0/log2(1+1) +
   * 1/log2(2+1) + 1/log2(3+1) = 1.13 * Ideal DCG@3: 1/log2(1+1) + 1/log2(2+1) +
   * 0/log2(3+1) = 1.63 * NDCG@3: 1.13/1.63 = 0.693
   *
   * @param GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $pageNdcg
   */
  public function setPageNdcg(GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $pageNdcg)
  {
    $this->pageNdcg = $pageNdcg;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics
   */
  public function getPageNdcg()
  {
    return $this->pageNdcg;
  }
  /**
   * Recall per page, at various top-k cutoff levels. Recall is the fraction of
   * relevant pages retrieved out of all relevant pages. Example (top-5): * For
   * a single SampleQuery, if 3 out of 5 relevant pages are retrieved in the
   * top-5, recall@5 = 3/5 = 0.6
   *
   * @param GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $pageRecall
   */
  public function setPageRecall(GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics $pageRecall)
  {
    $this->pageRecall = $pageRecall;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaQualityMetricsTopkMetrics
   */
  public function getPageRecall()
  {
    return $this->pageRecall;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaQualityMetrics::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaQualityMetrics');
