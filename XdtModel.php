<?php
/**
 * @package Xdt Ajax DataTable Server Side
 * @category DataTable rendering Class
 * @version 1.0.0
 * @author JIHAD SINNAOUR
 * @copyright (c) 2018 JIHAD SINNAOUR <j.sinnaour.official@gmail.com>
 * @license MIT
 * @link https://jakiboy.github.io/Xdt/
 */

namespace Xdt;

class XdtModel extends Db implements XdtModelInterface
{
    /**
     * @access public
     */
    public $total = ''; // Total filtred

    /**
     * @access private
     */
    private $bind = [];

    /**
     * Connect to database
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        $this->log = new Logger();
        $this->connect();
        $this->parameters = [];
    }

    /**
     * Count data
     *
     * @param string $table
     * @return string|int
     */
    public function count($table)
    {
    	$sql = "SELECT COUNT(*) FROM $table";
    	return $this->single($sql);
    }

    /**
     * Get data
     *
     * @param string $table, array $columns, array $request
     * @return array
     */
    public function get($table, $columns, $request)
    {
    	$rows = implode(', ',$columns);
    	$sql  = "SELECT {$rows} FROM {$table} ORDER BY ";

    	$order  = $columns[$request['order'][0]['column']];
    	$dir    = $request['order'][0]['dir'];
    	$start  = $request['start'];
    	$length = $request['length'];

    	$sql .= "$order $dir LIMIT $start, $length";
    	return $this->query($sql);
    }

    /**
     * Get search data
     *
     * @param string $table, array $columns, array $request
     * @return array
     */
    public function search($table, $columns, $request)
    {
    	$rows = implode(', ',$columns);
    	$sql  = "SELECT {$rows} FROM {$table} WHERE ";

    	// Dynamically set binded and SQL
    	foreach ($columns as $column)
    	{
    		$this->bind[$column] = "%{$request['search']['value']}%";
    		$sql .= "({$column} LIKE :{$column}) OR ";
    	}

    	// Remove last 'OR'
    	$sql = substr($sql, 0, -3);

    	// Small break to get filtred count
    	$this->query($sql, $this->bind);
    	$this->total = $this->query->rowCount();

    	// Continue query
    	$order  = $columns[$request['order'][0]['column']];
    	$dir    = $request['order'][0]['dir'];
    	$start  = $request['start'];
    	$length = $request['length'];
    	$sql .= "ORDER BY $order $dir LIMIT $start, $length";
    	return $this->query($sql, $this->bind);
    }
}
