<?php

function smarty_function_permission($args){
    if(array_key_exists($args['controller'],$args['module_funciton_list']) && in_array($args['action'],$args['module_funciton_list'][$args['controller']]) )
    {
	
	 return TRUE;
    }
    else
    {
	    return false;
    }
}
?>
