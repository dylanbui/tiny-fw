<?php
/**
 *
 * @View Class
 *
 */

class View
{
	
// 	private $layout_file = 'default';	
	
	public $_disableLayout = false;
	
	public $_default_layout_path = "default";
	
	/**
	 *
	 * The constructor, duh
	 *
	 */
	public function __construct($default_layout_path = "default")
	{
		$this->template_dir = __VIEW_PATH;
		$this->layout_dir = __LAYOUT_PATH;
		$this->_default_layout_path = $default_layout_path;
	}

	/**
	 * The variable property contains the variables
	 * that can be used inside of the templates.
	 *
	 * @access private
	 * @var array
	 */
	private $variables = array();

	/**
	 * The directory where the templates are stored
	 *
	 * @access private
	 * @var string
	 */
	private $template_dir = null;
	
	/**
	 * The directory where the templates are stored
	 *
	 * @access private
	 * @var string
	 */
	private $layout_dir = null;	

	/**
	 * Adds a variable that can be used by the templates.
	 *
	 * Adds a new array index to the variable property. This
	 * new array index will be treated as a variable by the templates.
	 *
	 * @param string $name The variable name to use in the template
	 *
	 * @param string $value The content you assign to $name
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @see getVars, $variables
	 *
	 */
	public function __set($name, $value)
	{
		$this->variables[$name] = $value;
	}
	
	/**
	 * @Returns names of all the added variables
	 *
	 * Returns a numeral array containing the names of all
	 * added variables.
	 *
	 * @access public
	 *
	 * @return array
	 *
	 * @see addVar, $variables
	 *
	 */
	public function getVars()
	{
		 $variables = array_keys($this->variables);
		 return !empty($variables) ? $variables : false;
	}

	/**
	 *
	 * Outputs the final template output
	 *
	 * Fetches the final template output, and echoes it to the browser.
	 *
	 * @param string $full_path Filename (with path) to the template you want to output
	 *
	 *
	 * @access public
	 *
	 * @return string
	 *
	 * @see parser
	 *
	 */
	public function parser($full_path,$args = array())
	{
		if (file_exists($full_path) == false)
		{
			show_error('View not found in '. $full_path);
		}
		
		$output = $this->getOutput($full_path,$args);
		return isset($output) ? $output : false;		
	}
	
	public function fetch($path,$args = array()) 
	{
		$path = $this->template_dir . '/' . $path . '.phtml';
			
		if (file_exists($path) == false)
		{
			show_error('View not found in '. $path);
		}

        // -- Fixed DucBui : 24/11/2015  --
//		if(!empty($args))
//			$this->variables = array_merge($this->variables,$args);
//		$output = $this->getOutput($path,$this->variables);

        $variables = $this->variables;
        if(!empty($args))
            $variables = array_merge($variables,$args);

        $output = $this->getOutput($path,$variables);

		return isset($output) ? $output : false;
	}
	
	public function renderLayout($path = null) 
	{
		if(is_null($path))
			$path = $this->layout_dir . "/{$this->_default_layout_path}/default.phtml";
		else 
			$path = $this->layout_dir . '/' . $path . '.phtml';
			
		if (file_exists($path) == false)
		{
			show_error('Layout not found in '. $path);
		}
		
		if($this->_disableLayout == true)
			return $this->variables['content'];
		
		$output = $this->getOutput($path,$this->variables);
		return isset($output) ? $output : false;		
	}

	/**
	 *
	 * Fetch the template output, and return it
	 *
	 * @param string $template_file Filename (with path) to the template to be processed
	 *
	 * @return string Returns a string on success, and FALSE on failure
	 *
	 * @access private
	 *
	 * @see fetch, display
	 *
	 */
	private function getOutput($template_file,$args = array())
	{
		$args['_view'] = $this;
		
		/*** extract all the variables ***/
		extract($args);
	
		if (file_exists($template_file))
		{
			ob_start();
			include($template_file);
			$output = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			show_error("The template file '$template_file' does not exist");
		}
		return !empty($output) ? $output : false;
	}

} /*** end of class ***/

?>
