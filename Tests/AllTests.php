<?php
require 'Bootstrap.php';

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();

        $suite->addTestSuite('AuthNetCIMTest');

        return $suite;
    }
}

AllTests::main();
