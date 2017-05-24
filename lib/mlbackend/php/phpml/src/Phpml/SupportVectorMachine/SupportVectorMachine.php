<?php

declare(strict_types=1);

namespace Phpml\SupportVectorMachine;

use Phpml\Helper\Trainable;

class SupportVectorMachine
{
    use Trainable;

    /**
     * @var int
     */
    private $type;

    /**
     * @var int
     */
    private $kernel;

    /**
     * @var float
     */
    private $cost;

    /**
     * @var float
     */
    private $nu;

    /**
     * @var int
     */
    private $degree;

    /**
     * @var float
     */
    private $gamma;

    /**
     * @var float
     */
    private $coef0;

    /**
     * @var float
     */
    private $epsilon;

    /**
     * @var float
     */
    private $tolerance;

    /**
     * @var int
     */
    private $cacheSize;

    /**
     * @var bool
     */
    private $shrinking;

    /**
     * @var bool
     */
    private $probabilityEstimates;

    /**
     * @var string
     */
    private $binPath;

    /**
     * @var string
     */
    private $varPath;

    /**
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $targets = [];

    /**
     * @param int        $type
     * @param int        $kernel
     * @param float      $cost
     * @param float      $nu
     * @param int        $degree
     * @param float|null $gamma
     * @param float      $coef0
     * @param float      $epsilon
     * @param float      $tolerance
     * @param int        $cacheSize
     * @param bool       $shrinking
     * @param bool       $probabilityEstimates
     */
    public function __construct(
        int $type, int $kernel, float $cost = 1.0, float $nu = 0.5, int $degree = 3,
        float $gamma = null, float $coef0 = 0.0, float $epsilon = 0.1, float $tolerance = 0.001,
        int $cacheSize = 100, bool $shrinking = true, bool $probabilityEstimates = false
    ) {
        $this->type = $type;
        $this->kernel = $kernel;
        $this->cost = $cost;
        $this->nu = $nu;
        $this->degree = $degree;
        $this->gamma = $gamma;
        $this->coef0 = $coef0;
        $this->epsilon = $epsilon;
        $this->tolerance = $tolerance;
        $this->cacheSize = $cacheSize;
        $this->shrinking = $shrinking;
        $this->probabilityEstimates = $probabilityEstimates;

        $rootPath = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..'])).DIRECTORY_SEPARATOR;

        $this->binPath = $rootPath.'bin'.DIRECTORY_SEPARATOR.'libsvm'.DIRECTORY_SEPARATOR;
        $this->varPath = $rootPath.'var'.DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $binPath
     *
     * @return $this
     */
    public function setBinPath(string $binPath)
    {
        $this->binPath = $binPath;

        return $this;
    }

    /**
     * @param string $varPath
     *
     * @return $this
     */
    public function setVarPath(string $varPath)
    {
        $this->varPath = $varPath;

        return $this;
    }

    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets)
    {
        $this->samples = array_merge($this->samples, $samples);
        $this->targets = array_merge($this->targets, $targets);

        $trainingSet = DataTransformer::trainingSet($this->samples, $this->targets, in_array($this->type, [Type::EPSILON_SVR, Type::NU_SVR]));
        file_put_contents($trainingSetFileName = $this->varPath.uniqid('phpml', true), $trainingSet);
        $modelFileName = $trainingSetFileName.'-model';

        $command = $this->buildTrainCommand($trainingSetFileName, $modelFileName);
        $output = '';
        exec(escapeshellcmd($command), $output);

        $this->model = file_get_contents($modelFileName);

        unlink($trainingSetFileName);
        unlink($modelFileName);
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $samples
     *
     * @return array
     */
    public function predict(array $samples)
    {
        $testSet = DataTransformer::testSet($samples);
        file_put_contents($testSetFileName = $this->varPath.uniqid('phpml', true), $testSet);
        file_put_contents($modelFileName = $testSetFileName.'-model', $this->model);
        $outputFileName = $testSetFileName.'-output';

        $command = sprintf('%ssvm-predict%s %s %s %s', $this->binPath, $this->getOSExtension(), $testSetFileName, $modelFileName, $outputFileName);
        $output = '';
        exec(escapeshellcmd($command), $output);

        $predictions = file_get_contents($outputFileName);

        unlink($testSetFileName);
        unlink($modelFileName);
        unlink($outputFileName);

        if (in_array($this->type, [Type::C_SVC, Type::NU_SVC])) {
            $predictions = DataTransformer::predictions($predictions, $this->targets);
        } else {
            $predictions = explode(PHP_EOL, trim($predictions));
        }

        if (!is_array($samples[0])) {
            return $predictions[0];
        }

        return $predictions;
    }

    /**
     * @return string
     */
    private function getOSExtension()
    {
        $os = strtoupper(substr(PHP_OS, 0, 3));
        if ($os === 'WIN') {
            return '.exe';
        } elseif ($os === 'DAR') {
            return '-osx';
        }

        return '';
    }

    /**
     * @param string $trainingSetFileName
     * @param string $modelFileName
     *
     * @return string
     */
    private function buildTrainCommand(string $trainingSetFileName, string $modelFileName): string
    {
        return sprintf('%ssvm-train%s -s %s -t %s -c %s -n %s -d %s%s -r %s -p %s -m %s -e %s -h %d -b %d %s %s',
            $this->binPath,
            $this->getOSExtension(),
            $this->type,
            $this->kernel,
            $this->cost,
            $this->nu,
            $this->degree,
            $this->gamma !== null ? ' -g '.$this->gamma : '',
            $this->coef0,
            $this->epsilon,
            $this->cacheSize,
            $this->tolerance,
            $this->shrinking,
            $this->probabilityEstimates,
            escapeshellarg($trainingSetFileName),
            escapeshellarg($modelFileName)
        );
    }
}
