<?php


namespace Simple\Http\Security\Csrf;

use Simple\Http\Security\ITokenProvider;
use Simple\Session\Session;
use Exception;
use Simple\Http\Security\Exceptions\SecurityException;

class SessionTokenProvider implements ITokenProvider
{
    public const CSRF_KEY = 'CSRF-TOKEN';

    /**
     * @var string
     */
    protected $token;

    private $session;

    /**
     * SessionTokenProvider constructor.
     * @throws SecurityException
     */
    public function __construct()
    {
        $this->session = new Session();
        $key = static::CSRF_KEY;
        $this->token = ($this->hasToken() === true) ? $this->session->$key : null;

        if ($this->token === null) {
            $this->token = $this->generateToken();
        }
    }

    /**
       * Generate random identifier for CSRF token
       *
       * @return string
       * @throws SecurityException
       */
    public function generateToken(): string
    {
        try {
            return bin2hex(random_bytes(32));
        } catch (Exception $e) {
            throw new SecurityException($e->getMessage(), (int)$e->getCode(), $e->getPrevious());
        }
    }

    /**
    * Returns whether the csrf token has been defined
    * @return bool
    */
    public function hasToken(): bool
    {
        return $this->session->has(static::CSRF_KEY);
    }
    /**
     * Refresh existing token
     */
    public function refresh(): void
    {
        if ($this->token !== null) {
            $this->setToken($this->token);
        }
    }

    /**
     * Validate valid CSRF token
     *
     * @param string $token
     * @return bool
     */
    public function validate(string $token): bool
    {
        if ($this->getToken() !== null) {
            return hash_equals($token, $this->getToken());
        }

        return false;
    }

    /**
     * Get token token
     *
     * @param string|null $defaultValue
     * @return string|null
     */
    public function getToken(?string $defaultValue = null): ?string
    {
        return $this->token ?? $defaultValue;
    }
    /**
     * Set csrf token cookie
     * Overwrite this method to save the token to another storage like session etc.
     *
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
        $this->session->set(static::CSRF_KEY, $token);
        //setcookie(static::CSRF_KEY, $token, time() + (60 * $this->cookieTimeoutMinutes), '/', ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
    }
}
