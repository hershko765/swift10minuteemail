<?php
namespace SourceBundle\Exception;

use SourceBundle\Helpers\Arr;
use Symfony\Component\HttpKernel\Exception;

/**
 * AccessDeniedHttpException.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ValidationException extends Exception\HttpException
{
	public $errors;

	/**
	 * Constructor.
	 *
	 * @param string     $message  The internal exception message
	 * @param \Exception $previous The previous exception
	 * @param int        $code     The internal exception code
	 */
	public function __construct($message = null, \Exception $previous = null, $headers = [], $code = 412)
	{

		if (is_array($message)) {
			$this->errors = $message;
			while(is_array($message))
			{
				$message = Arr::get(array_values($message), 0, 'UNRECOGNIZED_ERROR');
			}
		}
		parent::__construct($code, $message, $previous, $headers, $code = 412);
	}
}