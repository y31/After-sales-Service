<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2008 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.6.3, 2008-08-25
 */


/** PHPExcel */
require_once dirname(__FILE__).'/../PHPExcel.php';

/** PHPExcel_IWriter */
require_once dirname(__FILE__).'/../PHPExcel/Writer/IWriter.php';

/** PHPExcel_IReader */
require_once dirname(__FILE__).'/../PHPExcel/Reader/IReader.php';
require_once dirname(__FILE__).'/../PHPExcel/Writer/Excel5.php';
require_once dirname(__FILE__).'/../PHPExcel/Reader/Excel5.php';
//require_once dirname(__FILE__).'/../PHPExcel/Writer/PDF.php';


/**
 * PHPExcel_IOFactory
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_IOFactory
{	
	/**
	 * Search locations
	 *
	 * @var array
	 */
	private static $_searchLocations = array(
		array( 'type' => 'IWriter', 'path' => 'PHPExcel/Writer/{0}.php', 'class' => 'PHPExcel_Writer_{0}' ),
		array( 'type' => 'IReader', 'path' => 'PHPExcel/Reader/{0}.php', 'class' => 'PHPExcel_Reader_{0}' )
	);
	
    /**
     * Private constructor for PHPExcel_IOFactory
     */
    private function __construct() { }
    
    /**
     * Get search locations
     *
     * @return array
     */
	public static function getSearchLocations() {
		return self::$_searchLocations;
	}
	
	/**
	 * Set search locations
	 * 
	 * @param array $value
	 * @throws Exception
	 */
	public static function setSearchLocations($value) {
		if (is_array($value)) {
			self::$_searchLocations = $value;
		} else {
			throw new Exception('Invalid parameter passed.');
		}
	}
	
	/**
	 * Add search location
	 * 
	 * @param string $type			Example: IWriter
	 * @param string $location		Example: PHPExcel/Writer/{0}.php
	 * @param string $classname 	Example: PHPExcel_Writer_{0}
	 */
	public static function addSearchLocation($type = '', $location = '', $classname = '') {
		self::$_searchLocations[] = array( 'type' => $type, 'path' => $location, 'class' => $classname );
	}
	
	/**
	 * Create PHPExcel_Writer_IWriter
	 *
	 * @param PHPExcel $phpExcel
	 * @param string  $writerType	Example: Excel2007
	 * @return PHPExcel_Writer_IWriter
	 */
	public static function createWriter(PHPExcel $phpExcel, $writerType = '') {
		// Search type
		$searchType = 'IWriter';
		
		// Include class
		foreach (self::$_searchLocations as $searchLocation) {
			if ($searchLocation['type'] == $searchType) {
				$className = str_replace('{0}', $writerType, $searchLocation['class']);
				$classFile = str_replace('{0}', $writerType, $searchLocation['path']);
				/*if (!class_exists($className)) {
					self::requireFile($classFile);
				}
				*/
				if (!function_exists($className)) {
					self::requireFile($classFile);
				}
				$instance = new $className($phpExcel);
				
				if (!is_null($instance)) {
					return $instance;
				}
			}
		}
		
		// Nothing found...
		throw new Exception("No $searchType found for type $writerType");
	}
	
	/**
	 * Create PHPExcel_Reader_IReader
	 *
	 * @param string $readerType	Example: Excel2007
	 * @return PHPExcel_Reader_IReader
	 */
	public static function createReader($readerType = '') {
		// Search type
		$searchType = 'IReader';
		
		// Include class
		foreach (self::$_searchLocations as $searchLocation) {
			if ($searchLocation['type'] == $searchType) {
				$className = str_replace('{0}', $readerType, $searchLocation['class']);
				$classFile = str_replace('{0}', $readerType, $searchLocation['path']);
				
				if (!class_exists($className)) {
					self::requireFile($classFile);
				}
				
				$instance = new $className();
				if (!is_null($instance)) {
					return $instance;
				}
			}
		}
		
		// Nothing found...
		throw new Exception("No $searchType found for type $readerType");
	}
	
	/**
	 * Require_once file
	 *
	 * @param string $filename
	 */
	private static function requireFile($filename) {
		$includePath = get_include_path();
		$includeTokens = explode(PATH_SEPARATOR, $includePath);
					
		foreach ($includeTokens as $includeToken) {
			if (file_exists($includeToken . '/' . $filename)) {
				require_once( $filename );
			}
		}
		
		/*
		* 说明：重新改写 包含的引入文件 不通过get_include_path 函数获得  因为 没有通过set_include_path 设置
		* 参数：
		* 作者：John
		* 时间：Tue Apr 07 10:48:59 CST 2009
		*/
		/*$count=preg_match_all('/\//i',$filename,$testarr);
		$separator='';
		for($i=1;$i<=$count;$i++)
		{
			if($separator=='')
			{
				$separator='/../';
			}
			else 
			{
				$separator=$separator.'../';
			}
		}
		$file=dirname(__FILE__).$separator.$filename;
		if(file_exists($file)){
			require_once( $file );
		}
		*/
	}
}
