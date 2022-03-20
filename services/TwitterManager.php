<?php


namespace Services;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class TwitterManager
 * @package Services
 */
class TwitterManager {
    private $key;
    private $secret;
    private $token;
    private $token_secret;

    public function __construct($key, $secret, $token, $token_secret){
        $this->key = $key;
        $this->secret = $secret;
        $this->token = $token;
        $this->token_secret = $token_secret;
    }

    /**
     * @return TwitterOAuth
     */
    public function getConnection(): TwitterOAuth
    {
        // Connexion à l'API de Twitter
        return new TwitterOAuth($this->key, $this->secret, $this->token, $this->token_secret);
    }
}