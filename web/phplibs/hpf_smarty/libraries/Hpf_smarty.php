<?php
if (! defined('BASEPATH'))
	exit('No direct script access allowed');
require_once (dirname(__FILE__) . '/../../Smarty_3/Smarty.class.php');

class hpf_smarty extends Smarty
{
	function __construct()
	{
		parent::__construct();
		$this->setConfigDir("resource".DS."smarty".DS."config" . DS); // "." . DS .
		$this->setCacheDir("resource".DS."smarty".DS."cache" . DS);
		$this->setCompileDir("resource".DS."smarty".DS."compile" . DS);
		$this->setTemplateDir(APPPATH . 'views');
		$this->caching=false;
		$this->left_delimiter='{';
		$this->right_delimiter='}';
	}
}