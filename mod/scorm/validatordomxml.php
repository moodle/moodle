<?php

define("01", "useCurrentAttemptObjectiveInfo");
define("02", "useCurrentAttemptProgressInfo");

$cont_err = 0;	//contatore per il numero di eventuali errori
$errors = array(); //array degli errori
$item_idref_array = array(); //array degli attr. idRef di <item>
$idres_array = array(); //array degli attr. id di <resource>
$def_org_array = array(); 
$id_org_array = array();

function is_spec($stringa)
{
        return($stringa == "\n" | $stringa == "\r" | $stringa == "\t" |
                $stringa == " " | $stringa == "\0" | $stringa == "\x0B"|
                $stringa == "");
}
                                                                                                                            
// Funzione che ritorna i valori validi per il tag <timeLimitAction>
                                                                                                                            
function is_message($str)
{
        return($str == "exit,message" | $str = "exit,no message" | $str == "continue,message" |
                $str == "continue,no message");
}

function is_cond_val($st)
{
	return($st == "satisfied" | $st == "objectiveStatusKnown" | $st == "objectiveMeasureKnown" |
	       $st == "objectiveMeasureGreaterThan" | $st == "objectiveMeasureLessThan" |
	       $st == "completed" | $st =="activityProgressKnown" | $st == "attempted" |
	       $st == "attemptLimitExceeded" | $st == "timeLimitExceeded" | $st == "always" |
	       $st == "outsideAvailableTimeRange"); 
}

function is_action1($str1)
{
	return($str1 == "skip" | $str1 == "disabled" | $str1 == "hiddenFromChoice" | $str1 == "stopForwardTraversal");
}

function is_action2($str2)
{
	return($str2 == "exitParent" | $str2 == "exitAll" | $str2 == "retry" | $str2 == "retryAll" |
	       $str2 == "continue" | $str2 == "previous");
}

function is_rcond_val($st1)
{
	return($st1 == "satisfied" | $st1 == "objectiveStatusKnown" | $st1 == "objectiveMeasureKnown" |
	       $st1 == "completed" | $st1 =="activityProgressKnown" | $st1 == "attempted" |
	       $st1 == "attemptLimitExceeded" | $st1 == "timeLimitExceeded" | $st1 == "outsideAvailableTimeRange");
}

function is_rolAc_val($str3)
{
	return($str3 == "satisfied" | $str3 == "notSatisfied" | $str3 == "completed" | $str3 == "incomplete");
}

function is_randT_attr($str4)
{
	return($str4 == "never" | $str4 == "once" | $str4 == "onEachNewAttempt");
}

function is_rolCo_val($str5)
{
	return($str5 == "always" | $str5 == "ifAttempted" | $str5 == "ifNotSkipped" | $str5 == "ifNotSuspended");
}

function is_hch_cont($str6)
{
	return($str6 == "previous" | $str6 == "continue" | $str6 == "exit" | $str6 == "abandon");
}

/* Funzione che testa l'elemento root dell'albero DOM generato */

function testRoot($DomNode)
{
	global $errors;
	global $cont_err;
	$error = NULL;
	
	if(($DomNode->node_type() == "1")&&($DomNode->tagname() == "manifest"))
	{
		if(!$DomNode->has_attributes())
		{
			$error->type = "no_attributes";
			$error->data->tag = "manifest";
			array_push($errors, $error);
			$cont_err++;
		}
		$attr_array = $DomNode->attributes();
		$attr_n = $attr_array[0]->node_name();
		if($DomNode->has_attribute("identifier"))
		{
		    if((strcmp("identifier", $attr_n)!=0)||($attr_array[0]->node_value() == ""))
		    {
			$error->type = "attr_error";
                        $error->data->tag = "manifest";
			$error->data->attr = $attr_n;
			array_push($errors, $error);
			$cont_err++;
		    }
		} else {
		    	$error->type = "missing_attribute";
		    	$error->data->tag = "manifest";
		    	$error->data->attr = 'identifier';
			array_push($errors, $error);
			$cont_err++;
		}
		
		if($DomNode->has_attribute("version"))
		{
			$version = $attr_array[1]->node_value();	
			if(!eregi("[1-9]\.[0-9]", $version))
			{
				$error->type = "attr_error";
                        	$error->data->tag = "manifest";
				$error->data->attr = 'version';
				array_push($errors, $error);
				$cont_err++;				
			}
		} else {
			$error->type = "missing_attribute";
			$error->data->tag = "manifest";
			$error->data->attr = 'version';
			array_push($errors, $error);
			$cont_err++;
		}
		if(count($errors) > 0)
  		{
 		   return false;
 		}
  		else {
  		   return true;
		}
	}
	return false;
}

