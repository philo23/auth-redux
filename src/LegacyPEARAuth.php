<?php
namespace WBC\Auth;

class LegacyPEARAuth implements PEARAuthInterface
{
    const IDLED = -1;
    const EXPIRED = -2;
    const WRONG_LOGIN = -3;
    const METHOD_NOT_SUPPORTED = -4;
    const SECURITY_BREACH = -5;
    const CALLBACK_ABORT = -6;

    const LOG_INFO = 6;
    const LOG_DEBUG = 7;

    const ADV_IP_CHECK = 1;
    const ADV_USERAGENT = 2;
    const ADV_CHALLENGE = 3;

    /** @var Redux */
    private $redux;

    // PEAR Auth compatible properties
    /** @var int */
    public $expire = 0;
    /** @var bool */
    public $expired = false;
    /** @var int */
    public $idle = 0;
    /** @var bool */
    public $idled = false;

    /** @var callable */
    public $loginFunction = null;
    /** @var bool */
    public $showLogin = true;
    /** @var bool */
    public $allowLogin = true;

    /** @var int */
    public $status = 0;
    /** @var string */
    public $username = '';
    /** @var string */
    public $password = '';

    /** @var null|callable */
    public $checkAuthCallback = null;
    /** @var null|callable */
    public $loginFailedCallback = null;
    /** @var null|callable */
    public $loginCallback = null;
    /** @var null|callable */
    public $logoutCallback = null;

    /** @var string */
    private $_sessionName = '_authsession';
    /** @var string */
    public $version = 'REDUX';

    /** @var bool */
    public $advancedsecurity = false;

    /** @var string */
    public $_postUsername = 'username';
    /** @var string */
    public $_postPassword = 'password';

    public function __construct(Redux $redux, callable $loginFunction = null, $showLogin = true)
    {
        $this->redux = $redux;

        if (is_callable($loginFunction)) {
            $this->loginFunction = $loginFunction;
        }
        if (is_bool($showLogin)) {
            $this->showLogin = $showLogin;
        }
    }

    private function storePostData()
    {
        if (!empty($_POST[$this->_postUsername])) {
            $this->username = $_POST[$this->_postUsername];
        }
        if (!empty($_POST[$this->_postPassword])) {
            $this->password = $_POST[$this->_postPassword];
        }
    }

    /**
     * To help ease the transition from PEAR Auth
     * the following method can automatically define in the global
     * scope all of the AUTH_* constants PEAR Auth comes with.
     * These are available without calling this method by using
     * the full namespace and no AUTH_ prefix, for example:
     * \WBC\Auth\LegacyPEARAuth::WRONG_LOGIN
     */
    public static function defineLegacyConstants()
    {
        $constants = [
            'IDLED',
            'EXPIRED',
            'WRONG_LOGIN',
            'METHOD_NOT_SUPPORTED',
            'SECURITY_BREACH',
            'CALLBACK_ABORT',

            'LOG_INFO',
            'LOG_DEBUG',

            'ADV_IP_CHECK',
            'ADV_USERAGENT',
            'ADV_CHALLENGE'
        ];
        $class_name = get_called_class();
        foreach ($constants as $name) {
            define('AUTH_' . $name, constant($class_name . '::' . $name));
        }
    }

    public function start()
    {
        $this->storePostData();

        $this->redux->start();

        if (!$this->checkAuth() && $this->allowLogin) {
            $this->login();
        }
    }

    public function login()
    {
        $login_ok = false;

        if (!empty($this->username)) {
            try {
                $this->redux->login($this->username, $this->password);
            }
            catch (Exceptions\IncorrectPassword $e) {}
            catch (Exceptions\UnknownUsername $e) {}

            $login_ok = $this->redux->isLoggedIn();
        }

        if (!empty($this->username) && $login_ok && is_callable($this->loginCallback)) {
            call_user_func($this->loginCallback, $this->username, $this);
        }
        if (!empty($this->username) && !$login_ok) {
            $this->status = self::WRONG_LOGIN;
            if (is_callable($this->loginFailedCallback)) {
                call_user_func($this->loginFailedCallback, $this->username, $this);
            }
        }

        if ((empty($this->username) || !$login_ok) && $this->showLogin && is_callable($this->loginFunction)) {
            call_user_func($this->loginFunction, $this->username, $this->status, $this);
        }
    }

    public function logout()
    {
        $user = $this->redux->getUser();
        if (is_callable($this->logoutCallback) && $user) {
            call_user_func($this->logoutCallback, $user->getUsername(), $this);
        }

        $this->username = '';
        $this->password = '';

        $this->redux->logout();
    }

    public function changePassword($username, $password)
    {
        if ($this->username != $username) {
            return false;
        }

        $this->redux->changePassword($password);

        return true;
    }

    public function checkAuth()
    {
        $logged_in = $this->redux->isLoggedIn();
        if ($logged_in && is_callable($this->checkAuthCallback)) {
            $result = call_user_func($this->checkAuthCallback, $this->username, $this);
            if ($result === false) {
                $this->status = self::CALLBACK_ABORT;
                $this->logout();
                return false;
            }
        }
        return $logged_in;
    }

    public function getAuth()
    {
        return $this->checkAuth();
    }

    //- Basic data getters
    public function getUsername()
    {
        $user = $this->redux->getUser();
        if (!$user) {
            return null;
        }
        return $user->getUsername();
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getAuthData($key = null)
    {
        $user = $this->redux->getUser();
        if (!$user) {
            return null;
        } elseif ($key) {
            return array_key_exists($key, $user->data) ? $user->data[$key] : null;
        }
        return $user->data;
    }

    public function getPostUsernameField()
    {
        return $this->_postUsername;
    }

    public function getPostPasswordField()
    {
        return $this->_postPassword;
    }

    //- Callback setters
    public function setCheckAuthCallback(callable $callback)
    {
        $this->checkAuthCallback = $callback;
    }

    public function setFailedLoginCallback(callable $callback)
    {
        $this->loginFailedCallback = $callback;
    }

    public function setLoginCallback(callable $callback)
    {
        $this->loginCallback = $callback;
    }

    public function setLogoutCallback(callable $callback)
    {
        $this->logoutCallback = $callback;
    }

    public function setShowLogin($showLogin = true)
    {
        $this->showLogin = $showLogin;
    }

    public function setAllowLogin($allowLogin = true)
    {
        $this->allowLogin = $allowLogin;
    }


    //- Methods not implemented
    private static function notImpelemented()
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    public function setExpire($time, $add = false)
    {
        self::notImpelemented();
    }

    public function setIdle($time, $add = false)
    {
        self::notImpelemented();
    }

    public function setSessionName($name = 'session')
    {
        self::notImpelemented();
    }

    public function setAuth($username)
    {
        self::notImpelemented();
    }

    public function setAuthData($name, $value, $overwrite = true)
    {
        self::notImpelemented();
    }

    public function deleteAuthData($name)
    {
        self::notImpelemented();
    }

    public function log($message, $level)
    {
        self::notImpelemented();
    }

    public function attachLogObserver($observer)
    {
        self::notImpelemented();
    }

    public function removeUser($username)
    {
        self::notImpelemented();
    }

    public function addUser($username, $password, $additional = '')
    {
        self::notImpelemented();
    }

    public function listUsers()
    {
        self::notImpelemented();
    }

    public function sessionValidThru()
    {
        self::notImpelemented();
    }

    public function setAdvancedSecurity($flag = true)
    {
        self::notImpelemented();
    }

    public static function staticCheckAuth($options = null)
    {
        self::notImpelemented();
    }
}
