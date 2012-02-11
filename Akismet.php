<?php

namespace Benji07\Bundle\AkismetBundle;

/**
 * Akismet class
 *
 * @author Benjamin Lévêque <benjamin@leveque.me>
 */
class Akismet
{
    protected $userAgent = 'Symfony2 | Akismet 1.11';

    protected $key = null;

    protected $blog = null;

    /**
     * __construct
     *
     * @param string $url the url of your website
     * @param string $key the api key
     */
    public function __construct($url, $key)
    {
        $this->key = $key;
        $this->blog = $url;
    }

    /**
     * Check if the params is a spam
     *
     * @param array $params params
     *
     * @return boolean
     */
    public function isSpam(array $params = array())
    {
        $this->checkRequirements($params);

        $response = $this->post($this->key.'.rest.akismet.com', '/1.1/comment-check', $params);

        if ($response == 'invalid') {
            throw new \Exception('Invalid API Key');
        }

        if ($response == 'true') {
            return true;
        }

        return false;
    }

    /**
     * Submit a comment as spam
     *
     * @param array $params params
     */
    public function submitSpam(array $params = array())
    {
        $this->checkRequirements($params);

        $response = $this->post($this->key.'.rest.akismet.com', '/1.1/submit-spam', $params);

        if ($response === 'invalid') {
            throw new \Exception('Invalid API Key');
        }
    }

    /**
     * Submit a comment as Ham
     *
     * @param array $params params
     */
    public function submitHam(array $params = array())
    {
        $this->checkRequirements($params);

        $response = $this->post($this->key.'.rest.akismet.com', '/1.1/submit-ham', $params);

        if ($response === 'invalid') {
            throw new \Exception('Invalid API Key');
        }
    }

    /**
     * Verify an api key
     *
     * @param string $key  your api key
     * @param string $blog your website url
     *
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

    /**
     * Checkf if params contains required data
     *
     * @param array $params params
     */
    protected function checkRequirements(array $params = array())
    {
        if (empty($params['user_ip']) || empty($params['user_agent'])) {
            throw new \Exception('Missing required Akismet fields (user_ip and user_agent are required)');
        }
    }

    /**
     * Send a request to Akismet server
     *
     * @param string $host   the hostname
     * @param string $path   the path
     * @param array  $params the data to send
     *
     * @return string
     */
    protected function post($host, $path, array $params = array())
    {
        if (!isset($params['key']) || empty($params['key'])) {
            $params['key'] = $this->key;
        }

        if (!isset($prams['blog']) || empt($params['blog'])) {
            $params['blog'] = $this->blog;
        }

        $content = \http_build_query($params);

        $request = array(
            'POST '.$path.' HTTP/1.0',
            'Host: '.$host,
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Content-Length: '.\strlen($content),
            'User-Agent: '.$this->userAgent,
            '',
            $content
        );

        $conn = \fsockopen($host, 80, $errno, $errstr, 3);

        $response = '';

        if ($conn != null) {
            \fwrite($conn, implode("\r\n", $request));

            while (!\feof($conn)) {
                $response .= \fgets($conn, 1160);
            }

            \fclose($conn);

            list($header, $response) = explode("\r\n\r\n", $response);
        }

        return \trim($response);
    }
}