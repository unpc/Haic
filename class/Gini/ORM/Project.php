<?php

namespace Gini\ORM;

class Project extends Object
{
    public $identity    = 'string:100';
    public $number      = 'string:50';
    public $title       = 'string:100';
    public $owner       = 'object:user';
    public $type        = 'int:1,default:0';
    public $source_description = 'string:250';
    public $ctime       = 'datetime';
    public $building    = 'object:building';
    // 派件员
    public $dispatcher  = 'object:user';
    // 估价员
    public $assessor    = 'object:user';
    // 查勘员
    public $surveyor    = 'object:user';

    protected static $db_index = [
        'unique:number',
        'identity', 'title', 'ctime', 'owner', 'type', 'building', 'approval'
    ];

    const PUBLIC_BUSINESS = 2;
    const PRIVATE_BUSINESS = 1;

    public static $TYPES = [
        self::PRIVATE_BUSINESS => '个贷业务',
        self::PUBLIC_BUSINESS => '对公业务'
    ];

    public function delete()
    {
        those('log')->whose('project')->is($this)->deleteAll();
        parent::delete();
    }

    public function filePath($path='')
    {
        $basePath = APP_PATH.'/'.DATA_DIR.'/project/'.($this->id?:0).'/';
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
                    'url' => "ajax/project/deleteAttachment/{$this->id}/{$file->getFileName()}", 
                    'key' => $file->getFileName()
                ];
            }
        }
        return $config;
    }
}