/* funzione che scorre l'albero DOM e valida il documento*/
function testNode($DomNode)
{
  global $cont_err; //gli array devono essere dichiarati globali
  global $errors;
  global $item_idref_array;
  global $idres_array;
  global $def_org_array;
  global $id_org_array;
  $error = NULL;
  $version_value = NULL;
  $schema_version = NULL;

  if($figlio = $DomNode->first_child())
  {
  	while($figlio)
	{
		if($figlio->node_type() == "1")
		{
			//valore booleano che viene restituito dalla funzione se e' tutto corretto		
		        
			$nome_figlio = $figlio->node_name();
			$padre = $figlio->parent_node();
			$pp = $padre->parent_node();
			
			if($nome_figlio == "metadata")
			{
			  if($figlio->has_attributes())
                          {
			    $error->type = "too_many_attributes";
			    $error->data->tag = "metadata";
			    array_push($errors, $error);
			    $cont_err++;
                          }
			                                                                                                  
                          $mdata_children = $figlio->child_nodes(); //array dei figli
                          // Il metodo count conta il numero di elementi dell'array e ritorna il numero
                          $tot_children = count($mdata_children);  

			  if($padre->node_name() == "manifest")
			  {
				//Si controlla se il tag metadata ha i figli <schema> e <schemaversion>
				//che devono essere SEMPRE presenti all'interno del manifest.
				$test_sc = $tot_children;
                                $test_scv = $tot_children;
				for($i = 0; $i < $tot_children; $i++)
				{
				  $cur_child = $mdata_children[$i];
				  if($cur_child->node_name()!="schema")
				  {
					$test_sc--;
				  }
				  if($cur_child->node_name() != "schemaversion")
				  {
					$test_scv--;
				  } else {
				    $schema_version = $cur_child->node_value();
				  }
			  	}
			  	
				if(!eregi("(CAM[[:blank:]])?[1]\.[3]", $schema_version))
                                {
                                  if($figlio->has_child_nodes())
                          	  {
			   	    $error->type = "too_many_children";
                           	    $error->data->tag = "metadata";
                           	    array_push($errors, $error);
                          	  }
                          	} else {
                          	  if(!$figlio->has_child_nodes())
                          	  {
			   	    $error->type = "no_children";
                           	    $error->data->tag = "metadata";
                           	    array_push($errors, $error);
                          	  }
                          	
   	         		  if($test_sc <= 0)
                                  {
				    $error->type = "missing_tag";
				    $error->data->tag = "schema";
				    array_push($errors, $error);
				    $cont_err++;
                                  }
				  if($test_scv <= 0)
	                          {
				    $error->type = "missing_tag";
                                    $error->data->tag = "schemaversion";
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
                                
			  }
 
			  //METADATA puo'comparire anche come figlio di altri nodi

			  if($padre->node_name() != "manifest")
			  {
			    $t_sc = $tot_children;
                            $t_scv = $tot_children;
                            $new_t_sc = 0;
                            $new_t_scv = 0;
			    for($i = 0; $i < $tot_children; $i++)
                            {
                              $cc = $mdata_children[$i];
                              
                              if($cc->node_name()=="schema")
                              {
                                $new_t_sc = $t_sc-1;
                              }
                              if($cc->node_name() == "schemaversion")
                              {
                                $new_t_scv = $t_scv-1;
                                $schema_version = $cc->node_value();
                              } 
                              
                            }//fine FOR

			    if(eregi("(CAM[[:blank:]])?[1]\.[3]", $schema_version))
			    {
			    	if($new_t_sc < $t_sc)
                            	{
                              	  $error->type = "too_many_children";
                                  $error->data->tag = "metadata";
                                  array_push($errors, $error);
                                  //echo "WARNING, se il tag metadata non e' figlio di manifest non deve avere schema
                                  //      come figlio <br>";
			         //echo "Probabilmente la versione e' antecedente alla 1.3 <br>";
			        }
			        if($new_t_scv < $t_scv)
                                {
                                  $error->type = "too_many_children";
                                  $error->data->tag = "metadata";
                                  array_push($errors, $error);
                                  //echo "WARNING, se il tag metadata non e' figlio di manifest, non deve avere schemaversion
                                  //      come figlio <br>";
			          //echo "Probabilmente la versione e' antecedente alla 1.3 <br>";
                                }
                            }
			  }	
			 // echo "SCANSIONE TAG METADATA TERMINATA, OK \n";		
			}// FINE IF METADATA
			
			if($nome_figlio == "schema")
			{
			  if($padre->node_name() != "metadata")
   			  {
				$error->type = "position_error";
				$error->data->tag = "schema";
				$error->data->parent = $padre->node_name();
				array_push($errors, $error);
				//echo "ERROR, schema puo'comparire solo come figlio di metadata";
				$cont_err++;
			  }
			  if(!$figlio->has_child_nodes())
			  {
				$error->type = "no_children";
				$error->data->tag = "schema";
				array_push($errors, $error);
				$cont_err++;
			  }
			  $sc = $figlio->first_child();
                          if(($sc->node_type() == 3)&&(strcmp($sc->node_value(), "ADL SCORM")!=0))
                          {
				$error->type = "tag_error";
				$error->data->tag = "schema";
				$error->data->value = $sc->node_value();
				array_push($errors, $error);
				$cont_err++;
                          }
			  
			}

			if($nome_figlio == "schemaversion")
			{
			  if($padre->node_name() != "metadata")
			  {
				$error->type = "position_error";
                                $error->data->tag = "schemaversion";
				$error->data->parent = $padre->node_name();
				array_push($errors, $error);
				$cont_err++;
			  }
			  if(!$figlio->has_child_nodes())
                          {
				$error->type = "no_children";
                                $error->data->tag = "schema";
				array_push($errors, $error);
				$cont_err++;
                          }
			  $svc = $figlio->first_child();
			  
			  $version_value = $svc->node_value();
                          if(($svc->node_type() == 3)&&(!eregi("(CAM[[:blank:]])?[1]\.[0-3]",$svc->node_value())))
			  {
			  	$version_value = $svc->node_value();
				if(eregi("[1]\.[0-3]", $version_value))
				{
				  echo "Versione=",$version_value;
				} else {
				  $error->type = "tag_error";
				  $error->data->tag = "schemaversion";
				  $error->data->value = $version_value;
				  array_push($errors, $error);
				}
                          }
			  
			}

			if($nome_figlio == "location")
			{
			  if(strcmp("metadata", $padre->node_name())!=0)
                          {
				$error->type = "position_error";
                                $error->data->tag = "location";
				$error->data->parent = $padre->node_name();
				array_push($errors, $error);	
				$cont_err++;
                          }
			  if(!$figlio->has_child_nodes())
                          {
				$error->type = "no_children";
                                $error->data->tag = "location";
				array_push($errors, $error);
				$cont_err++;
                          }
			  else
			  {	
                            $loc = $figlio->first_child();
                            if(($loc->node_type() == 3)&&(!eregi("[[:print:]]\.[a-z]",$loc->node_value())))
                            {
				$error->type = "tag_error";
                                $error->data->tag = "location";
				$error->data->value = $loc->node_value();
				array_push($errors, $error);
				$cont_err++;
                            } 
			  }          
			  //echo "<br> SCANSIONE DI LOCATION TERMINATA, OK \n";
			}

			if($nome_figlio == "organizations")
			{
				if($padre->node_name() != "manifest")
				{
				  $error->type = "position_error";
                                  $error->data->tag = "organizations";
				  $error->data->parent = $padre->node_name();
				  array_push($errors, $error);
				  $cont_err++;
				}
				if(!eregi("(CAM[[:blank:]])?[1]\.[3]", $version_value))
				{
				  if($figlio->has_attribute("default"))
				   {
				      $orgs_attr_old = $figlio->attributes();
				      $defv_old = $orgs_attr_old[0]->node_value();
				      if(!eregi("[[:graph:]]", $defv_old))
				      {
				         $error->type = "attr_error";
				         $error->data->tag = "organizations";
				         $error->data->attr = "default";
				         array_push($errors, $error);
				         $cont_err++;
				      }
				   }
				} else {
				   if((!$figlio->has_attributes())||(!$figlio->has_attribute("default")))
				   {
				      $error->type = "missing_attribute";
				      $error->data->tag = "organizations";
				      $error->data->attr = "default";
				      array_push($errors, $error);
				      $cont_err++;
				   }
				   $orgs_attr = $figlio->attributes();
				   if($orgs_attr[0]->node_value() != "")
				   {
				      $def_v = $orgs_attr[0]->node_value();
				      array_push($def_org_array, $def_v);
				   }
  
				   if(!$figlio->has_child_nodes())
				   {
				      $error->type = "no_children";
                                      $error->data->tag = "organizations";
				      array_push($errors, $error); 
				      $cont_err++;
				   }

				   $orgs_children = $figlio->child_nodes();
                                   $cont_orgs = count($orgs_children);
                                                                                                                            
                                   for($k=0; $k < $cont_orgs; $k++)
                                   {
                                      $co = $orgs_children[$k];
				      $test1 = $cont_orgs;
				      if(($co->node_type() != 1)&&($co->node_name() != "organization"))
				      {
				         $test1--;
				      }
				   }
	
				   if($test1 <= 0)
                                   {
				      $error->type = "missing_tag";
				      $error->data->tag = "organizations";
				      array_push($errors, $error);
				      $cont_err++;
                                   }
                                }
	
				//echo "SCANSIONE TAG ORGANIZATIONS TERMINATA, OK \n";
			}//FINE IF ORGANIZATIONS

			if($nome_figlio == "organization")
			{
				if($padre->node_name() != "organizations")
				{
				  $error->type = "position_error";
                                  $error->data->tag = "organization";
				  $error->data->parent = $padre->node_name();
				  array_push($errors, $error);
				  $cont_err++;
				}
			
				if(!$figlio->has_attributes())
                                {
			   	  $error->type = "no_attributes";
				  $error->data->tag = "organization";
				  array_push($errors, $error);
				  $cont_err++;
                                }
                                $org_attr = $figlio->attributes();
				$cont_org = count($org_attr);
				if($cont_org > 3)
				{
				  $error->type = "too_many_attributes";
				  $error->data->tag = "organization";
				  array_push($errors, $error);
				  $cont_err++;
				}
                                $org_id = $org_attr[0]->node_name();
                                $org_val = $org_attr[0]->node_value();
                                if(($org_id != "identifier")||(!eregi("[[:graph:]]", $org_val)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = "organization";
				  $error->data->attr = $org_id;
				  array_push($errors, $error);
				  $cont_err++;
                                }
				else {
                                  array_push($id_org_array, $org_val);	
				}
				
				if($figlio->has_attribute("structure"))
                                {
                                  if($org_attr[1]->node_value() == "")
				  {
					$error->type = "tag_error";
					$error->data->tag = "organization";
					array_push($errors, $error);
					$cont_err++;
				  }
                                }

				if($figlio->has_attribute("adlseq:objectivesGlobalToSystem"))
				{
				  $ot_n = $org_attr[2]->node_name();
				  $ot_val = $org_attr[2]->node_value();
				  if(eregi("^$", $ot_val))
				  {	
					$error->type = "attrError";
					$error->data->tag = "organization";
					$error->data->attr = $ot_n;
					array_push($errors, $error);
					$cont_err++;
				  }
				  if(!((strcmp("true", $ot_val)==0)||(strcmp("false", $ot_val)==0)))
				  {
					$error->type = "attr_error";
                                        $error->data->tag = "organization";
                                        $error->data->attr = $ot_n;
                                        array_push($errors, $error);
					$cont_err++;
				  }
				}
                                                                                                                            
                                if(!$figlio->has_child_nodes())
                                {
				  $error->type = "no_children";
                                  $error->data->tag = "organization";
                                  array_push($errors, $error);
				  $cont_err++;
                                }

				$org_children = $figlio->child_nodes();
				$org_cont = count($org_children);
				for($j=0; $j < $org_cont; $j++)
				{				
				  $of = $org_children[$j]->node_name();
				  $test2 = $org_cont;
				  if($of != "title")
				  {
				    $test2--;
				  }
				}
				if($test2 <= 0)
                                {
				  $error->type = "missing_tag";
                                  $error->data->tag = "title";
                                  array_push($errors, $error);
				  $cont_err++;
                                }

				//echo "SCANSIONE TAG ORGANIZATION TERMINATA, OK \n";
			}//FINE IF ORGANIZATION				
			 
			if($nome_figlio == "item")
			{
				if(!((strcmp("organization", $padre->node_name())==0)||
				     (strcmp("title", $padre->node_name())==0)||
				     (strcmp("item", $padre->node_name())==0)))
				{
				  $error->type = "position_error";
                                  $error->data->tag = "item";
				  $error->data->parent = $padre->node_name();
                                  array_push($errors, $error);
				  $cont_err++;
				}
				if(!$figlio->has_attributes())
				{
				  $error->type = "no_attributes";
                                  $error->data->tag = "item";
                                  array_push($errors, $error);
				  $cont_err++;
				}
				$item_attr = $figlio->attributes();
				$cont_attr2 = count($item_attr);
				for($i=0; $i < $cont_attr2; $i++)
				{
				  $ia = $item_attr[$i];
				  if(!$figlio->has_attribute("identifier"))
				  {
				    echo "ERROR, manca l'attributo ID";
				    $cont_err++;
				  }
				  elseif($ia->node_name()=="identifier")
				  {
				    $item_id = $ia->node_name();
                                    $item_id_val = $ia->node_value();
				    if($item_id_val == "")
				    {
				      $error->type = "attr_error";
                                      $error->data->tag = "item";
                                      $error->data->attr = $item_id;
                                      array_push($errors, $error);
				      $cont_err++;
				    }
				  }
	
				  if(($figlio->has_attribute("identifierref"))&&($ia->node_name()=="identifierref"))
				  {
				    $id_ref_val = $ia->node_value();
				    if(eregi("[[:graph:]]", $id_ref_val))
				    {
				      array_push($item_idref_array, $id_ref_val);
				    }
  				    else
				    {
				      $error->type = "attr_error";
                                      $error->data->tag = "item";
				      $error->data->attr = "identifierref";
                                      array_push($errors, $error);
				      $cont_err++;
				    }
        			  }				 

				  if(($figlio->has_attribute("isvisible"))&&($ia->node_name()=="isvisible"))
				  {
				    $isv_n = $ia->node_name();
				    $isv_val = $ia->node_value();
				    if(!((strcmp("true", $isv_val)==0)||(strcmp("false", $isv_val)==0))) 
				    {
				       $error->type = "attr_error";
                                       $error->data->tag = "item";
                                       $error->data->attr = $isv_n;
                                       array_push($errors, $error);
				       $cont_err++;
				    }
				  }
	
				  if(($figlio->has_attribute("parameters"))&&($ia->node_name()=="parameters"))
				  {
				    $par_n = $ia->node_name();
				    $par_val = $ia->node_value();
				    if(($par_val == "")||($par_val == " "))
				    {
					$error->type = "attr_error";
                                        $error->data->tag = "item";
                                        $error->data->attr = $par_n;
                                        array_push($errors, $error);
					$cont_err++;
				    }
				  }
				}//FINE FOR

				if(!$figlio->has_child_nodes())
				{				
				  $error->type = "no_children";
                                  $error->data->tag = "item";
                                  array_push($errors, $error);
				  $cont_err++;
				}

				$item_children = $figlio->child_nodes();
				$cont_ic = count($item_children);
				for($c=0; $c < $cont_ic; $c++)
				{			
				  $ic_n = $item_children[$c];
				  $test3 = $cont_ic;
				  if($ic_n->node_name() != "title")
				  {
				    $test3--;
				  }
				}
				if($test3 <= 0)
                                {
				  $error->type = "missing_tag";
                                  $error->data->tag = "title";
                                  array_push($errors, $error);
				  $cont_err++;
                                }		
			}//FINE IF ITEM

			if($nome_figlio == "title")
			{
			  if($figlio->get_content() == "")
			  {
				$error->type = "tag_error";
                                $error->data->tag = "title";
                                array_push($errors, $error);
				$cont_err++;
			  }
			}

			if($nome_figlio == "timeLimitAction")
			{
			  if($padre->node_name() != "item")
			  {
			    $error->type = "position_error";
			    $error->data->tag = "timeLimitAction";
			    $error->data->parent = $padre->node_name();
			    array_push($errors, $error);
			    $cont_err++;
			  }
			  if($pp->node_name() != "item")
			  {
			    $error->type = "position_error";
			    $error->data->tag = "timeLimitAction";
			    $error->data->parent = $padre->node_name();
			    array_push($errors, $error);
			    $cont_err++;
			  }
			  if(($figlio->get_content() == "")||(!is_message($figlio->get_content())))
			  {
			    $error->type = "tag_error";
			    $error->data->tag = "timeLimitAction";
			    $error->data->value = $figlio->get_content();
			    array_push($errors, $error);
			    $cont_err++;
			  }
			}	
			
			if($nome_figlio == "dataFromLMS")
			{	
			  if($padre->node_name() != "item")
                          {
			    $error->type = "position_error";
			    $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
			    array_push($errors, $error);
			    $cont_err++;
                          }
                          if($pp->node_name() != "item")
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
			    array_push($errors, $error);
			    $cont_err++;
                          }
			}

			//Il tag prerequisites e' presente solo nelle versioni precedenti alla 1.3

			if(($nome_figlio == "prerequisites")&&(eregi("(CAM[[:blank:]])?[1]\.[0-2]", $version_value)))
			{
			  if(strcmp("item", $padre->node_name())!=0)
			  {
			  	$error->type = "position_error";
				$error->data->tag = "$nome_figlio";
				$error->data->parent = $padre->node_name();
				array_push($errors);
				$cont_err++;
			  }		
			  if(!$figlio->has_attributes())
			  {
				$error->type = "no_attributes";
				$error->data->tag = $nome_figlio;
				array_push($errors, $error);
				$cont_err++;
			  }
			  if(!$figlio->has_attribute("type"))
			  {
				$error->type = "missing_attribute";
				$error->data->tag = $nome_figlio;
				$error->data->attr = "type";
				array_push($errors, $error);
				$cont_err++;
			  } else {
			      	$pre_attr = $figlio->attributes();
			      	$pre_attr_n = $pre_attr[0]->node_name();
			      	$pre_attr_v = $pre_attr[0]->node_value();
			      	if((strcmp("type", $pre_attr_n)==0)&&(strcmp("aicc_script", $pre_attr_v)!=0))
			      	{
				  $error->type = "attr_error";
				  $error->data->tag = $nome_figlio;
				  $error->data->attr = $pre_attr_n;
				  array_push($errors, $error);
				  $cont_err++;
			      	}
			    }		  
			}

			//Il tag maxtimeallowed e' presente solo nelle versioni precedenti alla 1.3

			if(($nome_figlio == "maxtimeallowed")&&(eregi("(CAM[[:blank:]])?[1]\.[0-2]", $version_value)))
			{
			  if(strcmp("item", $padre->node_name())!=0)
                          {
                                $error->type = "position_error";
                                $error->data->tag = "$nome_figlio";
                                $error->data->parent = $padre->node_name();
                                array_push($errors);
                                $cont_err++;
                          }
			  if(!$figlio->has_child_nodes())
                          {
                                $error->type = "no_children";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
                                $cont_err++;
                          } else {
			    	$mta_child = $figlio->first_child();
				if(!eregi("([0-9]{2}):([0-9]{2}):([0-9]{2})", $mta_child->get_content()))
				{
				  $error->type = "tag_error";
                                  $error->data->tag = $nome_figlio;
                                  array_push($errors, $error);
                                  $cont_err++;
				}
			  }
			}

			//Il tag masteryscore e' presente solo nelle versioni precedenti alla 1.3

                        if(($nome_figlio == "masteryscore")&&(eregi("(CAM[[:blank:]])?[1]\.[0-2]", $version_value)))
                        {
                          if(strcmp("item", $padre->node_name())!=0)
                          {
                                $error->type = "position_error";
                                $error->data->tag = "$nome_figlio";
                                $error->data->parent = $padre->node_name();
                                array_push($errors);
                                $cont_err++;
                          }
                          if(!$figlio->has_child_nodes())
                          {
                                $error->type = "no_children";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
                                $cont_err++;
                          } else {
                                $mta_child = $figlio->first_child();
                                if(eregi("^$", $mta_child->get_content()))
                                {
                                  $error->type = "tag_error";
                                  $error->data->tag = $nome_figlio;
                                  array_push($errors, $error);
                                  $cont_err++;
                                }
                          }
                        }
		
			if($nome_figlio == "resources")
			{
			  if(strcmp("manifest", $padre->node_name())!=0)
			  {
				$error->type = "position_error";
                                $error->data->tag = $nome_figlio;
				$error->data->parent = $padre->node_name();
                                array_push($errors, $error);
				$cont_err++;
			  }
			}

			if($nome_figlio == "resource")
			{
			  if(strcmp("resources", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error); 
			    $cont_err++;
			  }
			  if(!$figlio->has_attributes())
                          {
			    $error->type = "no_attributes";
                            $error->data->tag = $nome_figlio;
                            array_push($errors, $error);
			    $cont_err++;
                          }
                          $res_attr = $figlio->attributes();
                          $cont_res = count($res_attr);
			  if($cont_res > 6)
			  {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			  }
			  for($i=0; $i < $cont_res; $i++)
			  {
			    $ra_n = $res_attr[$i]->node_name();
			    $ra_v = $res_attr[$i]->node_value();
			    if(!$figlio->has_attribute("identifier"))
			    {
				$error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "identifier";
                                array_push($errors, $error);
				$cont_err++;
			    }
			    elseif($ra_n == "identifier")
			    {
			      if(eregi("[[:graph:]]", $ra_v))
			      {
				array_push($idres_array, $ra_v);
			      } else {
				$error->type = "attr_error";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = $ra_n;
                                array_push($errors, $error);
			      }
			    }

			    if(!$figlio->has_attribute("type"))
			    {
			      $error->type = "missing_attribute";
                              $error->data->tag = $nome_figlio;
			      $error->tag->attr = "type";
                              array_push($errors, $error);
			      $cont_err++;
			    }
			    elseif($ra_n == "type")
                            {
                              if(strcmp("webcontent", $ra_v)!=0)
			      {
				$error->type = "attr_error";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "type";
                                array_push($errors, $error);
				$cont_err++;
			      }
			    }

			    if(($figlio->has_attribute("href"))&&(strcmp("href", $ra_n)==0))
			    {
				if(!eregi("[[:graph:]]", $ra_v))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
				  $error->data->attr = "href";
                                  array_push($errors, $error);
				  $cont_err++;
				} 
			    }
			    
			    // if (xml:base)
			
			    if(($figlio->has_attribute("scormType"))||($figlio->has_attribute("scormtype")))
			    {
				if((strcmp("scormType", $ra_n)==0)||(strcmp("scormtype", $ra_n)==0))
				{
				   if(!((strcmp("sco", $ra_v)==0)||(strcmp("asset", $ra_v)==0)))
				   {
				     $error->type = "attr_error";
                                     $error->data->tag = $nome_figlio;
				     $error->data->attr = $ra_n;
                                     array_push($errors, $error);
				     $cont_err++;
				   }
				}
			    }
                            else
			    {
			    	$error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "scormType";
                                array_push($errors, $error);
				$cont_err++;
			    	
			    }

			    if(($figlio->has_attribute("persistState"))&&(strcmp("persistState", $ra_n)==0))
			    {
				if(!eregi("[0-1]", $ra_v))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
				  $error->data->attr = $ra_n;
                                  array_push($errors, $error);
				  $cont_err++;
				}
			    }
			  }
			  
			}

			if($nome_figlio == "file")
			{
			  if(!$figlio->has_attributes())
			  {
			    $error->type = "no_attributes";
                            $error->data->tag = $nome_figlio;
                            array_push($errors, $error);
			    $cont_err++;
			  }
			
			  $file_attr = $figlio->attributes();
			  if(($figlio->has_attribute("href"))&&($file_attr[0]->node_name() == "href"))
			  {			  
			    if(!eregi("[[:print:]]\.[a-z]", $file_attr[0]->node_value()))
                            {
				$error->type = "attr_error";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "href";
                                array_push($errors, $error);
				$cont_err++;
                            }
			    //else echo "SCANSIONE TAG FILE TERMINATA, OK \n";
			  }
			  else
			  {
			    $error->type = "missing_attribute";
                            $error->data->tag = $nome_figlio;
			    $error->data->attr = "href";
                            array_push($errors, $error);
			    $cont_err++;
			  }
			}

			if($nome_figlio == "dependency")
			{			
			  if(strcmp("resource", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  if(!$figlio->has_attributes())
			  {
			    $error->type = "no_attributes";
                            $error->data->tag = $nome_figlio;
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  $dep_attr = $figlio->attributes();
			  if(($figlio->has_attribute("identifierref"))&&($dep_attr[0]->node_name() == "identifierref"))
			  {
			    // Per valutare il valore di dependency uso l'espressione regolare [[:graph:]]
			    // che rappresenta tutti i caratteri ASCII stampabili, escluso lo spazio.

			    if(!eregi("[[:graph:]]", $dep_attr[0]->node_value()))
			    {
				$error->type = "attr_error";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "identifierref";
                                array_push($errors, $error);
				$cont_err++;
			    }
			    //else echo "SCANSIONE TAG DEPENDENCY TERMINATA \n";			  	
			  } else {
			        $error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "identifierref";
                                array_push($errors, $error);
			  }
			}

		      if(eregi("(CAM[[:blank:]])?[1]\.[3]", $version_value))
		      {
		        if($nome_figlio == "sequencing")
			{
			  if(!((strcmp("item", $padre->node_name())==0)||(strcmp("organization", $padre->node_name())==0)))
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);	
			    $cont_err++;
			  }
			 
			  $seq_attr = $figlio->attributes();
			  $cont_seq = count($seq_attr);
			  if($cont_seq > 2)
			  {
			    $error->type = "too_many_attributes";
                            $error->data->tag = $nome_figlio;
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  for($i=0; $i < $cont_seq; $i++)
			  { 
			    $seq_a = $seq_attr[$i];   
			    if(($figlio->has_attribute("ID"))&&($seq_a->node_name() == "ID"))
			    {
			      if(!eregi("[[:graph:]]", $seq_a->node_value()))
			      {	
				$error->type = "attr_error";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "ID";
                                array_push($errors, $error);
				$cont_err++;
			      }
			    }
                           
			    if(($figlio->has_attribute("IDRef"))&&($seq_a->node_name() == "IDRef"))
			    {
			      if(!eregi("[[:graph:]]", $seq_a->node_value()))
                              {
				$error->type = "attr_error";
                                $error->data->tag = $nome_figlio;
                                $error->data->attr = "IDRef";
                                array_push($errors, $error);
				$cont_err++;
                              }
			    }		
			  }
			}

			if($nome_figlio == "controlMode")
			{
			  if(strcmp("sequencing", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  
			  $ctrlM_attr = $figlio->attributes();
			  $cont_ctrlM = count($ctrlM_attr);
			  if($cont_ctrlM > 6)
			  {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);	
				$cont_err++;
			  }
			  
			  for($i=0; $i < $cont_ctrlM; $i++)
			  {
			    $cM_name = $ctrlM_attr[$i]->node_name();
			    $cM_val = $ctrlM_attr[$i]->node_value();
			    if(($figlio->has_attribute("choice"))&&($cM_name == "choice"))
			    {
				if(!((strcmp("true", $cM_val)==0)||(strcmp("false", $cM_val)==0)))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $cM_name;
                                  array_push($errors, $error);
				  $cont_err++;
				}
				//else "OK CHOICE";
			    }
			    
			    if(($figlio->has_attribute("choiceExit"))&&($cM_name == "choiceExit"))
                            {
                                if(!((strcmp("true", $cM_val)==0)||(strcmp("false", $cM_val)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $cM_name;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                            }
			
			    if(($figlio->has_attribute("flow"))&&($cM_name == "flow"))
                            {
                                if(!((strcmp("true", $cM_val)==0)||(strcmp("false", $cM_val)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $cM_name;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                            }

			    if(($figlio->has_attribute("forwardOnly"))&&($cM_name == "forwardOnly"))
                            {
                                if(!((strcmp("true", $cM_val)==0)||(strcmp("false", $cM_val)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $cM_name;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                            }

			    if(($figlio->has_attribute("01"))&&($cM_name == "01"))
                            {
                                if(!((strcmp("true", $cM_val)==0)||(strcmp("false", $cM_val)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $cM_name;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                            }

			    if(($figlio->has_attribute("02"))&&($cM_name == "02"))
                            {
                                if(!((strcmp("true", $cM_val)==0)||(strcmp("false", $cM_val)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $cM_name;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                            }			    
			  }// END FOR
			  
			}
		
			if($nome_figlio == "sequencingRules")
			{
			  if(strcmp("sequencing", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			}
			
			if($nome_figlio == "preConditionRule")
                        {
                          if(strcmp("sequencingRules", $padre->node_name())!=0)
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
			  $preCo_children = $figlio->child_nodes();
			  
                        }

			if($nome_figlio == "postConditionRule")
                        {
                          if(strcmp("sequencingRules", $padre->node_name())!=0)
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
			  
                        }

			if($nome_figlio == "exitConditionRule")
                        {
                          if(strcmp("sequencingRules", $padre->node_name())!=0)
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
			  
                        }

			if($nome_figlio == "ruleConditions")
			{
			  
			  if(!((strcmp("preConditionRule", $padre->node_name())==0)
			    ||(strcmp("postConditionRule", $padre->node_name())==0)
			    ||(strcmp("exitConditionRule", $padre->node_name())==0)))
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
			  
			  if($figlio->has_attributes())
			  {
			    $rCs_attr = $figlio->attributes();  
			    if($rCs_attr[0]->node_name()=="conditionCombination")
			    {
			      if(!((strcmp("all", $rCs[0]->node_value())==0)||(strcmp("any", $rCs[0]->node_value())==0)))
			      {
				$error->type = "attr_error";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "conditionCombination";
                                array_push($errors, $error);
				$cont_err++;
			      }
			    }	
			  }
			  //echo "SCANSIONE TAG RULESCONDITIONS TERMINATA";
			}

			if($nome_figlio == "ruleCondition")
			{
			  if(strcmp("ruleConditions", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }

			  if($figlio->has_attributes())
			  {
			    $rC_attr = $figlio->attributes();
			    $cont_rC = count($rC_attr);
			    if($cont_rC > 4)
			    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_rC; $i++)
			    {
			    	$rCa_name = $rC_attr[$i]->node_name();
				$rCa_val = $rC_attr[$i]->node_value();
				if($rCa_name == "referencedObjective")
				{
				  if(!eregi("[[:graph:]]", $rCa_val))
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
				    $error->data->attr = $rCa_name;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
				
				if($rCa_name == "measureThreshold")
				{
				  settype($rCa_val, "float");
				  if(($rCa_val < -1.0000)||($rCa_val > 1.0000))
				  {
				    $error->type = "attr_error";
                            	    $error->data->tag = $nome_figlio;
				    $error->data->attr = $rCa_name;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}	

				if($rCa_name == "operator")				
				{
				  if((!eregi("not", $rCa_val))||(!eregi("noOp", $rCa_val)))
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rCa_name;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
	
				if($rCa_name == "condition")
				{
				  if(!is_cond_val($rCa_val))
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rCa_name;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
			    }// END FOR
			  }
			}

			if($nome_figlio == "ruleAction")
			{			
			  if($padre->node_name() == "preConditionRule")
			  {
			    if(($figlio->has_attributes())&&($figlio->has_attribute("action")))
			    {
				$rAc_attr1 = $figlio->attributes();
				if($rAc_attr1[0]->node_name() != "action")
				{
				  $error->type = "missing_attribute";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = "action";
                                  array_push($errors, $error);
				  $cont_err++;
				}
				elseif(!is_action1($rAc_attr1[0]->node_value()))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = "action";
                                  array_push($errors, $error);
				  $cont_err++;
				}
				
			    }
			    else
			    {
				$error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
                                $error->data->attr = "action";
                                array_push($errors, $error);
				$cont_err++;
			    }
			  }
			
			  elseif($padre->node_name() == "postConditionRule")
                          {
                            if(($figlio->has_attributes())&&($figlio->has_attribute("action")))
                            {
                                $rAc_attr2 = $figlio->attributes();
                                if($rAc_attr2[0]->node_name() != "action")
                                {
				  $error->type = "missing_attribute";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = "action";
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                                elseif(!is_action2($rAc_attr2[0]->node_value()))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = "action";
                                  array_push($errors, $error);
				  $cont_err++;
                                }
				
                            }
                            else
                            {
				$error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
                                $error->data->attr = "action";
                                array_push($errors, $error);
				$cont_err++;
                            }
                          }
			
			  elseif($padre->node_name() == "exitConditionRule")
                          {
                            if(($figlio->has_attributes())&&($figlio->has_attribute("action")))
                            {
                                $rAc_attr3 = $figlio->attributes();
                                if($rAc_attr3[0]->node_name() != "action")
                                {
				  $error->type = "missing_attribute";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = "action";
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                                elseif($rAc_attr3[0]->node_value() != "exit")
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = "action";
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                                
                            }
                            else
                            {
				$error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
                                $error->data->attr = "action";
                                array_push($errors, $error);
				$cont_err++;
                            }
                          }
			  else
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			}

			if($nome_figlio == "limitConditions")
			{
			  if(strcmp("sequencing", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  if($figlio->has_attributes())
			  {
			    $lAc_attr = $figlio->attributes();
			    $cont_lAc = count($lAc_attr);
			    if($cont_lAc > 2)
			    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_lAc; $i++)
			    {
				$lAc_name = $lAc_attr[$i]->node_name();
				$lAc_val = $lAc_attr[$i]->node_value();
				if(($figlio->has_attribute("attemptLimit"))&&($lAc_name == "attemptLimit"))
				{
				  settype($lAc_val, "int");
				  if($lAc_val < 0)
				  {
				    $error->type = "attr_error";
                            	    $error->data->tag = $nome_figlio;
				    $error->data->attr = $lAc_name;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
				if($lAc_name == "attemptAbsoluteDurationLimit")
				{
				  if(!eregi("[P][[:digit:]][Y][0-9][M][0-9][D][0-9][H][0-60][M][0-60][S]", $lAc_val))
                                  {
				    $error->type = "attr_error";
                            	    $error->data->tag = $nome_figlio;
				    $error->data->attr = $lAc_val;
                              	    array_push($errors, $error);
				    $cont_err++;
                                  }
				}
			    }
			  }
			}
		
			if($nome_figlio == "auxiliaryResources")
			{
			  if(strcmp("sequencing", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  
			}

			if($nome_figlio == "auxiliaryResource")
			{
			  if(strcmp("auxiliaryResources", $padre->node_name())!=0)
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
			  if($figlio->has_attributes())
			  {
			    $aux_attr = $figlio->attributes();
			    $cont_aux = count($aux_attr);
			    if($cont_aux != 2)
			    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_aux; $i++)
			    {
				$aux_n = $aux_attr[$i]->node_name();
				$aux_v = $aux_attr[$i]->node_value();
				if((strcmp("auxiliaryResourceID", $aux_n)!=0)&&(strcmp("purpose", $aux_n)!=0))
				{
				  $error->type = "missing_attribute";
                                  $error->data->tag = $nome_figlio;
				  $error->data->attr = $aux_n;
                                  array_push($errors, $error);
				  $cont_err++;
				}
				elseif($aux_n == "auxiliaryResourceID")
				{
				  if(!eregi("[[:graph:]]", $aux_v))
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $aux_n;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
				elseif($aux_n == "purpose")
				{	
				  if(!eregi("[[:graph:]]", $aux_v))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $aux_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
				}
			    }    
			  }//echo "SCANSIONE TAG AUXILIARYRESOURCE TERMINATA";
			}
		
			if($nome_figlio == "rollupRules")
			{
			  if(strcmp("sequencing", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  if($figlio->has_attributes())
			  {
			    $rules_attr = $figlio->attributes();
			    $cont_rules = count($rules_attr);
			    if($cont_rules > 3)
			    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_rules; $i++)
			    {
			      $rules_nome = $rules_attr[$i]->node_name();
			      $rules_val = $rules_attr[$i]->node_value();
			      if($rules_nome == "rollupObjectiveSatisfied")
			      {
				if(!((strcmp("true", $rules_val)==0)||(strcmp("false", $rules_val)==0)))
				{
				  $error->type = "attr_error";
        	                  $error->data->tag = $nome_figlio;
				  $error->data->attr = $rules_nome;
	                          array_push($errors, $error);
				  //echo "ERROR, rollupObjectiveSatisfied puo'assumere solo valori booleani";
				  $cont_err++;
				}
			      }			      
			      if($rules_nome == "rollupProgressCompletion")
			      {
				if(!((strcmp("true", $rules_val)==0)||(strcmp("false", $rules_val)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $rules_nome;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
			      }
			      if($rules_nome == "objectiveMeasureWeight")
			      {  
				settype($rules_val, "float");  
				if(($rules_val < 0.0000)||($rules_val > 1.0000))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $rules_nome;
                                  array_push($errors, $error);
				  $cont_err++;
				}
			      }
			    }//END FOR	
			  }//echo "SCANSIONE TAG ROLLUPRULES TERMINATA";
			}// END ROLLUPRULES

			if($nome_figlio == "rollupRule")
			{
			  if(strcmp("rollupRules", $padre->node_name())!= 0)
			  {	
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);	
			    $cont_err++;
			  }
			  if($figlio->has_attributes())
			  {
			    $rule_attr = $figlio->attributes();
			    $cont_rule = count($rule_attr);
			    if($cont_rule > 3)
			    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_rule; $i++)
			    {
			    	$rule_name = $rule_attr[$i]->node_name();
				$rule_val = $rule_attr[$i]->node_value();
				if($rule_name == "childActivitySet")
				{
				  if(!((strcmp("all", $rule_val)==0)||(strcmp("any", $rule_val)==0)))
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rule_nome;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
				if($rule_name == "minimumCount")
				{
				  settype($rule_val, "int");
				  if($rule_val < 0)
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rule_nome;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
				if($rule_name == "minimumPercent")
                                {
				  settype($rule_val, "float");
                                  if(($rule_val < 0.0000)||($rule_val > 1.0000))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rules_nome;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
			    }
			  }//echo "SCANSIONE TAG ROLLUPRULE TERMINATA";
			}

			if($nome_figlio == "rollupConditions")
			{
			  if(strcmp("rollupRule", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  if(($figlio->has_attributes())&&($figlio->has_attribute("conditionCombination")))
			  {
			    $rCo_attr = $figlio->attributes();
			    $rCo_name = $rCo_attr[0]->node_name();
			    $rCo_val = $rCo_attr[0]->node_val();
                            if($rCo_name != "conditionCombination")
                            {
			      $error->type = "missing_attribute";
                              $error->data->tag = $nome_figlio;
                              $error->data->attr = $rCo_name;
                              array_push($errors, $error);
			      $cont_err++;
                            }
                            elseif(!((strcmp("all", $rCo_val)==0)||(strcmp("any", $rCo_val)==0)))
                            {
			      $error->type = "attr_error";
                              $error->data->tag = $nome_figlio;
                              $error->data->attr = $rCo_name;
                              array_push($errors, $error);
			      $cont_err++;
                            }
			  }
			  //echo "SCANSIONE TAG ROLLUPCONDITIONS TERMINATA";
			}
			
			if($nome_figlio == "rollupCondition")			
			{
			  if(strcmp("rollupConditions", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  if($figlio->has_attributes())
			  {
			    $rCond_attr = $figlio->attributes();
			    $cont_rCond = count($rCond_attr);
			    if($cont_rCond > 2)
			    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_rCond; $i++)
			    {
			      $rCond_n = $rCond_attr[$i]->node_name();
			      $rCond_v = $rCond_attr[$i]->node_value();
			      if($rCond_n == "operator")
			      {
				if(!((strcmp("not", $rCond_v)==0)||(strcmp("noOp", $rCond_v)==0)))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $rCond_n;
                                  array_push($errors, $error);
				  $cont_err++;
				}
			      }				     
			      elseif($rCond_n == "condition")
			      {
				if(!is_rcond_val($rCond_v))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $rCond_n;
                                  array_push($errors, $error);
				  $cont_err++;
				}	
			      }
			      else
			      { 
				$error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
                                $error->data->attr = "condition";
                                array_push($errors, $error);
				$cont_err++;
			      }  
			    }
			  }
			  else
			  {
			     $error->type = "no_attributes";
                             $error->data->tag = $nome_figlio;
                             array_push($errors, $error);
			     $cont_err++;
			  }
			}
	
			if($nome_figlio == "rollupAction")
			{
			  if(strcmp("rollupRule", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();  
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  if(($figlio->has_attributes())&&($figlio->has_attribute("action")))
                            {
                                $roAc_attr = $figlio->attributes();
                                if($roAc_attr[0]->node_name() != "action")
                                {
				  $error->type = "missing_attribute";
                                  $error->data->tag = $nome_figlio;
                     		  $error->data->attr = "action";
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                                elseif(!is_rolAc_val($roAc_attr[0]->node_value()))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = "action";
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                            }
                            else
                            {
				$error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
                                $error->data->attr = "action";
                                array_push($errors, $error);
				$cont_err++;
                            }
			}

			if($nome_figlio == "objectives")
                        {
                          if(strcmp("sequencing", $padre->node_name())!=0)
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
                        }			
		
			if($nome_figlio == "primaryObjective")
			{
			  if(strcmp("objectives", $padre->node_name())!=0)
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
			  if($figlio->has_attributes())
			  {
			    $prObj_attr = $figlio->attributes();
			    $cont_prObj = count($prObj_attr);
			    if($cont_prObj > 2)
			    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_prObj; $i++)
			    {
			      $pro_n = $prObj_attr[$i]->node_name();
			      $pro_v = $prObj_attr[$i]->node_value();
			      if($pro_n == "satisfiedByMeasure")
			      {
				if(!((strcmp("true", $pro_v)==0)||(strcmp("false", $pro_v)==0)))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
				  $error->data->attr = $pro_n;
                                  array_push($errors, $error);
				  $cont_err++;
				}
			      }
			      if($pro_n == "objectiveID")
			      {
				if(!eregi("[[:graph:]]", $pro_v))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $pro_n;
                                  array_push($errors, $error);
				  $cont_err++;
				}
			      }
			    }
			  }
			}
			
			if($nome_figlio == "minNormalizedMeasure")
			{
			  $fn = $padre->node_name();			
                          if(!((strcmp("primaryObjective", $fn)==0)||(strcmp("objective", $fn)==0)))
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
                          
			  if($figlio->has_child_nodes())
			  {
			    $figlio1 = $figlio->first_child();
			    if($figlio1->node_type() != 3)
			    {
				$error->type = "not_corr_type";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    settype($figlio1->get_content(), "float");
			    if(($figlio1->get_content() < 0.0)||($figlio1->get_content() > 1.0))
			    {
				$error->type = "tag_error";
                                $error->data->tag = $nome_figlio;
                                $error->data->value = $figlio1->get_content();
                                array_push($errors, $error);
				$cont_err++;
			    }			    
			  }
			  else
			  {
			    $error->type = "tag_error";
                            $error->data->tag = $nome_figlio;
                            array_push($errors, $error);
			    $cont_err++;
			  }
			}

			if($nome_figlio == "mapInfo")
			{
			  $pn = $padre->node_name();
                          if(!((strcmp("primaryObjective", $pn)==0)||(strcmp("objective", $pn)==0)))
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
			  if($figlio->has_attributes())
			  {
			    $map_attr = $figlio->attributes();
			    $cont_map = count($map_attr);
			    if($cont_map > 5)
		  	    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    if(!$figlio->has_attribute("targetObjectiveID"))
			    {
				$error->type = "missing_attribute";
                                $error->data->tag = $nome_figlio;
				$error->data->attr = "targetObjectiveID";
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_map; $i++)
			    {
			      $map_n = $map_attr[$i]->node_name();
			      $map_v = $map_attr[$i]->node_value();
			      if($map_n == "targetObjectiveID")
			      {
				if(!eregi("[[:graph:]]", $map_v))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = "targetObjectiveID";
                                  array_push($errors, $error);
				  $cont_err++;
			        }
			      }
			      if($map_n == "readSatisfiedStatus")
			      {
				if(!((strcmp("true", $map_v)==0)||(strcmp("false", $map_v)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $map_n;
                                  array_push($errors, $error);
				  $cont_err++;
                                }	
			      }
			      if($map_n == "readNormalizedMeasure")
			      {
                                if(!((strcmp("true", $map_v)==0)||(strcmp("false", $map_v)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $map_n;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                              }
			      if($map_n == "writeSatisfiedStatus")
			      {
                                if(!((strcmp("true", $map_v)==0)||(strcmp("false", $map_v)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $map_n;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
                              }
			      if($map_n == "writeNormalizedMeasure")
			      {
				if(!((strcmp("true", $map_v)==0)||(strcmp("false", $map_v)==0)))
                                {
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $map_n;
                                  array_push($errors, $error);
				  $cont_err++;
                                }
			      }	
			    }// END FOR
			  }
 			  else
			  {
			     $error->type = "missing_attribute";
                             $error->data->tag = $nome_figlio;
                             $error->data->attr = "targetObjectiveID";
                             array_push($errors, $error);
			     $cont_err++;
			  }
			  
			}
			
			if($nome_figlio == "objective")
                        {
                          if(strcmp("objectives", $padre->node_name())!=0)
                          {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
                          }
                          if($figlio->has_attributes())
                          {
                            $obj_attr = $figlio->attributes();
                            $cont_obj = count($obj_attr);
                            if($cont_obj > 2)
                            {  
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
                            }
                            for($i=0; $i < $cont_obj; $i++)
                            {
                              $ob_n = $obj_attr[$i]->node_name();
                              $ob_v = $obj_attr[$i]->node_value();
                              if($ob_n == "satisfiedByMeasure")
                              {
                                if(!((strcmp("true", $ob_v)==0)||(strcmp("false", $ob_v)==0)))
                                {
				  $error->type = "attr_error";
                            	  $error->data->tag = $nome_figlio;
				  $error->data->attr = $ob_n;
                            	  array_push($errors, $error);
				  $cont_err++;
                                }
                              }
                              if($ob_n == "objectiveID")
                              {
                                if(!eregi("[[:graph:]]", $ob_v))
				{
				  $error->type = "attr_error";
                                  $error->data->tag = $nome_figlio;
                                  $error->data->attr = $ob_n;
                                  array_push($errors, $error);
				  $cont_err++;
				}
                              }
                            }
                          }
                        }
			
			if($nome_figlio == "randomizationControls")
			{			
			  if(strcmp("sequencing", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
                            $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
                            array_push($errors, $error);
			    $cont_err++;
			  }
			  if($figlio->has_attributes())
			  {
			    $rand_attr = $figlio->attributes();
		 	    $cont_rand = count($rand_attr);
			    if($cont_rand > 4)
			    {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_rand; $i++)
			    {
				$rAt_n = $rand_attr[$i]->node_name();
				$rAt_v = $rand_attr[$i]->node_value();
				if($rAt_n == "randomizationTiming")
				{
				  if(!is_randT_attr($rAt_v))
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rAt_n;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
				if($rAt_n == "selectCount")
				{
				  setttype($rAt_v, "int");
				  if($rAt_v < 0)
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rAt_n;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}
				if($rAt_n == "reorderChildren")
				{
				  if(!((strcmp("true", $rAt_v)==0)||(strcmp("false", $rAt_v)==0)))
				  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rAt_n;
                                    array_push($errors, $error);
				    $cont_err++;
				  }
				}	
				if($rAt_v == "selectionTiming")
				{
				  if(!is_randT_attr($rAt_v))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rAt_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
				}
			    }
			  }//echo "SCANSIONE TAG RANDOMIZATION CONTROLS TERMINATA";
			}
			
			if($nome_figlio == "deliveryControls")
			{
			  if(strcmp("sequencing", $padre->node-name())!=0)
			  {
			    $error->type = "position_error";
			    $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
			    array_push($errors, $error);
			    $cont_err++;
			  }
			  if($figlio->has_attributes())
			  {
			    $delv_attr = $figlio->attributes();
			    $cont_delv = count($delv_attr);
			    if($cont_delv > 3)
			    {
				$error->type = "too_many_attributes";
				$error->data->tag = $nome_figlio;
				array_push($errors, $error);
				$cont_err++;
			    }
			    for($i=0; $i < $cont_delv; $i++)
			    {
			    	$delv_n = $delv_attr[$i]->node_name();
				$delv_v + $delv_attr[$i]->node_value();
				if($delv_n == "tracked")
				{
				  if(!((strcmp("true", $delv_v)==0)||(strcmp("false", $delv_v)==0)))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $delv_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
				}
				if($delv_n == "completionSetByContent")
				{
				  if(!((strcmp("true", $delv_v)==0)||(strcmp("false", $delv_v)==0)))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $delv_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
				}
				if($delv_n == "objectiveSetByContent")
				{
				  if(!((strcmp("true", $delv_v)==0)||(strcmp("false", $delv_v)==0)))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $delv_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
				}
			    }
			  }//echo "SCANSIONE TAG DELIVERYCONTROLS COMPLETATA";
			}
	
			if($nome_figlio == "constrainedChoiceConsiderations")
			{
			  if(strcmp("sequencing", $padre->node_name())!=0)
			  {
			    $error->type = "position_error";
			    $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
			    array_push($errors, $error);
			    $cont_err++;
			  }
			  if($figlio->has_attributes())
                          {
                            $ccc_attr = $figlio->attributes();
                            $cont_ccc = count($ccc_attr);
                            if($cont_ccc > 2)
                            {
				$error->type = "too_many_attributes";
				$error->data->tag = $nome_figlio;
				array_push($errors, $error);
				$cont_err++;
                            }
                            for($i=0; $i < $cont_ccc; $i++)
                            {
                                $ccc_n = $ccc_attr[$i]->node_name();
                                $ccc_v + $ccc_attr[$i]->node_value();
                                if($ccc_n == "preventActivation")
                                {
                                  if(!((strcmp("true", $ccc_v)==0)||(strcmp("false", $ccc_v)==0)))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $ccc_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
                                if($ccc_n == "constrainChoice")
                                {
                                  if(!((strcmp("true", $ccc_v)==0)||(strcmp("false", $ccc_v)==0)))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $ccc_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
			    }
                          }
			}
 		
			if($nome_figlio == "rollupConsiderations")
			{
			  if(strcmp("sequencing", $padre->node_name())!=0)
                          {
			    $error->type = "position_error";
			    $error->data->tag = $nome_figlio;
			    $error->data->parent = $padre->node_name();
			    array_push($errors, $error);
			    $cont_err++;
                          } 
			  if($figlio->has_attributes())
                          {
                            $rolCo_attr = $figlio->attributes();
                            $cont_rolCo = count($rolCo_attr);
                            if($cont_rolCo > 5)
                            {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
                            }
                            for($i=0; $i < $cont_rolCo; $i++)
                            {
                                $rolCo_n = $rolCo_attr[$i]->node_name();
                                $rolCo_v =  $rolCo_attr[$i]->node_value();
                                if($rolCo_n == "measureSatisfactionIfActive")
                                {
                                  if(!((strcmp("true", $rolCo_v)==0)||(strcmp("false", $rolCo_v)==0)))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rolCo_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
                                if($rolCo_n == "requiredForSatisfied")
                                {
                                  if(!is_rolCo_val($rolCo_v))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rolCo_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
				if($rolCo_n == "requiredForNotSatisfied")
                                {
                                  if(!is_rolCo_val($rolCo_v))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rolCo_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
				if($rolCo_n == "requiredForCompleted")
                                {
                                  if(!is_rolCo_val($rolCo_v))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rolCo_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
				if($rolCo_n == "requiredForIncomplete")
                                {
                                  if(!is_rolCo_val($rolCo_v))
                                  {
				    $error->type = "attr_error";
                                    $error->data->tag = $nome_figlio;
                                    $error->data->attr = $rolCo_n;
                                    array_push($errors, $error);
				    $cont_err++;
                                  }
                                }
                            }
			  }
			}
		
			if($nome_figlio == "presentation")
			{
			  if(strcmp("item", $padre->node_name())!=0)
			  {
			     $error->type = "position_error";
			     $error->data->tag = $nome_figlio;
			     $error->data->parent = $padre->node_name();
			     array_push($errors, $error);
			     $cont_err++;
			  }
			}

			if($nome_figlio == "navigationInterface")
			{
			  if(strcmp("presentation", $padre->node_name())!=0)
                          {
			     $error->type = "position_error";
                             $error->data->tag = $nome_figlio;
			     $error->data->parent = $padre->node_name();
                             array_push($errors, $error);
			     $cont_err++;
			  }
			}

			if($nome_figlio == "hideLMSUI")
			{
			  if(strcmp("navigationInterface", $padre->node_name())!=0)
			  {
			     $error->type = "position_error";
                             $error->data->tag = $nome_figlio;
			     $error->data->parent = $padre->node_name();
                             array_push($errors, $error);
			     $cont_err++;
			  }
			  if($figlio->has_child_nodes())
			  {
			     $hide_children = $figlio->child_nodes();
			     $cont_hide = count($hide_children);
			     if($cont_hide > 4)
			     {
				$error->type = "too_many_attributes";
                                $error->data->tag = $nome_figlio;
                                array_push($errors, $error);
				$cont_err++;
			     }
			     for($i=0; $i < $cont_hide; $i++)
			     {
				$hch_t = $hide_children[$i]->node_type();
				$hch_cont = $hide_children[$i]->get_content();
				if(($hch_t == 3)&&(is_hch_cont($hch_cont)))
				{
				  //echo "OK, SCANSIONE TAG HIDELMSUI TERMINATA";
				}
				else
				{
				   $error->type = "tag_error";
                                   $error->data->tag = $nome_figlio;
				   $error->data->value = $hch_cont;
                                   array_push($errors, $error);
				   $cont_err++;
				}
			     }
			  }
			}
	              }

		  }// END IF FIGLIO
		  if($figlio->has_child_nodes())
		  {	
			testNode($figlio);	//Ricorsione sui nodi figli
		  }
		  $figlio = $figlio->next_sibling();
	  }
  }

  if(count($errors) > 0)
  {
    return false;
  }
  else return true;	
}


?>
