<?php
namespace SourceBundle\Exception;

use Symfony\Component\HttpKernel\Exception;

/**
 * AccessDeniedHttpException.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ConnectionException extends Exception\HttpException
{
	/**
	 * Constructor.
	 *
	 * @param string     $message  The internal exception message
	 * @param \Exception $previous The previous exception
	 * @param int        $code     The internal exception code
	 */
	public function __construct($message = null, \Exception $previous = null, $code = 412)
	{
		parent::__construct($code, $message, $previous);
	}
}