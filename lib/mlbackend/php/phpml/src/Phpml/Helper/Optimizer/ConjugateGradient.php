<?php

declare(strict_types=1);

namespace Phpml\Helper\Optimizer;

use Closure;

/**
 * Conjugate Gradient method to solve a non-linear f(x) with respect to unknown x
 * See https://en.wikipedia.org/wiki/Nonlinear_conjugate_gradient_method)
 *
 * The method applied below is explained in the below document in a practical manner
 *  - http://web.cs.iastate.edu/~cs577/handouts/conjugate-gradient.pdf
 *
 * However it is compliant with the general Conjugate Gradient method with
 * Fletcher-Reeves update method. Note that, the f(x) is assumed to be one-dimensional
 * and one gradient is utilized for all dimensions in the given data.
 */
class ConjugateGradient extends GD
{
    public function runOptimization(array $samples, array $targets, Closure $gradientCb): array
    {
        $this->samples = $samples;
        $this->targets = $targets;
        $this->gradientCb = $gradientCb;
        $this->sampleCount = count($samples);
        $this->costValues = [];

        $d = MP::muls($this->gradient($this->theta), -1);

        for ($i = 0; $i < $this->maxIterations; ++$i) {
            // Obtain α that minimizes f(θ + α.d)
            $alpha = $this->getAlpha($d);

            // θ(k+1) = θ(k) + α.d
            $thetaNew = $this->getNewTheta($alpha, $d);

            // β = ||∇f(x(k+1))||²  ∕  ||∇f(x(k))||²
            $beta = $this->getBeta($thetaNew);

            // d(k+1) =–∇f(x(k+1)) + β(k).d(k)
            $d = $this->getNewDirection($thetaNew, $beta, $d);

            // Save values for the next iteration
            $oldTheta = $this->theta;
            $this->costValues[] = $this->cost($thetaNew);

            $this->theta = $thetaNew;
            if ($this->enableEarlyStop && $this->earlyStop($oldTheta)) {
                break;
            }
        }

        $this->clear();

        return $this->theta;
    }

    /**
     * Executes the callback function for the problem and returns
     * sum of the gradient for all samples & targets.
     */
    protected function gradient(array $theta): array
    {
        [, $updates, $penalty] = parent::gradient($theta);

        // Calculate gradient for each dimension
        $gradient = [];
        for ($i = 0; $i <= $this->dimensions; ++$i) {
            if ($i === 0) {
                $gradient[$i] = array_sum($updates);
            } else {
                $col = array_column($this->samples, $i - 1);
                $error = 0;
                foreach ($col as $index => $val) {
                    $error += $val * $updates[$index];
                }

                $gradient[$i] = $error + $penalty * $theta[$i];
            }
        }

        return $gradient;
    }

    /**
     * Returns the value of f(x) for given solution
     */
    protected function cost(array $theta): float
    {
        [$cost] = parent::gradient($theta);

        return array_sum($cost) / (int) $this->sampleCount;
    }

    /**
     * Calculates alpha that minimizes the function f(θ + α.d)
     * by performing a line search that does not rely upon the derivation.
     *
     * There are several alternatives for this function. For now, we
     * prefer a method inspired from the bisection method for its simplicity.
     * This algorithm attempts to find an optimum alpha value between 0.0001 and 0.01
     *
     * Algorithm as follows:
     *  a) Probe a small alpha  (0.0001) and calculate cost function
     *  b) Probe a larger alpha (0.01) and calculate cost function
     *		b-1) If cost function decreases, continue enlarging alpha
     *		b-2) If cost function increases, take the midpoint and try again
     */
    protected function getAlpha(array $d): float
    {
        $small = MP::muls($d, 0.0001);
        $large = MP::muls($d, 0.01);

        // Obtain θ + α.d for two initial values, x0 and x1
        $x0 = MP::add($this->theta, $small);
        $x1 = MP::add($this->theta, $large);

        $epsilon = 0.0001;
        $iteration = 0;
        do {
            $fx1 = $this->cost($x1);
            $fx0 = $this->cost($x0);

            // If the difference between two values is small enough
            // then break the loop
            if (abs($fx1 - $fx0) <= $epsilon) {
                break;
            }

            if ($fx1 < $fx0) {
                $x0 = $x1;
                $x1 = MP::adds($x1, 0.01); // Enlarge second
            } else {
                $x1 = MP::divs(MP::add($x1, $x0), 2.0);
            } // Get to the midpoint

            $error = $fx1 / $this->dimensions;
        } while ($error <= $epsilon || $iteration++ < 10);

        // Return α = θ / d
        // For accuracy, choose a dimension which maximize |d[i]|
        $imax = 0;
        for ($i = 1; $i <= $this->dimensions; ++$i) {
            if (abs($d[$i]) > abs($d[$imax])) {
                $imax = $i;
            }
        }

        if ($d[$imax] == 0) {
            return $x1[$imax] - $this->theta[$imax];
        }

        return ($x1[$imax] - $this->theta[$imax]) / $d[$imax];
    }

