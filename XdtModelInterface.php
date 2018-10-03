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

interface XdtModelInterface
{
	public function count($table);
	public function get($table, array $columns = [], array $request = []);
	public function search($table, array $columns = [], array $request = []);
}
