<?php

namespace Benji\AkismetBundle;

/**
 * Akismet class
 *
 * @author Benjamin Lévêque <benjamin@leveque.me>
 */
class Akismet
{
    protected $user_agent = 'Symfony2 | Akismet 1.11';

    protected $key = null;

    protected $blog = null;

    public function __construct($url, $key)
    {
        $this->key = $key;
        $this->blog = $url;
    }

    public function isSpam(array $params = array())
    {
        $this->checkRequirements($params);

        $response = $this->post($this->key.'.rest.akismet.com', '/1.1/comment-check', $params);

        if($response == 'invalid') {
            throw new \Exception('Invalid API Key');
        }

        if($response == 'true') {
            return true;
        }

        return false;
    }

    public function submitSpam(array $params = array())
    {
        $this->checkRequirements($params);

        $response = $this->post($this->key.'.rest.akismet.com','/1.1/submit-spam', $params);

        if($response === 'invalid') {
            throw new \Exception('Invalid API Key');
        }
    }

    public function submitHam(array $params = array())
    {
        $this->checkRequirements($params);

        $response = $this->post($this->key.'.rest.akismet.com','/1.1/submit-ham', $params);

        if($response === 'invalid') {
            throw new \Exception('Invalid API Key');
        }
    }

    /**
     * Verify an api key
     *
     * @param string $key
     * @param string $blog
     * @return boolean
     */
    public function verifyKey($key = null, $blog = null)
    {
        $response = $this->post('rest.akismet.com', '/1.1/verify-key', array(
           'key' => $key,
           'blog' => $blog
        ));

        return $response === 'valid';
    }

    protected function checkRequirements(array $params = array())
    {
        if (empty($params['user_ip']) || empty($params['user_agent'])) {
            throw new \Exception('Missing required Akismet fields (user_ip and user_agent are required)');
        }
    }

    protected function post($host, $path, array $params = array())
    {
        if(!isset($params['key']) || empty($params['key'])) {
            $params['key'] = $this->key;
        }

        if(!isset($prams['blog']) || empt($params['blog'])) {
            $params['blog'] = $this->blog;
        }

        $content = \http_build_query($params);

        $request = array(
            'POST '.$path.' HTTP/1.0',
            'Host: '.$host,
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Content-Length: '.\strlen($content),
            'User-Agent: '.$this->user_agent,
            '',
            $content
        );

        $conn = \fsockopen($host, 80, $errno, $errstr, 3);

        $response = '';

        if($conn != null) {
            \fwrite($conn, implode("\r\n",$request));

            while(!\feof($conn)) {
                $response .= \fgets($conn, 1160);
            }

            \fclose($conn);

            list($header, $response) = explode("\r\n\r\n",$response);
        }

        return \trim($response);
    }
}