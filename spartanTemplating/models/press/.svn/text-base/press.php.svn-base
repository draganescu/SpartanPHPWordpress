<?php 	

/**
 * This class is an interface between the OO calls made in a spartan template
 * and the global functions defined by wordpress.
 */

class press {

	// function names for functions inside the loop
	var $bits = array();
 
	public function __call($function, $arguments) {
		$app = my::app();
		
		// this manages the loop and the functions that only work inside it
		if($app->current_action == 'render' && strpos($function, "the_") !== false)
		{

			// storing the bits allows the loop to know whick functions to call
			// and what tags to replace
			if(strpos($function, '@') !== false)
			{
				$this->bits[] = substr($function, 0, strpos($function, '@'));
				$function = strrchr($function,'@').'.'.substr($function, 0, strpos($function, '@'));
			}
			else
				$this->bits[] = $function;

			// we return the template tag that the loop will replace
			return "<!-- print.$function -->".$app->current_block."<!-- /print.$function -->";	
		}

		// this handles wp functions that return strings as arrays or simple strings to be placed in html
		$fetch = false;
		if(strpos($function, "fetch_") !== false)
		{
			$fetch = true;
			$function = str_replace("fetch_", '', $function);
		}

		// otherwise just call the function
		ob_start();
		$result = call_user_func_array($function, $arguments);
		$res = ob_get_clean();
		if($res != '') $result = $res;

		// for simple functions of wp that return a string we simply echo it
		if($app->current_action == 'print')
			return $result;

		// for simple wp functions that return a named string we build a render array
		if($fetch && !is_array($result))
			return array(0=>array($function=>$result));
		else if ($fetch && is_array($result))
			return array(0=>array($function=>implode(' ', $result)));

		// otherwise not much else to do that retun the value
		return $result;
	}

	/**
	* The loop is the basic wordpress code. We need it here because the other functions only work inside 
	* the loop. A better approach is to build an object that can handle each function outside the loop, although
	* it does sound like an over abstraction.
	*/

	public function the_loop($filters = false)
	{
		$app = my::app();
		global $wp_query, $page, $paged;

		if($filters !== false)
			query_posts( $filters . '&paged='. $paged );

		// go trough each post
		if(have_posts())
		{
			$result = array();
			while (have_posts()) {
				$i++;
				the_post();
				// for every called function we call the function and store the result
				foreach ($this->bits as $key => $bit) {
					$function = 'get_'.$bit;
					if(!function_exists($function))
						$function = str_replace('the_', '', $function);
					if(!function_exists($function))
						$function = str_replace('the_', '', $bit);
					ob_start();
					$result[$i][$bit] = apply_filters($bit,$function());
					ob_end_clean();
				}
			}
			
			if (  $wp_query->max_num_pages > 1 ) $app->paged = true;
			else $app->paged = false;
		}
		else
		{
			return '';
		}

		if($filters !== false)
			wp_reset_query();

		// clear the bits for other loop calls _in the same template_
		$this->reset_bits();

		// spartan knows what to do with this named array
		return $result;

	}

	// self explanatory, right?
	public function reset_bits()
	{
		$this->bits = array();
	}
}
