<?php
namespace pmill\AwsCognito\Exception;

use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Exception;
use Throwable;

class CognitoResponseException extends Exception
{
    /**
     * CognitoResponseException constructor.
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct(get_class(), 0, $previous);
    }

    /**
     * @param CognitoIdentityProviderException $e
     * @return Exception
     */
    public static function createFromCognitoException(CognitoIdentityProviderException $e)
    {
        $awsErrorCode = $e->getAwsErrorCode();
        $awsErrorMessage = $e->getAwsErrorMessage();

        $errorClass = 'pmill\\AwsCognito\\Exception\\';
        if ($awsErrorCode === 'NotAuthorizedException' and stristr($awsErrorMessage, 'Access Token has expired') !== false) {
            $errorClass .= 'AccessTokenExpiredException';
        } elseif ($awsErrorCode === 'NotAuthorizedException' and stristr($awsErrorMessage, 'Invalid Refresh Token') !== false) {
            $errorClass .= 'InvalidRefreshTokenException';
        } else {
            $errorClass .= $awsErrorCode;
        }

        if (class_exists($errorClass)) {
            return new $errorClass($e);
        }

        return $e;
    }
}
