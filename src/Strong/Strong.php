<?php
/**
 * Strong Authentication Library
 *
 * User authentication and authorization library
 *
 * @license     MIT Licence
 * @category    Libraries
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @copyright   Copyright (c) 2012, Andrew Smith.
 * @version     1.0.0
 */

namespace Strong;

class Strong
{
    /**
     * @var array
     */
    protected $config = array(
        'name' => 'default',
    );

    /**
     * @const string
     */
    const VERSION = '1.0.0';

    /**
     * @var array[Strong]
     */
    protected static $apps = array();

    /**
     * @var Strong_Provider
     */
    protected $provider;

    /**
     * Factory method to call Strong and initalize
     *
     * @param array $config
     * @return Strong
     */
    public static function factory($config = array())
    {
        return new self($config);
    }

    /**
     * Get an existing instance of Strong using a
     * static method
     *
     * @param string $name
     * @return Strong
     */
    public static function getInstance($name = 'default')
    {
        return self::$apps[$name];
    }

    /**
     * Instantiate Strong and provide config for your settings
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        // Save the config in gloabal variable
        $this->setConfig($config);
        
        // Load the provider for access
        $this->provider = $this->config['provider'];

        //Set app name
        if ( !isset(self::$apps[$this->config['name']]) ) {
            $this->setName($this->config['name']);
        }
    }

    /**
     * User login check based on provider
     *
     * @return boolean
     */
    public function loggedIn()
    {
        return $this->provider->loggedIn();
    }

    /**
     * Protect a page, route, controller, url
     * @param string $name
     * @return boolean
     */
    public static function protect($name = 'default')
    {
        if ( ! Strong::getInstance($name)->loggedIn()) {
            return false;
        }
        return true;
    }

    /**
     * To authenticate user based on username or email
     * and password
     *
     * @param string $usernameOrEmail
     * @param string $password
     * @param boolean $remember
     * @return boolean
     */
    public function login($usernameOrEmail, $password, $remember = false)
    {
        if (empty($password)) {
            return false;
        }

        if (method_exists($this->provider, 'hashPassword') && is_string($password)) {
            $password = $this->provider->hashPassword($password);
        }

        return $this->provider->login($usernameOrEmail, $password, $remember);
    }

    /**
     * Log user out by deleting session key values or
     * deleting the session completely
     *
     * @param boolean $destroy
     * @return boolean
     */
    public function logout($destroy = false)
    {
        return $this->provider->logout($destroy);
    }

    /**
     * Get the users details stored in Session
     *
     * @return array
     */
    public function getUser()
    {
        return $this->provider->getUser();
    }

    /**
     * Set Strong application name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
        self::$apps[$name] = $this;
    }

    /**
     * Get Strong application name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Config for Strong Auth
     * @param array $config
     * @return Strong
     */
    public function setConfig($config = array())
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Get the Provider class being used specifically
     *
     * @return Strong_Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
