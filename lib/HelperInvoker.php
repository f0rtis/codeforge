<?php

class HelperInvoker
{
	protected $builder;
	protected $candidates;
	protected $original_candidates;
	protected $args;
	
	public function __construct($builder, $candidates, $reusable=false)
	{
		$this->builder = $builder;
		$this->candidates = $candidates;
		if ($reusable) {
			$this->original_candidates = $candidates;
		}
	}
	
	public function call()
	{
		$this->args = func_get_args();
		return $this->_callInternal();
	}
	
	public function referSuper()
	{
		$args = func_get_args();
		if (count($args)) {
			$this->args = $args;
		}
		return $this->_callInternal();
	}
	
	public function fallback()
	{
		return $this->_callInternal();
	}
	
	protected function _callInternal()
	{
		$func = array_shift($this->candidates);
		if (is_object($func) && $func instanceof FunctionCaller) {
			return $func->call($this->args);
		} elseif (is_callable($func)) {
			$args = $this->args;
			array_unshift($args, $this);
			return call_user_func_array($func, $args);
		}
		return null;
	}
	
	public function refer()
	{
		$args = func_get_args();
		$name = array_shift($args);
		$invoker = $this->builder->invokeHelper($name);
		return call_user_func_array(array($invoker, 'call'), $args);
	}
	
	public function getEnv($name=null, $default='')
	{
		return $this->builder->getEnv($name, $default);
	}
	
	public function arrayMap($name, $array)
	{
		return $this->builder->arrayMap($name, $array);
	}
	
	public function reuse()
	{
		$this->candidates = $this->original_candidates;
	}
	
	public function getBuilder()
	{
		return $this->builder;
	}
}
