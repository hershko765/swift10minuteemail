<?php

/**
 * ****DESCRIPTION OF FILE****
 *
 * @package    Sortex
 * @author     Sortex Systems Development Ltd.
 * @copyright  (c) 2011-2013 Sortex
 * @license    BSD
 * @link       http://www.sortex.co.il
 */
class Converter {

	public function String($val)
	{
		return ((string) $val).'';
	}

	public function Integer($val)
	{
		return (int) $val + 0;
	}

	public function JSON($val)
	{
		return is_array($val) ? json_encode($val) : '['.$val.']';
	}

	public function Bool($val)
	{
		return $val == 'false' ? FALSE : (bool) $val;
	}

} // End Converter