<?php

namespace Gini\ORM;

use \Gini\ORM\Building;
use \Gini\Model\Help;

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
    // 报告模板
    public $template    = 'object:template';
    // 预评模板
    public $preeval     = 'object:template';
    // 预评项目
    public $isPreparation = 'int:1,default:0';

    protected static $db_index = [
        'number', 'source_from', 'bank_from',
        'identity', 'title', 'ctime', 'owner', 'type', 'building', 'archive_time', 'isPreparation'
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
        $approval = a('approval', ['project' => $this]);
        $project = $this;
        $true_group = (array)Hub('template.group');
        $group = (array)\Gini\Config::get('project.group');
        $calculat_table = (array)$project->calculat_table;
        $income_table = (array)$project->income_table;
        $ownership = (array)current((array)$building->ownership);
        $data = [
            '#001' => strtr(\Gini\Config::get('project.number_template'), [
                '%year' => date('Y', strtotime($project->ctime)),
                '%number' => $project->number
            ]),
            '#002' => $project->title,
            '#003' => $project->source_description,
            '#004' => $project->user_name,
            '#005' => $project->user_address,
            '#006' => $project->target,
            '#007' => $building->year,
            '#008' => $building->property ? '有' : '无',
            '#009' => $building->address,
            '#010' => $building->structure_text ?: Building::$structure_s[$building->structure],
            '#011' => $building->total_floor,
            '#012' => $building->floor,
            '#013' => Building::$front_s[$building->front],
            '#014' => $building->use_text ?: Building::$use_s[$building->use],
            '#015' => $building->area,
            '#016' => $building->type_text ?: ($building->type_door ? 
                "{$building->type_stairs}梯{$building->type_door}户"
                :
                Building::$type_s[$building->type]
            ),
            '#017' => $building->height,
            '#018' => $building->mortgage ? '有' : '无',
            '#019' => Help::mergeArrayText((array)$building->appurtenance, Building::$appurtenance_s),
            '#020' => Help::mergeArrayText((array)$building->out_wall, Building::$out_wall_s),
            '#021' => $building->parlour_door_outside_text ?: Building::$door_outside_s[$building->parlour_door_outside],
            '#022' => $building->parlour_door_inside_text ?: Building::$door_inside_s[$building->parlour_door_inside],
            '#023' => $building->parlour_window_outside_text ?: Building::$window_outside_s[$building->parlour_window_outside],
            '#024' => $building->parlour_window_inside_text ?: Building::$window_inside_s[$building->parlour_window_inside],
            '#025' => $building->parlour_wall_text ?: Building::$parlour_wall_s[$building->parlour_wall],
            '#026' => $building->parlour_platfond_text ?: Building::$parlour_platfond_s[$building->parlour_platfond],
            '#027' => $building->parlour_floor_text ?: Building::$parlour_floor_s[$building->parlour_floor],
            '#028' => $building->room_wall_text ?: Building::$parlour_wall_s[$building->room_wall],
            '#029' => $building->room_platfond_text ?: Building::$parlour_platfond_s[$building->room_platfond],
            '#030' => $building->room_floor_text ?: Building::$parlour_floor_s[$building->room_floor],
            '#031' => $building->toilet_wall_text ?: Building::$toilet_wall_s[$building->toilet_wall],
            '#032' => $building->toilet_platfond_text ?: Building::$toilet_platfond_s[$building->toilet_platfond],
            '#033' => $building->toilet_floor_text ?: Building::$toilet_floor_s[$building->toilet_floor],
            '#034' => Help::mergeArrayText((array)$building->toilet_appurtenance, Building::$toilet_appurtenance_s),
            '#035' => $building->cook_wall_text ?: Building::$toilet_wall_s[$building->cook_wall],
            '#036' => $building->cook_platfond_text ?: Building::$toilet_platfond_s[$building->cook_platfond],
            '#037' => $building->cook_floor_text ?: Building::$toilet_floor_s[$building->cook_floor],
            '#038' => Help::mergeArrayText((array)$building->cook_appurtenance, Building::$cook_appurtenance_s),
            '#039' => $building->veranda_wall_text ?: Building::$veranda_wall_s[$building->veranda_wall],
            '#040' => $building->veranda_floor_text ?: Building::$veranda_floor_s[$building->veranda_floor],
            '#041' => $building->lighting ? '有' : '无',
            '#042' => $building->ammeter ? '有' : '无',
            '#043' => $building->smoke_detector ? '有' : '无',
            '#044' => $building->up_down_water ? '有' : '无',
            '#045' => $building->line_tv ? '有' : '无',
            '#046' => $building->spray_system ? '有' : '无',
            '#047' => $building->heating ? '有' : '无',
            '#048' => $building->telephone_reservation ? '有' : '无',
            '#049' => $building->center_water ? '有' : '无',
            '#050' => $building->gas ? '有' : '无',
            '#051' => $building->lift ? '有' : '无',
            '#052' => $building->hot_water ? '有' : '无',
            '#053' => $building->talk_back ? '有' : '无',
            '#054' => $building->use_status == 3 ? '出租' : ($building->use_status == 2 ? '空置' : '自用'),
            '#055' => $building->percent_new,
            '#056' => $building->east_front,
            '#057' => $building->south_front,
            '#058' => $building->west_front,
            '#059' => $building->north_front,
            '#060' => $building->around_appurtenance,
            '#061' => $building->another_desc,
            '#062' => $building->plot,
            '#063' => (string)V('projects/template/result', ['project' => $project]),
            '#064' => $building->amount,
            '#065' => $building->upper_amount,
            '#066' => $building->unit,
            '#067' => $building->upper_unit,
            '#068' => $building->yxsck_amount,
            '#069' => $building->upper_yxsck_amount,
            '#070' => $building->dyzq_amount,
            '#071' => $building->upper_dyzq_amount,
            '#072' => $building->tqk_amount,
            '#073' => $building->upper_tqk_amount,
            '#074' => $building->fdyxsck_amount,
            '#075' => $building->upper_fdyxsck_amount,
            '#076' => $building->dyjz_amount,
            '#077' => $building->upper_dyjz_amount,
            '#078' => $building->dyjz_unit,
            '#079' => $building->upper_dyjz_unit,
            '#080' => (string)V('projects/template/register', ['project' => $project]),
            '#081' => $project->operation_date,
            '#082' => $project->operation_from . ' ~ ' . $project->operation_to,
            '#083' => '表1-比较因素条件说明表',
            '#084' => '表2-比较因素条件指数表',
            '#085' => '表3-比较因素条件修正指数表',
            '#086' => $project->source_from,
            '#087' => $project->bank_from,
            '#088' => $approval->info['user_name'] ?: $project->user_name,
            '#089' => $approval->info['report_no'] ?: $project->report_no,
            '#090' => $approval->info['owner'] ?: $building->ownership['owner'],
            '#091' => $approval->info['address'] ?: $building->address,
            '#092' => $approval->info['contact'] ?: $project->user_name,
            '#093' => $approval->info['contact_phone'] ?: $project->user_phone,
            '#094' => $approval->info['case_name'] ?: $project->case_name,
            '#095' => date('Y-m-d', $project->stment_date ? strtotime($project->stment_date) : time()),
            '#096' => date('Y-m-d', $project->explor_date ? strtotime($project->explor_date) : time()),
            '#097' => $approval->info['explor_user'] ?: $building->explor_user,
            '#098' => $approval->info['area'] ?: $building->area,
            '#099' => $approval->info['acreage'] ?: $building->acreage,
            '#0100' => $approval->info['target'] ?: $project->target,
            "#0101" => $building->year ? Date('Y') - $building->year : 0,
            "#0102" => $calculat_table['business_desc'],
            '#0103' => $calculat_table['business_jttdd'],
            '#0104' => $calculat_table['business_syfhd'],
            '#0105' => $calculat_table['business_hjtj'],
            '#0106' => $calculat_table['business_jcsswbd'],
            '#0107' => $calculat_table['business_ggsswbd'],
            '#0108' => $calculat_table['gbys_ggbf'],
            '#0109' => $calculat_table['gbys_nbzx'],
            '#0110' => $calculat_table['gbys_fssszk'],
            '#0111' => $true_group['name'],
            '#0112' => $true_group['owner'],
            '#0113' => $true_group['address'],
            '#0114' => $true_group['code'],
            '#0115' => $true_group['level'],
            '#0116' => $true_group['number'],
            '#0117' => $true_group['time_limit'],
            '#0120' => $ownership['number'],
            '#0121' => '产权证视图',
            "#0122" => "租赁实例说明表视图",
            "#0123" => "估价对象与可比实例对比分析表视图",
            "#0124" => $calculat_table['pgdj'],
            "#0125" => $income_table['project_total_compensation_1'],
            "#0126" => $income_table['project_total_compensation_2'],
            "#0127" => $income_table['project_total_compensation_3'],
            "#0128" => $income_table['project_unit_1'],
            "#0129" => $income_table['project_unit_2'],
            "#0130" => $income_table['project_unit_3'],
            "#0131" => $income_table['project_unit'],
            "#0132" => $income_table['project_adjust_unit_1'],
            "#0133" => $income_table['project_adjust_unit_2'],
            "#0134" => $income_table['project_adjust_unit_3'],
            "#0135" => $income_table['project_income_yxczmj'] . "%",
            "#0136" => $income_table['project_income_nqzmsr'],
            "#0137" => $income_table['project_income_yhckll'] . "%",
            "#0138" => $income_table['project_income_qtsr'],
            "#0139" => $income_table['project_income_nyxmsr'],
            "#0140" => $income_table['project_income_glf'],
            "#0141" => $income_table['project_income_jzwczcb'],
            "#0142" => $income_table['project_income_wxf'],
            "#0143" => $income_table['project_income_bxf'],
            "#0144" => $income_table['project_income_sj'],
            "#0145" => $income_table['project_income_yxfy'],
            "#0146" => $income_table['project_income_jsy'],
            "#0147" => $income_table['project_income_tzyhl'] . "%",
            "#0148" => $income_table['project_income_bcl'] . "%",
            "#0149" => $income_table['project_income_gjdxcyq'],
            "#0150" => $income_table['project_income_cyqjfdcjz'],
            "#0151" => $income_table['project_income_fjzzl'] . "%",
            "#0152" => $income_table['project_income_zhdj'],
            "#0153" => $income_table['project_income_dj'],
            "#0154" => $income_table['project_income_zjz'],
            "#0155" => $project->operation_dur,
            "#0156" => $project->company_title,
            "#0157" => $project->company_address
        ];
        return $data;
    }
}
