<?php

namespace Benji07\Bundle\AkismetBundle\Tests;

use Benji07\Bundle\AkismetBundle\Akismet;

class AkismetTest extends \PHPUnit_Framework_TestCase
{
    protected $akismet = null;

    protected function setUp()
    {
        $this->akismet = new Akismet('http://benjamin.leveque.me', 'c7f92a636eac');
    }


    public function testVerifyKey()
    {
        $return = $this->akismet->verifyKey("c7f92a636eac", 'http://benjamin.leveque.me');

        $this->assertEquals(true, $return, 'Valid API key, must return true');

        $return = $this->akismet->verifyKey("a636ead",'toto');

        $this->assertEquals(false, $return, 'Invalid API key, must return false');

        $return = $this->akismet->verifyKey();

        $this->assertEquals(true, $return, 'Default API key, must return true');
    }

    public function testIsSpam()
    {
        try {
            $this->akismet->isSpam();
            $this->fail('isSpam must throw an exception when user_ip and user_agent are missing');
        }
        catch(\Exception $e) {
            $this->assertTrue(true, 'isSpam must throw an exception when user_ip and user_agent are missing');
        }

        try {
            $this->akismet->isSpam(array('user_agent' => 'Toto', 'user_ip' => '127.0.0.1'));
            $this->assertTrue(true,'isSpam must not throw an exception when user_ip and user_agent are missing');
        }
        catch(\Exception $e) {
            $this->fail('isSpam must not throw an exception when user_ip and user_agent are missing');
        }

        try {
            $akismet = new Akismet('http://benjamin.leveque.me', 'invalid');
            $akismet->isSpam(array('user_agent' => 'Toto', 'user_ip' => '127.0.0.1'));
            $this->fail('isSpam must throw an exception key is invalid');
        }
        catch(\Exception $e) {
            $this->assertTrue(true,'isSpam must throw an exception key is invalid');
        }

        $spam = array(
            'user_ip' => '52.33.8.5.160',
            'user_agent' => 'Firefox',
            'referrer' => 'http://benjamin.leveque.me',
            'comment_type' => 'comment',
            'comment_author' => 'viagra-test-123',
            'comment_author_email' => 'admin@example.com',
            'comment_content' => 'im a bad spam'
        );

        $not_spam = array(
            'user_ip' => '82.33.8.5.160',
            'user_agent' => 'Firefox',
            'referrer' => 'http://benjamin.leveque.me',
            'comment_type' => 'comment',
            'comment_author' => 'benjamin',
            'comment_author_email' => 'benjamin@leveque.me',
            'comment_content' => 'merci pour le commentaire'
        );

        $this->assertTrue($this->akismet->isSpam($spam));

        $this->assertFalse($this->akismet->isSpam($not_spam));
    }

    public function testSubmitSpam()
    {
        try {
            $akismet = new Akismet('http://benjamin.leveque.me', 'invalid');
            $akismet->submitSpam(array('user_agent' => 'Toto', 'user_ip' => '127.0.0.1'));
            $this->fail('submitSpam must throw an exception key is invalid');
        }
        catch(\Exception $e) {
            $this->assertTrue(true,'submitSpam must throw an exception key is invalid');
        }

        try {
            $this->akismet->submitSpam(array('user_agent' => 'Toto', 'user_ip' => '127.0.0.1'));
            $this->assertTrue(true,'submitSpam must throw an exception key is invalid');
        }
        catch(\Exception $e) {
            $this->fail('submitSpam must throw an exception key is invalid');
        }
    }

    public function testSubmitHam()
    {
        try {
            $akismet = new Akismet('http://benjamin.leveque.me', 'invalid');
            $akismet->submitHam(array('user_agent' => 'Toto', 'user_ip' => '127.0.0.1'));
            $this->fail('submitHam must throw an exception key is invalid');
        }
        catch(\Exception $e) {
            $this->assertTrue(true,'submitHam must throw an exception key is invalid');
        }

        try {
            $this->akismet->submitHam(array('user_agent' => 'Toto', 'user_ip' => '127.0.0.1'));
            $this->assertTrue(true,'submitHam must throw an exception key is invalid');
        }
        catch(\Exception $e) {
            $this->fail('submitHam must throw an exception key is invalid');
        }
    }
}