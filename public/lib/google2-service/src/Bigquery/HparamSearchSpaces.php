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

namespace Google\Service\Bigquery;

class HparamSearchSpaces extends \Google\Model
{
  protected $activationFnType = StringHparamSearchSpace::class;
  protected $activationFnDataType = '';
  protected $batchSizeType = IntHparamSearchSpace::class;
  protected $batchSizeDataType = '';
  protected $boosterTypeType = StringHparamSearchSpace::class;
  protected $boosterTypeDataType = '';
  protected $colsampleBylevelType = DoubleHparamSearchSpace::class;
  protected $colsampleBylevelDataType = '';
  protected $colsampleBynodeType = DoubleHparamSearchSpace::class;
  protected $colsampleBynodeDataType = '';
  protected $colsampleBytreeType = DoubleHparamSearchSpace::class;
  protected $colsampleBytreeDataType = '';
  protected $dartNormalizeTypeType = StringHparamSearchSpace::class;
  protected $dartNormalizeTypeDataType = '';
  protected $dropoutType = DoubleHparamSearchSpace::class;
  protected $dropoutDataType = '';
  protected $hiddenUnitsType = IntArrayHparamSearchSpace::class;
  protected $hiddenUnitsDataType = '';
  protected $l1RegType = DoubleHparamSearchSpace::class;
  protected $l1RegDataType = '';
  protected $l2RegType = DoubleHparamSearchSpace::class;
  protected $l2RegDataType = '';
  protected $learnRateType = DoubleHparamSearchSpace::class;
  protected $learnRateDataType = '';
  protected $maxTreeDepthType = IntHparamSearchSpace::class;
  protected $maxTreeDepthDataType = '';
  protected $minSplitLossType = DoubleHparamSearchSpace::class;
  protected $minSplitLossDataType = '';
  protected $minTreeChildWeightType = IntHparamSearchSpace::class;
  protected $minTreeChildWeightDataType = '';
  protected $numClustersType = IntHparamSearchSpace::class;
  protected $numClustersDataType = '';
  protected $numFactorsType = IntHparamSearchSpace::class;
  protected $numFactorsDataType = '';
  protected $numParallelTreeType = IntHparamSearchSpace::class;
  protected $numParallelTreeDataType = '';
  protected $optimizerType = StringHparamSearchSpace::class;
  protected $optimizerDataType = '';
  protected $subsampleType = DoubleHparamSearchSpace::class;
  protected $subsampleDataType = '';
  protected $treeMethodType = StringHparamSearchSpace::class;
  protected $treeMethodDataType = '';
  protected $walsAlphaType = DoubleHparamSearchSpace::class;
  protected $walsAlphaDataType = '';

