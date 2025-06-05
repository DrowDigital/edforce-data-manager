<?php
class SampleTest extends WP_UnitTestCase {

    public function test_plugin_class_exists() {
        $this->assertTrue( class_exists( 'Hp\EdforceDataManager\DataManager' ) );
    }


    public function test_plugin_functionality() {
        $this->assertTrue( function_exists( 'your_plugin_function' ) );
    }
}

