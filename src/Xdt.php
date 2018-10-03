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

class Xdt
{
	/**
	 * @access public
	 */
	public $request; // Request from AJAX
	public $table; // Database table
	public $columns = []; // Table columns
	public $total; // Count data without filter
	public $totalFilter; // Count filtred data
	public $data = []; // SQL data
	
	/**
	 * @access protected
	 */
	protected $model; // Database API

	/**
	 * @access private
	 */
	private $wrapper; // SQL data wrapper
	private $result = []; // datatable JSON result
	private $key = []; // table key

	/**
	 * Set Model and Catch request
	 *
	 * @param XdtModelInterface $model
	 * @return void
	 *
	 * Model is the database key for datatable
	 * require any Model Class API (MySQL, PostgreSQL.. )
	 * that implements XdtModelInterface
	 */
	public function __construct(XdtModelInterface $model)
	{
		$this->model = $model;
		$this->request = $_REQUEST;
	}

	/**
	 * Set table and columns
	 *
	 * @param string $table, array $columns
	 * @return void
	 */
	public function set($table, array $columns)
	{
		$this->table = $table;
		$this->columns = $columns;
		$this->total = $this->model->count($this->table);
	}

	/**
	 * Render datatable content
	 *
	 * @param void
	 * @return void
	 */
	public function render()
	{
		// Check if its simple request or search request
		if ( !$this->isSearch() )
		{
			$this->wrapper = $this->model->get( $this->table, $this->columns, $this->request);
			$this->totalFilter = $this->total;
		}
		else
		{
			$this->wrapper = $this->model->search( $this->table, $this->columns, $this->request);
			$this->totalFilter = $this->model->total;
		}
		
		// Prepare output
		foreach ($this->wrapper as $column)
		{
			// Check if action option required
			if ( $this->isAction() )
			{
				$action = str_replace('{KEY}', $column[$this->key], $this->request['action']['buttons']);
				$column[] = $action;
			}
			
			// Format data wrapper
			array_push( $this->data, array_values($column) );
		}

		// Combine data
		$this->result = [

			'draw'            => intval( $this->request['draw'] ),
			'recordsTotal'    => intval( $this->total ),
			'recordsFiltered' => intval( $this->totalFilter ),
			'data'            => $this->data
		];

		echo json_encode($this->result);
	}

	/**
	 * Define key fro action buttons
	 *
	 * @param string $key
	 * @return void
	 */
	public function setKey($key = '')
	{
		$this->key = $key;
	}

	/**
	 * Check if request is search
	 *
	 * @param void
	 * @return boolean
	 */
	private function isSearch()
	{
		if ( !empty($this->request['search']['value']) ) return true;
	}

	/**
	 * Check if action buttons requested
	 *
	 * @param void
	 * @return boolean
	 */
	private function isAction()
	{
		if ( $this->request['action']['buttons'] && $this->request['action']['buttons'] !== 'false' ) return true;
	}
}
