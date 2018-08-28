<?php

namespace Gini\ORM;

use \Gini\ORM\Building;

class Project extends Object
{
    public $identity    = 'string:100';
    public $number      = 'string:50';
    public $title       = 'string:100';
    public $owner       = 'object:user';
    public $type        = 'int:1,default:0';
    public $source_description = 'string:250';
    public $source_from = 'string:50';
    public $bank_from   = 'string:50';
    public $ctime       = 'datetime';
    public $building    = 'object:building';
    // 派件员
    public $dispatcher  = 'object:user';
    // 估价员
    public $assessor    = 'object:user';
    // 查勘员
    public $surveyor    = 'object:user';
    // 归档时间
    public $archive_time = 'datetime';

    protected static $db_index = [
        'unique:number',
        'identity', 'title', 'ctime', 'owner', 'type', 'building', 'approval', 'archive_time'
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

    public function archived()
    {
        return $this->archive_time != '0000-00-00 00:00:00';
    }

    public function getTemplateData()
    {
        $building = $this->building;
        $data = [
            '%委托人姓名%' => $this->user_name,
            '%委托人地址%' => $this->user_address,
            '%估价目的%' => $this->target,
            '%坐落%' => $building->address,
            '%建造年代%' => $building->year,
            '%物业管理%' => $building->property ? '有' : '无',
            '%坐落%' => $building->address,
            '%结构%' => Building::$structure_s[$building->structure],
            '%总层数%' => $building->total_floor,
            '%所在层数%' => $building->floor,
            '%朝向%' => Building::$front_s[$building->front],
            '%用途%' => Building::$use_s[$building->use],
            '%建筑面积' => $building->area,
            '%户型%' => Building::$type_s[$building->type],
            '%层高%' => $building->height,
            '%是否有抵押%' => $building->mortgage ? '有' : '无',
            '%附属物%' => Building::$appurtenance_s[$building->appurtenance],
            '%外墙%' => Building::$out_wall_s[$building->out_wall],
            '%装修部分厅门外%' => Building::$door_outside_s[$building->parlour_door_outside],
            '%装修部分厅门内%' => Building::$door_inside_s[$building->parlour_door_inside],
            '%装修部分厅窗外%' => Building::$windows_outside_s[$building->parlour_window_outside],
            '%装修部分厅窗内%' => Building::$windows_inside_s[$building->parlour_window_inside],
            '%装修部分厅墙面%' => Building::$parlour_wall_s[$building->parlour_wall],
            '%装修部分厅顶棚%' => Building::$parlour_platfond_s[$building->parlour_platfond],
            '%装修部分厅地面%' => Building::$parlour_floor_s[$building->parlour_floor],
            '%装修部分卧室墙面%' => Building::$parlour_wall_s[$building->room_wall],
            '%装修部分卧室顶棚%' => Building::$parlour_platfond_s[$building->room_platfond],
            '%装修部分卧室地面%' => Building::$parlour_floor_s[$building->room_floor],
            '%装修部分卫生间墙面%' => Building::$toilet_wall_s[$building->toilet_wall],
            '%装修部分卫生间顶棚%' => Building::$toilet_platfond_s[$building->toilet_platfond],
            '%装修部分卫生间地面%' => Building::$toilet_floor_s[$building->toilet_floor],
            '%装修部分卫生间设备%' => Building::$toilet_appurtenance_s[$building->toilet_appurtenance],
            '%装修部分厨房墙面%' => Building::$toilet_wall_s[$building->cook_wall],
            '%装修部分厨房顶棚%' => Building::$toilet_platfond_s[$building->cook_platfond],
            '%装修部分厨房地面%' => Building::$toilet_floor_s[$building->cook_floor],
            '%装修部分厨房设备%' => Building::$cook_appurtenance_s[$building->cook_appurtenance],
            '%装修部分阳台墙面%' => Building::$veranda_wall_s[$building->veranda_wall],
            '%装修部分阳台地面%' => Building::$veranda_floor_s[$building->veranda_floor],
            '%设备设施照明%' => $building->lighting ? '有' : '无',
            '%设备设施一户一表%' => $building->ammeter ? '有' : '无',
            '%设备设施烟感报警系统%' => $building->smoke_detector ? '有' : '无',
            '%设备设施上下水%' => $building->up_down_water ? '有' : '无',
            '%设备设施有线电视%' => $building->line_tv ? '有' : '无',
            '%设备设施自动喷淋系统%' => $building->spray_system ? '有' : '无',
            '%设备设施暖气%' => $building->heating ? '有' : '无',
            '%设备设施电话预留%' => $building->telephone_reservation ? '有' : '无',
            '%设备设施中水%' => $building->center_water ? '有' : '无',
            '%设备设施煤气%' => $building->gas ? '有' : '无',
            '%设备设施电梯%' => $building->lift ? '有' : '无',
            '%设备设施热水%' => $building->hot_water ? '有' : '无',
            '%设备设施对讲系统%' => $building->talk_back ? '有' : '无',
            '%利用状况' => $building->use_status == 3 ? '出租' : ($building->use_status == 2 ? '空置' : '自用'),
            '%成新%' => $building->percent_new,
            '%四至东%' => $building->east_front,
            '%四至南%' => $building->south_front,
            '%四至西%' => $building->west_front,
            '%四至北%' => $building->north_front,
            '%周围配套情况%' => $building->around_appurtenance,
            '%其他%' => $building->another_desc,
            '%结果1估价对象总价%' => $building->amount,
            '%结果1估价对象总价金额大写%' => $building->upper_amount,
            '%结果1估价对象单价%' => $building->unit,
            '%结果1估价对象单价金额大写%' => $building->upper_unit,
            '%结果2估价对象总额%' => $building->yxsck_amount,
            '%结果2估价对象总额金额大写%' => $building->upper_yxsck_amount,
            '%结果2.1估价对象总额%' => $building->dyzq_amount,
            '%结果2.1估价对象总额金额大写%' => $building->upper_dyzq_amount,
            '%结果2.2估价对象总额%' => $building->tqk_amount,
            '%结果2.2估价对象总额金额大写%' => $building->upper_tqk_amount,
            '%结果2.3估价对象总额%' => $building->fdyxsck_amount,
            '%结果2.3估价对象总额金额大写%' => $building->upper_fdyxsck_amount,
            '%结果3估价对象总价%' => $building->dyjz_amount,
            '%结果3估价对象总价金额大写%' => $building->upper_dyjz_amount,
            '%结果3估价对象单价%' => $building->dyjz_unit,
            '%结果3估价对象单价金额大写%' => $building->upper_dyjz_unit,
            '%实地查勘日期%' => $this->operation_date,
            '%估价作业期' => $this->operation_from . ' ~ ' . $this->operation_to,
            '%业务来源%' => $this->source_from,
            '%贷款银行%' => $this->bank_from,
            '%估价结果视图%' => V('projects/template/view/result', ['project' => $this ]),
            '%注册估价师视图%' => V('projects/template/view/register', ['project' => $this ]),
            '%不动产权证书视图%' => V('projects/template/view/ownership', ['project' => $this ])
        ];
        return $data;
    }
}
