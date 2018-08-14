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
}