  /**
   * Activation functions of neural network models.
   *
   * @param StringHparamSearchSpace $activationFn
   */
  public function setActivationFn(StringHparamSearchSpace $activationFn)
  {
    $this->activationFn = $activationFn;
  }
  /**
   * @return StringHparamSearchSpace
   */
  public function getActivationFn()
  {
    return $this->activationFn;
  }
  /**
   * Mini batch sample size.
   *
   * @param IntHparamSearchSpace $batchSize
   */
  public function setBatchSize(IntHparamSearchSpace $batchSize)
  {
    $this->batchSize = $batchSize;
  }
  /**
   * @return IntHparamSearchSpace
   */
  public function getBatchSize()
  {
    return $this->batchSize;
  }
  /**
   * Booster type for boosted tree models.
   *
   * @param StringHparamSearchSpace $boosterType
   */
  public function setBoosterType(StringHparamSearchSpace $boosterType)
  {
    $this->boosterType = $boosterType;
  }
  /**
   * @return StringHparamSearchSpace
   */
  public function getBoosterType()
  {
    return $this->boosterType;
  }
  /**
   * Subsample ratio of columns for each level for boosted tree models.
   *
   * @param DoubleHparamSearchSpace $colsampleBylevel
   */
  public function setColsampleBylevel(DoubleHparamSearchSpace $colsampleBylevel)
  {
    $this->colsampleBylevel = $colsampleBylevel;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getColsampleBylevel()
  {
    return $this->colsampleBylevel;
  }
  /**
   * Subsample ratio of columns for each node(split) for boosted tree models.
   *
   * @param DoubleHparamSearchSpace $colsampleBynode
   */
  public function setColsampleBynode(DoubleHparamSearchSpace $colsampleBynode)
  {
    $this->colsampleBynode = $colsampleBynode;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getColsampleBynode()
  {
    return $this->colsampleBynode;
  }
  /**
   * Subsample ratio of columns when constructing each tree for boosted tree
   * models.
   *
   * @param DoubleHparamSearchSpace $colsampleBytree
   */
  public function setColsampleBytree(DoubleHparamSearchSpace $colsampleBytree)
  {
    $this->colsampleBytree = $colsampleBytree;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getColsampleBytree()
  {
    return $this->colsampleBytree;
  }
  /**
   * Dart normalization type for boosted tree models.
   *
   * @param StringHparamSearchSpace $dartNormalizeType
   */
  public function setDartNormalizeType(StringHparamSearchSpace $dartNormalizeType)
  {
    $this->dartNormalizeType = $dartNormalizeType;
  }
  /**
   * @return StringHparamSearchSpace
   */
  public function getDartNormalizeType()
  {
    return $this->dartNormalizeType;
  }
  /**
   * Dropout probability for dnn model training and boosted tree models using
   * dart booster.
   *
   * @param DoubleHparamSearchSpace $dropout
   */
  public function setDropout(DoubleHparamSearchSpace $dropout)
  {
    $this->dropout = $dropout;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getDropout()
  {
    return $this->dropout;
  }
  /**
   * Hidden units for neural network models.
   *
   * @param IntArrayHparamSearchSpace $hiddenUnits
   */
  public function setHiddenUnits(IntArrayHparamSearchSpace $hiddenUnits)
  {
    $this->hiddenUnits = $hiddenUnits;
  }
  /**
   * @return IntArrayHparamSearchSpace
   */
  public function getHiddenUnits()
  {
    return $this->hiddenUnits;
  }
  /**
   * L1 regularization coefficient.
   *
   * @param DoubleHparamSearchSpace $l1Reg
   */
  public function setL1Reg(DoubleHparamSearchSpace $l1Reg)
  {
    $this->l1Reg = $l1Reg;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getL1Reg()
  {
    return $this->l1Reg;
  }
  /**
   * L2 regularization coefficient.
   *
   * @param DoubleHparamSearchSpace $l2Reg
   */
  public function setL2Reg(DoubleHparamSearchSpace $l2Reg)
  {
    $this->l2Reg = $l2Reg;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getL2Reg()
  {
    return $this->l2Reg;
  }
  /**
   * Learning rate of training jobs.
   *
   * @param DoubleHparamSearchSpace $learnRate
   */
  public function setLearnRate(DoubleHparamSearchSpace $learnRate)
  {
    $this->learnRate = $learnRate;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getLearnRate()
  {
    return $this->learnRate;
  }
  /**
   * Maximum depth of a tree for boosted tree models.
   *
   * @param IntHparamSearchSpace $maxTreeDepth
   */
  public function setMaxTreeDepth(IntHparamSearchSpace $maxTreeDepth)
  {
    $this->maxTreeDepth = $maxTreeDepth;
  }
  /**
   * @return IntHparamSearchSpace
   */
  public function getMaxTreeDepth()
  {
    return $this->maxTreeDepth;
  }
  /**
   * Minimum split loss for boosted tree models.
   *
   * @param DoubleHparamSearchSpace $minSplitLoss
   */
  public function setMinSplitLoss(DoubleHparamSearchSpace $minSplitLoss)
  {
    $this->minSplitLoss = $minSplitLoss;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getMinSplitLoss()
  {
    return $this->minSplitLoss;
  }
  /**
   * Minimum sum of instance weight needed in a child for boosted tree models.
   *
   * @param IntHparamSearchSpace $minTreeChildWeight
   */
  public function setMinTreeChildWeight(IntHparamSearchSpace $minTreeChildWeight)
  {
    $this->minTreeChildWeight = $minTreeChildWeight;
  }
  /**
   * @return IntHparamSearchSpace
   */
  public function getMinTreeChildWeight()
  {
    return $this->minTreeChildWeight;
  }
  /**
   * Number of clusters for k-means.
   *
   * @param IntHparamSearchSpace $numClusters
   */
  public function setNumClusters(IntHparamSearchSpace $numClusters)
  {
    $this->numClusters = $numClusters;
  }
  /**
   * @return IntHparamSearchSpace
   */
  public function getNumClusters()
  {
    return $this->numClusters;
  }
  /**
   * Number of latent factors to train on.
   *
   * @param IntHparamSearchSpace $numFactors
   */
  public function setNumFactors(IntHparamSearchSpace $numFactors)
  {
    $this->numFactors = $numFactors;
  }
  /**
   * @return IntHparamSearchSpace
   */
  public function getNumFactors()
  {
    return $this->numFactors;
  }
  /**
   * Number of parallel trees for boosted tree models.
   *
   * @param IntHparamSearchSpace $numParallelTree
   */
  public function setNumParallelTree(IntHparamSearchSpace $numParallelTree)
  {
    $this->numParallelTree = $numParallelTree;
  }
  /**
   * @return IntHparamSearchSpace
   */
  public function getNumParallelTree()
  {
    return $this->numParallelTree;
  }
  /**
   * Optimizer of TF models.
   *
   * @param StringHparamSearchSpace $optimizer
   */
  public function setOptimizer(StringHparamSearchSpace $optimizer)
  {
    $this->optimizer = $optimizer;
  }
  /**
   * @return StringHparamSearchSpace
   */
  public function getOptimizer()
  {
    return $this->optimizer;
  }
  /**
   * Subsample the training data to grow tree to prevent overfitting for boosted
   * tree models.
   *
   * @param DoubleHparamSearchSpace $subsample
   */
  public function setSubsample(DoubleHparamSearchSpace $subsample)
  {
    $this->subsample = $subsample;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getSubsample()
  {
    return $this->subsample;
  }
  /**
   * Tree construction algorithm for boosted tree models.
   *
   * @param StringHparamSearchSpace $treeMethod
   */
  public function setTreeMethod(StringHparamSearchSpace $treeMethod)
  {
    $this->treeMethod = $treeMethod;
  }
  /**
   * @return StringHparamSearchSpace
   */
  public function getTreeMethod()
  {
    return $this->treeMethod;
  }
  /**
   * Hyperparameter for matrix factoration when implicit feedback type is
   * specified.
   *
   * @param DoubleHparamSearchSpace $walsAlpha
   */
  public function setWalsAlpha(DoubleHparamSearchSpace $walsAlpha)
  {
    $this->walsAlpha = $walsAlpha;
  }
  /**
   * @return DoubleHparamSearchSpace
   */
  public function getWalsAlpha()
  {
    return $this->walsAlpha;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HparamSearchSpaces::class, 'Google_Service_Bigquery_HparamSearchSpaces');