    /**
     * Calculates new set of solutions with given alpha (for each θ(k)) and
     * gradient direction.
     *
     * θ(k+1) = θ(k) + α.d
     */
    protected function getNewTheta(float $alpha, array $d): array
    {
        return MP::add($this->theta, MP::muls($d, $alpha));
    }

    /**
     * Calculates new beta (β) for given set of solutions by using
     * Fletcher–Reeves method.
     *
     * β = ||f(x(k+1))||²  ∕  ||f(x(k))||²
     *
     * See:
     *  R. Fletcher and C. M. Reeves, "Function minimization by conjugate gradients", Comput. J. 7 (1964), 149–154.
     */
    protected function getBeta(array $newTheta): float
    {
        $gNew = $this->gradient($newTheta);
        $gOld = $this->gradient($this->theta);
        $dNew = 0;
        $dOld = 1e-100;
        for ($i = 0; $i <= $this->dimensions; ++$i) {
            $dNew += $gNew[$i] ** 2;
            $dOld += $gOld[$i] ** 2;
        }

        return $dNew / $dOld;
    }

    /**
     * Calculates the new conjugate direction
     *
     * d(k+1) =–∇f(x(k+1)) + β(k).d(k)
     */
    protected function getNewDirection(array $theta, float $beta, array $d): array
    {
        $grad = $this->gradient($theta);

        return MP::add(MP::muls($grad, -1), MP::muls($d, $beta));
    }
}

/**
 * Handles element-wise vector operations between vector-vector
 * and vector-scalar variables
 */
class MP
{
    /**
     * Element-wise <b>multiplication</b> of two vectors of the same size
     */
    public static function mul(array $m1, array $m2): array
    {
        $res = [];
        foreach ($m1 as $i => $val) {
            $res[] = $val * $m2[$i];
        }

        return $res;
    }

    /**
     * Element-wise <b>division</b> of two vectors of the same size
     */
    public static function div(array $m1, array $m2): array
    {
        $res = [];
        foreach ($m1 as $i => $val) {
            $res[] = $val / $m2[$i];
        }

        return $res;
    }

    /**
     * Element-wise <b>addition</b> of two vectors of the same size
     */
    public static function add(array $m1, array $m2, int $mag = 1): array
    {
        $res = [];
        foreach ($m1 as $i => $val) {
            $res[] = $val + $mag * $m2[$i];
        }

        return $res;
    }

    /**
     * Element-wise <b>subtraction</b> of two vectors of the same size
     */
    public static function sub(array $m1, array $m2): array
    {
        return self::add($m1, $m2, -1);
    }

    /**
     * Element-wise <b>multiplication</b> of a vector with a scalar
     */
    public static function muls(array $m1, float $m2): array
    {
        $res = [];
        foreach ($m1 as $val) {
            $res[] = $val * $m2;
        }

        return $res;
    }

    /**
     * Element-wise <b>division</b> of a vector with a scalar
     */
    public static function divs(array $m1, float $m2): array
    {
        $res = [];
        foreach ($m1 as $val) {
            $res[] = $val / ($m2 + 1e-32);
        }

        return $res;
    }

    /**
     * Element-wise <b>addition</b> of a vector with a scalar
     */
    public static function adds(array $m1, float $m2, int $mag = 1): array
    {
        $res = [];
        foreach ($m1 as $val) {
            $res[] = $val + $mag * $m2;
        }

        return $res;
    }

    /**
     * Element-wise <b>subtraction</b> of a vector with a scalar
     */
    public static function subs(array $m1, float $m2): array
    {
        return self::adds($m1, $m2, -1);
    }
}
