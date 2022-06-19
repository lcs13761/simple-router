<?php


namespace Simple\Session;

class Session
{
	/**
	 * Session constructor.
	 */
	public function __construct()
	{

		if (!session_id()) {
			session_start();
		};
	}

	/**
	 * @param $name
	 * @return null|mixed
	 */
	public function __get($name)
	{
		if (!empty($_SESSION[$name])) {
			return $_SESSION[$name];
		}
		return null;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->has($name);
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return Session
	 */
	public function set(string $key, $value): Session
	{
		$_SESSION[$key] = (is_array($value) ? (object)$value : $value);
		$this->has($key);
		return $this;
	}

	/**
	 * @param string $key
	 * @return Session
	 */
	public function unset(string $key): Session
	{
		unset($_SESSION[$key]);
		return $this;
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function has(string $key): bool
	{

		return isset($_SESSION[$key]);
	}

	/**
	 * CSRF Token
	 */
	public function csrf(): void
	{
		$_SESSION['csrf_token'] = md5(uniqid(rand(), true));
	}
}
