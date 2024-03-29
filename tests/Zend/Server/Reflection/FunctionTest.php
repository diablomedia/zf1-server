<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */


/**
 * Test case for Zend_Server_Reflection_Function
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_Reflection_FunctionTest extends PHPUnit\Framework\TestCase
{
    public function test__construct()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r        = new Zend_Server_Reflection_Function($function);
        $this->assertInstanceOf(Zend_Server_Reflection_Function::class, $r);
        $this->assertInstanceOf(Zend_Server_Reflection_Function_Abstract::class, $r);
        $params = $r->getParameters();
        try {
            $r = new Zend_Server_Reflection_Function($params[0]);
            $this->fail('Should not be able to construct with non-function');
        } catch (Exception $e) {
            // do nothing
        }

        $r = new Zend_Server_Reflection_Function($function, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());

        $argv = array('string1', 'string2');
        $r    = new Zend_Server_Reflection_Function($function, 'namespace', $argv);
        $this->assertIsArray($r->getInvokeArguments());
        $this->assertTrue($argv === $r->getInvokeArguments());

        $prototypes = $r->getPrototypes();
        $this->assertIsArray($prototypes);
        $this->assertTrue(0 < count($prototypes));
    }

    public function test__getSet()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r        = new Zend_Server_Reflection_Function($function);

        $r->system = true;
        $this->assertTrue($r->system);
    }


    public function testNamespace()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r        = new Zend_Server_Reflection_Function($function, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
        $r->setNamespace('framework');
        $this->assertEquals('framework', $r->getNamespace());
    }

    public function testDescription()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r        = new Zend_Server_Reflection_Function($function);
        $this->assertStringContainsString('function for reflection', $r->getDescription());
        $r->setDescription('Testing setting descriptions');
        $this->assertEquals('Testing setting descriptions', $r->getDescription());
    }

    public function testGetPrototypes()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r        = new Zend_Server_Reflection_Function($function);

        $prototypes = $r->getPrototypes();
        $this->assertIsArray($prototypes);
        $this->assertTrue(0 < count($prototypes));
        $this->assertCount(8, $prototypes);

        foreach ($prototypes as $p) {
            $this->assertInstanceOf(Zend_Server_Reflection_Prototype::class, $p);
        }
    }

    public function testGetPrototypes2()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function2');
        $r        = new Zend_Server_Reflection_Function($function);

        $prototypes = $r->getPrototypes();
        $this->assertIsArray($prototypes);
        $this->assertTrue(0 < count($prototypes));
        $this->assertCount(1, $prototypes);

        foreach ($prototypes as $p) {
            $this->assertInstanceOf(Zend_Server_Reflection_Prototype::class, $p);
        }
    }


    public function testGetInvokeArguments()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r        = new Zend_Server_Reflection_Function($function);
        $args     = $r->getInvokeArguments();
        $this->assertIsArray($args);
        $this->assertCount(0, $args);

        $argv = array('string1', 'string2');
        $r    = new Zend_Server_Reflection_Function($function, null, $argv);
        $args = $r->getInvokeArguments();
        $this->assertIsArray($args);
        $this->assertCount(2, $args);
        $this->assertTrue($argv === $args);
    }

    public function test__wakeup()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r        = new Zend_Server_Reflection_Function($function);
        $s        = serialize($r);
        $u        = unserialize($s);
        $this->assertInstanceOf(Zend_Server_Reflection_Function::class, $u);
        $this->assertEquals('', $u->getNamespace());
    }

    public function testMultipleWhitespaceBetweenDoctagsAndTypes()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function3');
        $r        = new Zend_Server_Reflection_Function($function);

        $prototypes = $r->getPrototypes();
        $this->assertIsArray($prototypes);
        $this->assertTrue(0 < count($prototypes));
        $this->assertCount(1, $prototypes);

        $proto  = $prototypes[0];
        $params = $proto->getParameters();
        $this->assertIsArray($params);
        $this->assertCount(1, $params);
        $this->assertEquals('string', $params[0]->getType());
    }

    /**
     * @group ZF-6996
     */
    public function testParameterReflectionShouldReturnTypeAndVarnameAndDescription()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r        = new Zend_Server_Reflection_Function($function);

        $prototypes = $r->getPrototypes();
        $prototype  = $prototypes[0];
        $params     = $prototype->getParameters();
        $param      = $params[0];
        $this->assertStringContainsString('Some description', $param->getDescription(), var_export($param, 1));
    }
}

/**
 * Zend_Server_Reflection_FunctionTest_function
 *
 * Test function for reflection unit tests
 *
 * @param string $var1 Some description
 * @param string|array $var2
 * @param array $var3
 * @return null|array
 */
function Zend_Server_Reflection_FunctionTest_function($var1, $var2, $var3 = null)
{
}

/**
 * Zend_Server_Reflection_FunctionTest_function2
 *
 * Test function for reflection unit tests; test what happens when no return
 * value or params specified in docblock.
 */
function Zend_Server_Reflection_FunctionTest_function2()
{
}

/**
 * Zend_Server_Reflection_FunctionTest_function3
 *
 * @param  string $var1
 * @return void
 */
function Zend_Server_Reflection_FunctionTest_function3($var1)
{
}
