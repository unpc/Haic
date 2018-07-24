<?php

namespace Gini\Model;

class Alert
{
    //  成功
    const TYPE_OK = 'alert-success';
    // 失败
    const TYPE_ERROR = 'alert-danger';

    public static function setMessage($message, $type) {
        if (!in_array($type, [self::TYPE_OK, self::TYPE_ERROR])) {
            $type = self::TYPE_OK;
        }
        $messages = (array)$_SESSION['Alert.Message'];

        $_PUSHMESSAGE = function($type, $message) use($messages) {
            !is_array($messages[$type]) and $messages[$type] = [];
            $messages[$type][] = $message;
            $_SESSION['Alert.Message'] = $messages;
        };

        $type == self::TYPE_OK
            and !$messages[self::TYPE_ERROR]
            and $_PUSHMESSAGE($type, $message);

        $type == self::TYPE_ERROR
            and !$messages[self::TYPE_OK]
            and $_PUSHMESSAGE($type, $message);
    }

    public static function getMessage() {
        if ($_SESSION['Alert.Message']) {
            $messages = $_SESSION['Alert.Message'];
            $_SESSION['Alert.Message'] = [];
            $key = $messages[self::TYPE_ERROR] ? self::TYPE_ERROR : self::TYPE_OK;
            $message = join('<br/>', $messages[$key]);
            return V('components/message', [
                    'type' => $key,
                    'value' => $message,
                ]);
        }
        else {
            return '';
        }
    }
}