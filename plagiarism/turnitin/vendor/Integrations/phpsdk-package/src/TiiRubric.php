<?php

/*
 * @package TurnitinAPI
 * @subpackage TiiRubric 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

/**
 * Defines the TiiRubric data object which contains getters and setters for a Turnitin Rubric object.
 * 
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiRubric {
    
    private $rubricid;
    private $rubricname;
    private $rubricgroupname;
    
    /**
     * Get the Id for this Rubric
     * 
     * @return integer
     */
    public function getRubricId() {
        return $this->rubricid;
    }

    /**
     * Set the Id for this Rubric
     * 
     * @param integer $rubricid
     */
    public function setRubricId($rubricid) {
        $this->rubricid = $rubricid;
    }

    /**
     * Get the Name for this Rubric
     * 
     * @return text
     */
    public function getRubricName() {
        return $this->rubricname;
    }

    /**
     * Set the Name for this Rubric
     * 
     * @param text $rubricname
     */
    public function setRubricName($rubricname) {
        $this->rubricname = $rubricname;
    }

	/**
	 * Get the Group Name for this Rubric
	 *
	 * @return mixed
	 */
	public function getRubricGroupName()
	{
		return $this->rubricgroupname;
	}

	/**
	 * Set the Group Name for this Rubric
	 *
	 * @param mixed $rubricgroupname
	 */
	public function setRubricGroupName($rubricgroupname)
	{
		$this->rubricgroupname = $rubricgroupname;
	}
    
}

