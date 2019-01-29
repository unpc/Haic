<?php

namespace Gini\Model;

use \Gini\Model\Widget;

class Help
{
    public static function pagination(& $objects, $start, $per_page, $url=null, $params = [])
    {
        unset($params['st']);

        $totalCount = $objects->totalCount();

        $start = $start - ($start % $per_page);

        if ($start > 0) {
            $last = floor($totalCount / $per_page) * $per_page;
            if ($last == $totalCount) {
                $last = max(0, $last - $per_page);
            }
            if ($start > $last) {
                $start = $last;
            }
        }

        $objects = $objects->limit($start, $per_page);

        $pagination = Widget::factory('pagination', [
                'start' => $start,
                'per_page' => $per_page,
                'total' => $totalCount,
                'url' => $url,
                'params' => $params
            ]);

        return $pagination;
    }

    public static function links($links)
    {
        return Widget::factory('links', ['items' => $links]);
    }

    public static function jsQuote($str, $quote='"')
    {
        if (is_scalar($str)) {
            if (is_numeric($str)) {
                return $str;
            } elseif (is_bool($str)) {
                return $str ? true : false;
            } elseif (is_null($str)) {
                return 'null';
            } else {
                return $quote.self::escape($str).$quote;
            }
        } else {
            return @json_encode($str);
        }
    }

    public static function escape($str)
    {
        return addcslashes($str, "\\\'\"&\n\r<>");
    }

    public static function mergeArrayText($arr1=[], $arr2=[])
    {
        $new = [];
        foreach ($arr1 as $key => $value) {
            error_log("value:{$value}");
            if ($arr2[$value]) {
                $new[] = $arr2[$value];
            }
        }
        return join(', ', $new);
    }

    public static function toDateChinese($time)
    {
        if (!$time) $time = time();
        $date = date('Y-m-d', $time);
        $date_arr = explode('-', $date);
        $arr = [];
        foreach ($date_arr as $index => &$val) {
            if (mb_strlen($val) == 4) {
                $arr[] = preg_split('/(?<!^)(?!$)/u', $val);
            } else {
                if ($val > 10) {
                    $v[] = 10;
                    $v[] = $val % 10;
                    $arr[] = $v;
                    unset($v);
                } else {
                    $arr[][] = $val;
                }
            }
        }
        $cn = array("一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "零");
        $num = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "0");
        $str_time = '';
        for ($i = 0; $i < count($arr); $i++) {
            foreach ($arr[$i] as $index => $item) {
                $str_time .= $cn[array_search($item, $num)];
            }
            if ($i == 0) {
                $str_time .= '年';
            } elseif ($i == 1) {
                $str_time .= '月';
            } elseif ($i == 2) {
                $str_time .= '日';
            }
        }
        return $str_time;
    }

    public static function chinanum($num) {
        $china = array('零','一','二','三','四','五','六','七','八','九');
        $arr = str_split($num);
        for($i = 0; $i < count($arr); $i++){
            return $china[$arr[$i]];
        }
    }
}
