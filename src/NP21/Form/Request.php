<?php

namespace NP21\Form;


/**
 * リクエストクラス
 *
 * このクラスから出る値は全てHTMLエスケープされた状態
 * Laravelの\Illuminate\Http\Requestを意識
 *
 * @package NP21\Form
 */
class Request
{
    private $data;

    /**
     * Request constructor.
     *
     * @param string $method メソッド名(postかget)
     */
    public function __construct($method = 'post')
    {
        if ('get' === strtolower($method)) {
            $this->data = $_GET;
        } elseif ('post' === strtolower($method)) {
            $this->data = $_POST;
        } else {
            throw new \RuntimeException('存在しないメソッド');
        }
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * キーが存在しているか調べる
     *
     * @param string $name キー名
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->rawGet($name) !== null;
    }

    /**
     * エスケープしていない値の取得
     *
     * 危険なので常用はしない事
     *
     * @param string      $name    キー名
     * @param string|null $default 存在しない場合のデフォルト値
     *
     * @return null|string|array
     */
    public function rawGet($name, $default = null)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return $default;
    }

    /**
     * 配列要素を文字列により連結する
     *
     * @param string $glue 要素ごとに挟む文字列
     * @param string $name キー名
     *
     * @return string
     */
    public function implode($glue, $name)
    {
        if ($this->is_array($name)) {
            return implode($glue, $this->get($name));
        }
        return $this->get($name);
    }

    /**
     * 値が配列か調べる
     *
     * @param string $name キー名
     *
     * @return bool
     */
    public function is_array($name)
    {
        return is_array($this->get($name));
    }

    /**
     * 値の取得(HTMLエスケープ済み)
     *
     * @param string      $name    キー名
     * @param string|null $default 存在しない場合のデフォルト値
     *
     * @return null|string|array
     */
    public function get($name, $default = null)
    {
        return $this->HtmlEscape($this->rawGet($name, $default));
    }

    /**
     * HTMLエスケープ
     *
     * @param string|array $val
     *
     * @return string|array
     */
    private function HtmlEscape($val)
    {
        if (!is_array($val)) {
            return htmlspecialchars($val, ENT_QUOTES);
        }
        foreach ($val as &$item) {
            $item = htmlspecialchars($item, ENT_QUOTES);
        }
        return $val;

    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * hiddenなinputとして全てのデータを出力する
     *
     * @param array $exclude 除外したいキー名の配列
     */
    public function input(array $exclude = [])
    {
        foreach ($this->data as $name => $value) {
            if (!in_array($name, $exclude, true)) {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $val = htmlspecialchars($val, ENT_QUOTES);
                        echo "<input type='hidden' name='{$name}[]' value='$val'>";
                    }
                } else {
                    $value = htmlspecialchars($value, ENT_QUOTES);
                    echo "<input type='hidden' name='$name' value='$value'>";
                }
            }
        }
    }
}
