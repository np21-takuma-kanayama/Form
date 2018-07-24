<?php
/**
 * Created by PhpStorm.
 * User: takuma_kanayama
 * Date: 2018/07/23
 * Time: 10:12
 */

namespace takuma_kanayama\Test;

use NP21\Form\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    /**
     * @var Request
     */
    private $Request;

    private $test       = 'test';
    private $html       = '<div>test</div>';
    private $array      = [
        '1st',
        '2nd',
        '3rd',
        '4th',
        '5th',
    ];
    private $array_html = [
        '<div>1st</div>',
        '<div>2nd</div>',
        '<div>3rd</div>',
        '<div>4th</div>',
        '<div>5th</div>',
    ];

    /**
     * @return Request
     */
    public function test__construct()
    {
        $this->Request = new Request();
        $this->assertNotNull($this->Request);

        return $this->Request;
    }

    /**
     * @depends test__construct
     *
     * @param Request $request
     *
     * @return Request
     */
    public function test__set(Request $request)
    {
        $request->test = $this->test;
        $request->html = $this->html;
        $request->array = $this->array;
        $request->array_html = $this->array_html;

        $this->assertTrue(true);
        return $request;
    }

    /**
     * @depends test__set
     *
     * @param Request $request
     */
    public function testRawGet(Request $request)
    {
        $this->assertEquals($request->rawGet('test'), $this->test);
        $this->assertEquals($request->rawGet('html'), $this->html);
        $this->assertEquals($request->rawGet('array'), $this->array);
        $this->assertEquals($request->rawGet('array_html'), $this->array_html);

        $this->assertEquals($request->rawGet('foo'), null);
        $this->assertEquals($request->rawGet('bar', '<div>default</div>'), '<div>default</div>');
    }

    /**
     * @param Request $request
     *
     * @depends test__set
     */
    public function testInput(Request $request)
    {
        ob_start();
        $request->input(['test']);
        $echo = ob_get_clean();
        $this->assertEquals("<input type='hidden' name='html' value='&lt;div&gt;test&lt;/div&gt;'><input type='hidden' name='array[]' value='1st'><input type='hidden' name='array[]' value='2nd'><input type='hidden' name='array[]' value='3rd'><input type='hidden' name='array[]' value='4th'><input type='hidden' name='array[]' value='5th'><input type='hidden' name='array_html[]' value='&lt;div&gt;1st&lt;/div&gt;'><input type='hidden' name='array_html[]' value='&lt;div&gt;2nd&lt;/div&gt;'><input type='hidden' name='array_html[]' value='&lt;div&gt;3rd&lt;/div&gt;'><input type='hidden' name='array_html[]' value='&lt;div&gt;4th&lt;/div&gt;'><input type='hidden' name='array_html[]' value='&lt;div&gt;5th&lt;/div&gt;'>", $echo);
    }

    /**
     * @param Request $request
     *
     * @depends test__set
     */
    public function testImplode(Request $request)
    {
        $imploded = $request->implode(',', 'array_html');
        $this->assertEquals('&lt;div&gt;1st&lt;/div&gt;,&lt;div&gt;2nd&lt;/div&gt;,&lt;div&gt;3rd&lt;/div&gt;,&lt;div&gt;4th&lt;/div&gt;,&lt;div&gt;5th&lt;/div&gt;', $imploded);
    }

    /**
     * @param Request $request
     *
     * @depends test__set
     */
    public function testGet(Request $request)
    {
        $this->assertEquals($request->get('test'), $this->test);
        $this->assertEquals($request->get('html'), '&lt;div&gt;test&lt;/div&gt;');
        $this->assertEquals($request->get('array'), $this->array);
        $this->assertEquals($request->get('array_html'), [
            '&lt;div&gt;1st&lt;/div&gt;',
            '&lt;div&gt;2nd&lt;/div&gt;',
            '&lt;div&gt;3rd&lt;/div&gt;',
            '&lt;div&gt;4th&lt;/div&gt;',
            '&lt;div&gt;5th&lt;/div&gt;',
        ]);

        $this->assertEquals($request->get('foo'), null);
        $this->assertEquals($request->get('bar', '<div>default</div>'), '&lt;div&gt;default&lt;/div&gt;');
    }

    /**
     * @param Request $request
     *
     * @depends test__set
     */
    public function test__get(Request $request)
    {
        $this->assertEquals($request->test, $this->test);
        $this->assertEquals($request->html, '&lt;div&gt;test&lt;/div&gt;');
        $this->assertEquals($request->array, $this->array);
        $this->assertEquals($request->array_html, [
            '&lt;div&gt;1st&lt;/div&gt;',
            '&lt;div&gt;2nd&lt;/div&gt;',
            '&lt;div&gt;3rd&lt;/div&gt;',
            '&lt;div&gt;4th&lt;/div&gt;',
            '&lt;div&gt;5th&lt;/div&gt;',
        ]);

        $this->assertEquals($request->foo, null);
    }

    /**
     * @param Request $request
     *
     * @depends test__set
     */
    public function test__isset(Request $request)
    {
        $this->assertTrue(isset($request->test));
        $this->assertTrue(isset($request->html));
        $this->assertTrue(isset($request->array));
        $this->assertTrue(isset($request->array_html));

        $this->assertFalse(isset($request->foo));
    }

    /**
     * @param Request $request
     *
     * @depends test__set
     */
    public function testHas(Request $request)
    {
        $this->assertTrue($request->has('test'));
        $this->assertTrue($request->has('html'));
        $this->assertTrue($request->has('array'));
        $this->assertTrue($request->has('array_html'));

        $this->assertFalse($request->has('foo'));
    }

    /**
     * @param Request $request
     *
     * @depends test__set
     */
    public function testIs_array(Request $request)
    {
        $this->assertFalse($request->is_array('test'));
        $this->assertFalse($request->is_array('html'));
        $this->assertTrue($request->is_array('array'));
        $this->assertTrue($request->is_array('array_html'));

        $this->assertFalse($request->is_array('foo'));
    }

    /**
     * @param Request $request
     *
     * @depends test__set
     */
    public function test__unset(Request $request)
    {
        unset($request->test, $request->html, $request->array, $request->array_html);

        $this->assertFalse($request->has('test'));
        $this->assertFalse($request->has('html'));
        $this->assertFalse($request->has('array'));
        $this->assertFalse($request->has('array_html'));
    }
}
