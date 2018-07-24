<?php
/**
 * Created by PhpStorm.
 * User: takuma_kanayama
 * Date: 2018/07/23
 * Time: 14:25
 */

namespace NP21\Test;

use NP21\Form\Mail;
use PHPUnit\Framework\TestCase;

class MailTest extends TestCase
{

    public function test__construct()
    {
        $mail = new Mail();
        $this->assertNotNull($mail);

        return $mail;
    }

    /**
     * @depends test__construct
     *
     * @param Mail $mail
     *
     * @return Mail
     */
    public function testFrom(Mail $mail)
    {
        $chain = $mail->from('from@foo.com');
        $this->assertEquals('from@foo.com', $mail->From);
        $this->assertEquals('', $mail->FromName);

        $mail->from('from@foo.com', 'from name');
        $this->assertEquals('from@foo.com', $mail->From);
        $this->assertEquals('from name', $mail->FromName);


        $this->assertEquals($mail, $chain);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @depends testFrom
     * @return Mail
     */
    public function testReplyTo(Mail $mail)
    {
        $chain = $mail->replyTo('replyTo@foo.com');
        $this->assertEquals(['replyto@foo.com' => ['replyTo@foo.com', '']], $mail->getReplyToAddresses());

        $mail->replyTo('replyTo@foo.com', 'replyTo name');
        $this->assertEquals(['replyto@foo.com' => ['replyTo@foo.com', 'replyTo name']], $mail->getReplyToAddresses());


        $this->assertEquals($mail, $chain);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @return Mail
     * @depends testReplyTo
     */
    public function testTo(Mail $mail)
    {
        $chain = $mail->to('to@foo.com');
        $this->assertEquals([['to@foo.com', '']], $mail->getToAddresses());

        $mail->to('to@foo.com', 'toto@foo.com');
        $this->assertEquals([['to@foo.com', ''], ['toto@foo.com', '']], $mail->getToAddresses());

        $mail->to(['to@foo.com', 'toto@foo.com']);
        $this->assertEquals([['to@foo.com', ''], ['toto@foo.com', '']], $mail->getToAddresses());

        $mail->to(['to@foo.com' => 'to name', 'toto@foo.com' => 'toto name']);
        $this->assertEquals([['to@foo.com', 'to name'], ['toto@foo.com', 'toto name']], $mail->getToAddresses());


        $this->assertEquals($mail, $chain);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @return Mail
     * @depends testTo
     */
    public function testCc(Mail $mail)
    {
        $chain = $mail->cc('cc@foo.com');
        $this->assertEquals([['cc@foo.com', '']], $mail->getCcAddresses());

        $mail->cc('cc@foo.com', 'cccc@foo.com');
        $this->assertEquals([['cc@foo.com', ''], ['cccc@foo.com', '']], $mail->getCcAddresses());

        $mail->cc(['cc@foo.com', 'cccc@foo.com']);
        $this->assertEquals([['cc@foo.com', ''], ['cccc@foo.com', '']], $mail->getCcAddresses());

        $mail->cc(['cc@foo.com' => 'cc name', 'cccc@foo.com' => 'cccc name']);
        $this->assertEquals([['cc@foo.com', 'cc name'], ['cccc@foo.com', 'cccc name']], $mail->getCcAddresses());


        $this->assertEquals($mail, $chain);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @return Mail
     * @depends testCc
     */
    public function testBcc(Mail $mail)
    {
        $chain = $mail->bcc('bcc@foo.com');
        $this->assertEquals([['bcc@foo.com', '']], $mail->getBccAddresses());

        $mail->bcc('bcc@foo.com', 'bccbcc@foo.com');
        $this->assertEquals([['bcc@foo.com', ''], ['bccbcc@foo.com', '']], $mail->getBccAddresses());

        $mail->bcc(['bcc@foo.com', 'bccbcc@foo.com']);
        $this->assertEquals([['bcc@foo.com', ''], ['bccbcc@foo.com', '']], $mail->getBccAddresses());

        $mail->bcc(['bcc@foo.com' => 'bcc name', 'bccbcc@foo.com' => 'bccbcc name']);
        $this->assertEquals([['bcc@foo.com', 'bcc name'], ['bccbcc@foo.com', 'bccbcc name']], $mail->getBccAddresses());


        $this->assertEquals($mail, $chain);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @return Mail
     * @depends testBcc
     */
    public function testTitle(Mail $mail)
    {
        $chain = $mail->title('mail subtitle');
        $this->assertEquals('mail subtitle', $mail->Subject);


        $this->assertEquals($mail, $chain);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @depends testTitle
     * @return Mail
     */
    public function testBody(Mail $mail)
    {
        $mail->Body = <<<BODY
test mail
PHPUnit test
BODY;
        $this->assertNotEmpty($mail->Body);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @return Mail
     * @depends testBody
     */
    public function testCsv(Mail $mail)
    {
        $chain = $mail->csv([['col1', 'col2'], ['val1', 'val2']], 'content.csv');
        $this->assertNotEmpty($mail->getAttachments());


        $this->assertEquals($mail, $chain);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @depends testCsv
     * @return Mail
     */
    public function testDebugMailtrap(Mail $mail)
    {
        $chain = $mail->debugMailtrap('52ae74d95d7141', '02d9224443a4c3');

        $this->assertEquals($mail, $chain);

        return $mail;
    }

    /**
     * @param Mail $mail
     *
     * @depends testDebugMailtrap
     * @return Mail
     */
    public function testSend(Mail $mail)
    {
        $this->assertTrue($mail->send(), $mail->ErrorInfo);

        return $mail;
    }
}
