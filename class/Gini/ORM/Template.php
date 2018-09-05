<?php

namespace Gini\ORM;

class Template extends Object
{
    public $user	    = 'object:user';
    public $title 	    = 'string:100';
    public $ctime       = 'datetime';
    public $content     = 'string:*';
    public $type        = 'int:1,default:0';

    protected static $db_index = [
        'user', 'title', 'ctime', 'type'
    ];

    const REPORT_BUSINESS = 1;
    const READY_BUSINESS = 2;
    

    public static $TYPES = [
        self::REPORT_BUSINESS => '预评模板',
        self::READY_BUSINESS => '报告模板'
    ];
    
    public function save()
    {
        if ($this->ctime == '0000-00-00 00:00:00' || !$this->ctime) {
            $this->ctime = date('Y-m-d H:i:s');
        }
        return parent::save();
    }

    public function filePath($path='')
    {
        $basePath = APP_PATH.'/'.DATA_DIR.'/template/'.($this->id?:0).'/';
        \Gini\File::ensureDir($basePath);
        return $basePath . $path;
    }

    public function attachments()
    {
        $attachments = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->filePath()), \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            if (!preg_match("/^\./", $file->getFileName())) {
                $attachments[] = $file->getFileName();
            }
        }
        return $attachments;
    }

    public function attachmentsConfig()
    {
        $config = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->filePath()), \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            if (!preg_match("/^\./", $file->getFileName())) {
                $config[] = [
                    'type' => $file->getExtension(),
                    'size' => $file->getSize(),
                    'caption' => $file->getFileName(),
                    'url' => "ajax/template/deleteAttachment/{$this->id}/{$file->getFileName()}",
                    'key' => $file->getFileName()
                ];
            }
        }
        return $config;
    }
}
