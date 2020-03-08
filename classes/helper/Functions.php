<?php

namespace helper;

use core\DB;

class Functions {

	private static $_limit;
	private static $_page;
	private static $_total;
	private static $_query;

	public static function paginationPrepare($query) {
		$queryResult = DB::select($query);
		self::$_total = count($queryResult);
		self::$_query = $query;
	}

	public static function paginationResults($page = 1 , $limit = 25) {

		self::$_limit   = $limit;
		self::$_page    = $page;

		if ( $limit == 'all' ) {
			$endQuery      = self::$_query;
		} else {
			$endQuery      = self::$_query . " LIMIT " . ( ( $page - 1 ) * $limit ) . ", $limit";
		}

		$results             = DB::select($endQuery);

		$result['page']   = self::$_page;
		$result['limit']  = self::$_limit;
		$result['total']  = self::$_total;
		$result['data']   = $results;

		return $result;
	}

	public static function paginationCreateLinks( $list_class = "", $links = 2 ) {
		if ( self::$_limit == 'all' ) {
			return '';
		}

		$last       = ceil( self::$_total / self::$_limit );

		$start      = ( ( self::$_page - $links ) > 0 ) ? self::$_page - $links : 1;
		$end        = ( ( self::$_page + $links ) < $last ) ? self::$_page + $links : $last;

		$html       = '<ul class="' . $list_class . '">';

		$class      = ( self::$_page == 1 ) ? "disabled" : "";

		//if(self::$_page > $start) {
			$html   .= '<li class="'. $class .'"><a href="?page=1">First</a></li>';
		//}
		//else

		//$html       .= ( ( self::$_page - 1 ) > 0) ? '<li class="' . $class . '"><a href="?page=' . ( self::$_page - 1 ) . '">&laquo;</a></li>' : '';
		$html       .= '<li class="' . $class . '"><a href="?page=' . ( self::$_page - 1 ) . '">&laquo;</a></li>';


		for ( $i = $start ; $i <= $end; $i++ ) {
			$class  = ( self::$_page == $i ) ? "active" : "";
			$html   .= '<li class="' . $class . '"><a href="?page=' . $i . '">' . $i . '</a></li>';
		}


		$class      = ( self::$_page == $last ) ? "disabled" : "";


		$html       .= ( self::$_page != $last ) ? '<li class="' . $class . '"><a href="?page=' . ( self::$_page + 1 ) . '">&raquo;</a></li>' : '';

		if ( $end < $last ) {
			//	$html   .= '<li class="disabled"><span>...</span></li>';
			$html   .= '<li><a href="?page=' . $last . '">Last</a></li>';
		}

		$html       .= '</ul>';

		return $html;
	}

	public static function timeago($time) {
		$etime = time() - $time;

		if($etime < 1){
			return '0 seconds ago';
		}

		$a = array(
			12 * 30 * 24 * 60 * 60 - 1 => 'year',
			30 * 24 * 60 * 60 - 1 => 'month',
			24 * 60 * 60 - 1 => 'day',
			60 * 60 - 1 => 'hour',
			60 - 1 => 'minute',
			1 => 'second'
		);

		foreach($a as $secs => $str) {
			if($secs >= $etime){
				continue;
			}
			$d = $etime / $secs;
			if($d >= 1){
				$r = round($d);
				return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
			}
		}
		return 'some time ago';
	}

}