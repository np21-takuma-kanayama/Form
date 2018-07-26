<?php

namespace NP21\Form;

use PHPMailer\PHPMailer\PHPMailer;


/**
 * 日本語仕様のPHPMailer
 * CSVの添付も出来るように
 *
 * @package NP21\Form
 */
class Mail extends PHPMailer
{
    /**
     * @var string 本文をセット
     */
    public $Body;

    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);
        $this->Encoding = '7bit';
        $this->CharSet = 'ISO-2022-JP';
    }

    /**
     * fromをセット
     *
     * @param string $address メールアドレス
     * @param string $name    名前
     *
     * @return $this
     */
    public function from($address, $name = '')
    {
        $this->setFrom($address, $name);

        return $this;
    }

    /**
     * reply-toをセット
     *
     * @param string $address メールアドレス
     * @param string $name    名前
     *
     * @return $this
     */
    public function replyTo($address, $name = '')
    {
        $this->clearReplyTos();
        $this->addReplyTo($address, $name);

        return $this;
    }

    /**
     * toをセット
     *
     * @param string|array $address メールアドレスの配列またはメールアドレスをキーとした名前の配列
     *
     * @return $this
     */
    public function to($address)
    {
        $this->clearAddresses();
        $address = is_array($address) ? $address : func_get_args();
        foreach ($this->ConvertArrayKey($address) as $key => $value) {
            $this->addAddress($key, $value);
        }

        return $this;
    }

    /**
     * 配列の内容をキーにセット
     * キーが文字列の場合そのままに
     *
     * @example ['mail','mail2' => name] -> ['mail' => '','mail2' => 'name']
     *
     * @param string|array $array
     *
     * @return array
     */
    private function ConvertArrayKey($array)
    {
        $return = [];
        foreach ($array as $key => $item) {
            if (is_numeric($key)) {
                $return[$item] = '';
            } else {
                $return[$key] = $item;
            }
        }

        return $return;
    }

    /**
     * ccをセット
     *
     * @param string|array $address メールアドレスの配列またはメールアドレスをキーとした名前の配列
     *
     * @return $this
     */
    public function cc($address)
    {
        $this->clearCCs();
        $address = is_array($address) ? $address : func_get_args();
        foreach ($this->ConvertArrayKey($address) as $key => $value) {
            $this->addCC($key, $value);
        }

        return $this;
    }

    /**
     * bccをセット
     *
     * @param string|array $mails メールアドレスの配列またはメールアドレスをキーとした名前の配列
     *
     * @return $this
     */
    public function bcc($mails)
    {
        $this->clearBCCs();
        $mails = is_array($mails) ? $mails : func_get_args();
        foreach ($this->ConvertArrayKey($mails) as $key => $value) {
            $this->addBCC($key, $value);
        }

        return $this;
    }

    /**
     * 件名をセット
     *
     * @param string $string
     *
     * @return $this
     */
    public function title($string)
    {
        $this->Subject = mb_encode_mimeheader($string);

        return $this;
    }

    /**
     * メールを送信
     *
     * @return bool falseの場合エラーが発生しています - See the ErrorInfo property for details of the error
     */
    public function send()
    {
        $this->Body = mb_convert_encoding($this->Body, 'JIS', 'UTF-8');
        $return = false;
        try {
            $return = parent::send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
        }
        $this->Body = mb_convert_encoding($this->Body, 'UTF-8', 'JIS');
        return $return;
    }

    /**
     * CSVファイルの作成、添付
     *
     * @param array  $array    CSVデータ(2重array)
     * @param string $filename 添付の際に使用するファイル名(拡張子必須)
     *
     * @return $this
     */
    public function csv(array $array, $filename)
    {
        mb_convert_variables('Shift_JIS', 'UTF-8', $array);
        $this->addStringAttachment($this->to_csv($array), $filename);
        return $this;
    }

    /**
     * 配列をCSVなStringに変換
     *
     * @param array $data
     *
     * @return bool|string
     */
    private function to_csv(array $data)
    {
        $mp = fopen('php://temp', 'wb+');
        foreach ($data as $row) {
            fputcsv($mp, $row);
        }
        rewind($mp);
        return stream_get_contents($mp);
    }

    /**
     * 全てのメールをMailtrapへ飛ばすようにする
     *
     * !!! デバック用 !!!
     *
     * @param string $username
     * @param string $password
     *
     * @return Mail
     */
    public function debugMailtrap($username, $password)
    {
        $this->isSMTP();
        $this->Host = 'smtp.mailtrap.io';
        $this->SMTPAuth = true;
        $this->Username = $username;
        $this->Password = $password;
        $this->SMTPSecure = 'tls';
        $this->Port = '2525';

        return $this;
    }

}