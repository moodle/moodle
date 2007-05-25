<?php

require_once $CFG->dirroot.'/lib/evalmath/evalmath.class.php';

class calc_formula {

    var $em;
    var $nfx = false;
    var $error = false;

    function calc_formula($formula, $params=false) {
        $this->em = new EvalMath();
        $this->em->suppress_errors = true;
        if (strpos($formula, '=') !== 0) {
            $this->error = "missing '='";
            return;
        }
        $formula = substr($formula, 1);
        if (strpos($formula, '=') !== false) {
            $this->error = "too many '='";
            return;
        }
        $this->nfx = $this->em->nfx($formula);
        if ($this->nfx == false) {
            $this->error = $this->em->last_error;
            return;
        }
        if ($params != false) {
            $this->em->v = $params;
        }
    }

    function set_params($params) {
        $this->em->v = $params;
    }

    function evaluate() {
        if ($this->nfx == false) {
            return false;
        }
        $res = $this->em->pfx($this->nfx);
        if ($res === false) {
            $this->error = $this->em->last_error;
            return false;
        } else {
            $this->error = false;
            return $res;
        }

    }

    function get_error() {
        return $this->error;
    }
}

?>