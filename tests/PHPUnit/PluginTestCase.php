<?php

namespace Sofokus\WooCommerceCustobar\Tests\PHPUnit;

/**
 * Class PluginTestCase
 *
 * @package Sofokus\WooCommerceCustobar\Tests\PHPUnit
 */
class PluginTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * PluginTestCase constructor.
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        global $wpdb;

        parent::__construct($name, $data, $dataName);

        $this->wpdb = $wpdb;
    }
    /**
     * Set up before testing.
     *
     * Start database transactions.
     */
    public function setUp()
    {
        global $wpdb;

        $wpdb->query('BEGIN;');
    }

    /**
     * Teardown after testing.
     *
     * Rollback database transaction.
     */
    public function tearDown()
    {
        global $wpdb;

        $wpdb->query('ROLLBACK');
    }
}
