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

    public function getTableListData() {
        $building = $this->building;
        $approval = a('approval', ['project' => $this]);
        $project = $this;
        $true_group = (array)Hub('template.group');
        $group = (array)\Gini\Config::get('project.group');
        $calculat_table = (array)$project->calculat_table;
        $income_table = (array)$project->income_table;
        $ownership = (array)current((array)$building->ownership);

        $data = [];

        $data['DES'] = [
            'source' => [$income_table['project_source_1'], $income_table['project_source_2'], $income_table['project_source_3']],
            'title' => [$income_table['project_title_1'], $income_table['project_title_2'], $income_table['project_title_3']],
            'address' => [$income_table['project_address_1'], $income_table['project_address_2'], $income_table['project_address_3']],
            'rent' => [$income_table['project_rent']],
            'jzmj' => [$income_table['project_jzmj_1'], $income_table['project_jzmj_2'], $income_table['project_jzmj_3']],
            'yt' => [$income_table['project_yt_1'], $income_table['project_yt_2'], $income_table['project_yt_3']],
            'precent' => [$income_table['project_precent_1'], $income_table['project_precent_2'], $income_table['project_precent_3']],
            'lcdc' => [$income_table['project_lcdc_1'], $income_table['project_lcdc_2'], $income_table['project_lcdc_3']],
            'nbzx' => [$income_table['project_nbzx_1'], $income_table['project_nbzx_2'], $income_table['project_nbzx_3']],
            'cjrq' => [$income_table['project_cjrq_1'], $income_table['project_cjrq_2'], $income_table['project_cjrq_3']],
            'unit' => [$income_table['project_unit_1'], $income_table['project_unit_2'], $income_table['project_unit_3']]
        ];

        $data['DOB'] = [
            'title' => [$income_table['project_title'], $income_table['project_title_1'], $income_table['project_title_2'], $income_table['project_title_3']],
            'address' => [$income_table['project_address'], $income_table['project_address_1'], $income_table['project_address_2'], $income_table['project_address_3']],
            'address_com' => [$income_table['project_title_compensation_1'], $income_table['project_title_compensation_2'], $income_table['project_title_compensation_3']],
            'jttdd' => [$income_table['project_jttdd'], $income_table['project_jttdd_1'], $income_table['project_jttdd_2'], $income_table['project_jttdd_3']],
            'jttdd_com' => [$income_table['project_jttdd_compensation_1'], $income_table['project_jttdd_compensation_2'], $income_table['project_jttdd_compensation_3']],
            'syfhd' => [$income_table['project_syfhd'], $income_table['project_syfhd_1'], $income_table['project_syfhd_2'], $income_table['project_syfhd_3']],
            'syfhd_com' => [$income_table['project_syfhd_compensation_1'], $income_table['project_syfhd_compensation_2'], $income_table['project_syfhd_compensation_3']],
            'hj' => [$income_table['project_hj'], $income_table['project_hj_1'], $income_table['project_hj_2'], $income_table['project_hj_3']],
            'hj_com' => [$income_table['project_hj_compensation_1'], $income_table['project_hj_compensation_2'], $income_table['project_hj_compensation_3']],
            'jg' => [$income_table['project_jg'], $income_table['project_jg_1'], $income_table['project_jg_2'], $income_table['project_jg_3']],
            'jg_com' => [$income_table['project_jg_compensation_1'], $income_table['project_jg_compensation_2'], $income_table['project_jg_compensation_3']],
            'lcdc' => [$income_table['project_lcdc'], $income_table['project_lcdc_1'], $income_table['project_lcdc_2'], $income_table['project_lcdc_3']],
            'lcdc_com' => [$income_table['project_lcdc_compensation_1'], $income_table['project_lcdc_compensation_2'], $income_table['project_lcdc_compensation_3']],
            'ggptss' => [$income_table['project_ggptss'], $income_table['project_ggptss_1'], $income_table['project_ggptss_2'], $income_table['project_ggptss_3']],
            'ggptss_com' => [$income_table['project_ggptss_compensation_1'], $income_table['project_ggptss_compensation_2'], $income_table['project_ggptss_compensation_3']],
            'front' => [$income_table['project_front'], $income_table['project_front_1'], $income_table['project_front_2'], $income_table['project_front_3']],
            'front_com' => [$income_table['project_front_compensation_1'], $income_table['project_front_compensation_2'], $income_table['project_front_compensation_3']],
            'csghxz' => [$income_table['project_csghxz'], $income_table['project_csghxz_1'], $income_table['project_csghxz_2'], $income_table['project_csghxz_3']],
            'csghxz_com' => [$income_table['project_csghxz_compensation_1'], $income_table['project_csghxz_compensation_2'], $income_table['project_csghxz_compensation_3']],
            'area_com' => [$income_table['project_area_total_compensation_1'], $income_table['project_area_total_compensation_2'], $income_table['project_area_total_compensation_3']],
            'market' => [$income_table['project_market'], $income_table['project_market_1'], $income_table['project_market_2'], $income_table['project_market_3']],
            'market_com' => [$income_table['project_market_compensation_1'], $income_table['project_market_compensation_2'], $income_table['project_market_compensation_3']],
            'market_t_com' => [$income_table['project_market_total_compensation_1'], $income_table['project_market_total_compensation_2'], $income_table['project_market_total_compensation_3']],
            'tran' => [$income_table['project_transaction'], $income_table['project_transaction_1'], $income_table['project_transaction_2'], $income_table['project_transaction_3']],
            'tran_com' => [$income_table['project_transaction_compensation_1'], $income_table['project_transaction_compensation_2'], $income_table['project_transaction_compensation_3']],
            'tran_t_com' => [$income_table['project_transaction_total_compensation_1'], $income_table['project_transaction_total_compensation_2'], $income_table['project_transaction_total_compensation_3']],
            'struct' => [$income_table['project_structure'], $income_table['project_structure_1'], $income_table['project_structure_2'], $income_table['project_structure_3']],
            'struct_com' => [$income_table['project_structure_compensation_1'], $income_table['project_structure_compensation_2'], $income_table['project_structure_compensation_3']],
            'precent' => [$income_table['project_precent'], $income_table['project_precent_1'], $income_table['project_precent_2'], $income_table['project_precent_3']],
            'precent_com' => [$income_table['project_precent_compensation_1'], $income_table['project_precent_compensation_2'], $income_table['project_precent_compensation_3']],
            'nbzx' => [$income_table['project_nbzx'], $income_table['project_nbzx_1'], $income_table['project_nbzx_2'], $income_table['project_nbzx_3']],
            'nbzx_com' => [$income_table['project_nbzx_compensation_1'], $income_table['project_nbzx_compensation_2'], $income_table['project_nbzx_compensation_3']],
            'shsb' => [$income_table['project_shsb'], $income_table['project_shsb_1'], $income_table['project_shsb_2'], $income_table['project_shsb_3']],
            'shsb_com' => [$income_table['project_shsb_compensation_1'], $income_table['project_shsb_compensation_2'], $income_table['project_shsb_compensation_3']],
            'qycb' => [$income_table['project_qycb'], $income_table['project_qycb_1'], $income_table['project_qycb_2'], $income_table['project_qycb_3']],
            'qycb_com' => [$income_table['project_qycb_compensation_1'], $income_table['project_qycb_compensation_2'], $income_table['project_qycb_compensation_3']],
            'jzmj' => [$income_table['project_jzmj'], $income_table['project_jzmj_1'], $income_table['project_jzmj_2'], $income_table['project_jzmj_3']],
            'jzmj_com' => [$income_table['project_jzmj_compensation_1'], $income_table['project_jzmj_compensation_2'], $income_table['project_jzmj_compensation_3']],
            'gbys' => [$income_table['project_gbys_total_compensation_1'], $income_table['project_gbys_total_compensation_2'], $income_table['project_gbys_total_compensation_3']],
            'total' => [$income_table['project_total_compensation_1'], $income_table['project_total_compensation_2'], $income_table['project_total_compensation_3']],
            'unit' => [$income_table['project_unit'], $income_table['project_unit_1'], $income_table['project_unit_2'], $income_table['project_unit_3']],
            'ad_unit' => [$income_table['project_adjust_unit_1'], $income_table['project_adjust_unit_1'], $income_table['project_adjust_unit_1']],
        ];

        $data['OWNERSHIP'] = $this->getOwnershipData();

        return $data;
    }

    public function getTableData() {

        $data = [];

        $r = [];
        $registers = (array)$this->registers;
        foreach ($registers as $register) {
            $r[] = ['name' => $register['name'], 'number' => $register['number'], 'date' => date('Y年m月d日')];
        }

        $data['register.name'] = $r;

        return $data;
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
            '#081' => Help::toDateChinese(strtotime($project->operation_date)),
            '#082' => Help::toDateChinese(strtotime($project->operation_from)) . ' 至 ' . Help::toDateChinese(strtotime($project->operation_to)),
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
            '#0111' => $true_group['name'] ?: $group['name'],
            '#0112' => $true_group['owner'] ?: $group['owner'],
            '#0113' => $true_group['address'] ?: $group['address'],
            '#0114' => $true_group['code'] ?: $group['code'],
            '#0115' => $true_group['level'] ?: $group['level'],
            '#0116' => $true_group['number'] ?: $group['number'],
            '#0117' => $true_group['time_limit'] ?: $group['time_limit'],
            '#0120' => $ownership['number'],
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
            "#0155" => Help::chinanum($project->operation_dur),
            "#0156" => $project->company_title,
            "#0157" => $project->company_address,
            "#0158" => Help::toDateChinese(strtotime($project->operation_from)),
            "#0159" => Help::toDateChinese(strtotime($project->operation_to)),
            "#0160" => Help::toDateChinese(strtotime("+{$project->operation_dur} year", strtotime($project->operation_to))),
            "#0161" => $this->getRegisters('name'),
            "#0162" => $this->getRegisters('list'),
            
            // '#0121' => (string)V('projects/template/ownership', ['project' => $project]),
            // '#083' => (string)V('projects/template/compar_desc', ['project' => $project]),
            // '#084' => (string)V('projects/template/compar_zhishu', ['project' => $project]),
            // '#085' => (string)V('projects/template/adjust_zhishu', ['project' => $project]),

            // "#0123" => (string)V('projects/template/example_object_desc', ['project' => $project]),
            // "#0122" => (string)V('projects/template/example_desc', ['project' => $project]),
            // '#063' => (string)V('projects/template/result', ['project' => $project]),
            // '#080' => (string)V('projects/template/register', ['project' => $project]),
        ];
        return $data;
    }

    public function getRegisters($mode='name')
    {
        $registers = (array)$this->registers;
        $d = [];
        foreach ($registers as $register) {
            if ($mode == 'name') {
                $d[] = $register['name'];
            }
            if ($mode == 'list') {
                $d[] = $register['name'] . "\t" . "(注册号 {$register['number']})";
            }
        }

        if ($mode == 'name') {
            return join('、', $d);
        }
        if ($mode == 'list') {
            return join('<br/>', $d);
        }
        return join('、', $d);
    }

    public function getOwnershipData()
    {
        $data = [];
        $building = $project->building ?: a('building');
        foreach ((array)$building->ownership_cert as $t) {
            $ownership = (array)$building->ownership[$t];
            switch ($t) {
                case \Gini\ORM\Building::OWNERSHIP_BDCQZS:
                    $data["{$t}#owner"] = $ownership['owner'];
                    $data["{$t}#gyqk"] = $ownership['gyqk'];
                    $data["{$t}#zl"] = $ownership['zl'];
                    $data["{$t}#bdcdyh"] = $ownership['bdcdyh'];
                    $data["{$t}#qllx"] = $ownership['qllx'];
                    $data["{$t}#qlxz"] = $ownership['qlxz'];
                    $data["{$t}#yt"] = $ownership['yt'];
                    $data["{$t}#mj"] = $ownership['mj'];
                    $data["{$t}#syqx"] = $ownership['syqx'];
                    $data["{$t}#qlqtzk"] = $ownership['qlqtzk'];
                    $data["{$t}#fj"] = $ownership['fj'];
                    break;
                case \Gini\ORM\Building::OWNERSHIP_BDCQZS_GY:
                    $data["{$t}#owner"] = $ownership['owner'];
                    $data["{$t}#gyqk"] = $ownership['gyqk'];
                    $data["{$t}#zl"] = $ownership['zl'];
                    $data["{$t}#bdcdyh"] = $ownership['bdcdyh'];
                    $data["{$t}#qllx"] = $ownership['qllx'];
                    $data["{$t}#qlxz"] = $ownership['qlxz'];
                    $data["{$t}#yt"] = $ownership['yt'];
                    $data["{$t}#mj"] = $ownership['mj'];
                    $data["{$t}#syqx"] = $ownership['syqx'];
                    $data["{$t}#qlqtzk"] = $ownership['qlqtzk'];
                    $data["{$t}#fj"] = $ownership['fj'];
                    break;
                case \Gini\ORM\Building::OWNERSHIP_TJSFDCQZ:
                    $data["{$t}#owner"] = $ownership['owner'];
                    $data["{$t}#zl"] = $ownership['zl'];
                    $data["{$t}#dh"] = $ownership['dh'];
                    $data["{$t}#th"] = $ownership['th'];
                    $data["{$t}#qsxz"] = $ownership['qllx'];
                    $data["{$t}#yt"] = $ownership['yt'];
                    $data["{$t}#syqlx"] = $ownership['syqlx'];
                    $data["{$t}#zzrq"] = $ownership['zzrq'];
                    $data["{$t}#syqmj"] = $ownership['syqmj'];
                    $data["{$t}#cb"] = $ownership['cb'];
                    $data["{$t}#fwzh"] = $ownership['fwzh'];
                    $data["{$t}#fwfh"] = $ownership['fwfh'];
                    $data["{$t}#fwjg"] = $ownership['fwjg'];
                    $data["{$t}#fwzcs"] = $ownership['fwzcs'];
                    $data["{$t}#fwszcs"] = $ownership['fwszcs'];
                    $data["{$t}#fwjzmj"] = $ownership['fwjzmj'];
                    $data["{$t}#fwsjyt"] = $ownership['fwsjyt'];
                    $data["{$t}#sum"] = $ownership['sum'];
                    $data["{$t}#sum_name"] = $ownership['sum_name'];
                    $data["{$t}#cert_start"] = $ownership['cert_start'];
                    $data["{$t}#cert_end"] = $ownership['cert_end'];
                    $data["{$t}#js"] = $ownership['js'];
                    break;
                case \Gini\ORM\Building::OWNERSHIP_TJSFDCGYQZ:
                    $data["{$t}#owner"] = $ownership['owner'];
                    $data["{$t}#zl"] = $ownership['zl'];
                    $data["{$t}#dh"] = $ownership['dh'];
                    $data["{$t}#th"] = $ownership['th'];
                    $data["{$t}#fdcqlczr"] = $ownership['fdcqlczr'];
                    $data["{$t}#fdcqzzh"] = $ownership['fdcqzzh'];
                    $data["{$t}#fwjzmj"] = $ownership['fwjzmj'];
                    $data["{$t}#tdsyqmj"] = $ownership['tdsyqmj'];
                    $data["{$t}#gyqrszfe"] = $ownership['gyqrszfe'];
                    $data["{$t}#js"] = $ownership['js'];
                    break;
                case \Gini\ORM\Building::OWNERSHIP_FDSYQZ:
                    $data["{$t}#fwsyqz"] = $ownership['fwsyqz'];
                    $data["{$t}#fwzl"] = $ownership['fwzl'];
                    $data["{$t}#qdh"] = $ownership['qdh'];
                    $data["{$t}#fwcb"] = $ownership['fwcb'];
                    $data["{$t}#fwzh"] = $ownership['fwzh'];
                    $data["{$t}#fwfh"] = $ownership['fwfh'];
                    $data["{$t}#fwjg"] = $ownership['fwjg'];
                    $data["{$t}#fwzcs"] = $ownership['fwzcs'];
                    $data["{$t}#fwszcs"] = $ownership['fwszcs'];
                    $data["{$t}#fwjzmj"] = $ownership['fwjzmj'];
                    $data["{$t}#fwsjyt"] = $ownership['fwsjyt'];
                    $data["{$t}#sum"] = $ownership['sum'];
                    $data["{$t}#cert_start"] = $ownership['cert_start'];
                    $data["{$t}#cert_end"] = $ownership['cert_end'];
                    $data["{$t}#tdzh"] = $ownership['tdzh'];
                    $data["{$t}#symj"] = $ownership['symj'];
                    $data["{$t}#qsxz"] = $ownership['qsxz'];
                    $data["{$t}#synx_start_y"] = $ownership['synx_start_y'];
                    $data["{$t}#synx_start_m"] = $ownership['synx_start_m'];
                    $data["{$t}#synx_start_d"] = $ownership['synx_start_d'];
                    $data["{$t}#synx_end_y"] = $ownership['synx_end_y'];
                    $data["{$t}#synx_end_m"] = $ownership['synx_end_m'];
                    $data["{$t}#synx_end_d"] = $ownership['synx_end_d'];
                    $data["{$t}#fj"] = $ownership['fj'];
                    break;
                case \Gini\ORM\Building::OWNERSHIP_FWGYQZ:
                    $data["{$t}#fwgyqz"] = $ownership['fwgyqz'];
                    $data["{$t}#fwsyqczzr"] = $ownership['fwsyqczzr'];
                    $data["{$t}#fwsyqzh"] = $ownership['fwsyqzh'];
                    $data["{$t}#fwzl"] = $ownership['fwzl'];
                    $data["{$t}#fwjzmj"] = $ownership['fwjzmj'];
                    $data["{$t}#gyqszfe"] = $ownership['gyqszfe'];
                    $data["{$t}#fj"] = $ownership['fj'];
                    break;
                case \Gini\ORM\Building::OWNERSHIP_SXB_FWSYQZ:
                    $data["{$t}#fwzl"] = $ownership['fwzl'];
                    $data["{$t}#fwdh"] = $ownership['fwdh'];
                    $data["{$t}#syqrdw"] = $ownership['syqrdw'];
                    $data["{$t}#syqxz"] = $ownership['syqxz'];
                    $data["{$t}#fwgyr"] = $ownership['fwgyr'];
                    $data["{$t}#fwzk#1#zh"] = $ownership['fwzk'][1]['zh'];
                    $data["{$t}#fwzk#1#fh"] = $ownership['fwzk'][1]['fh'];
                    $data["{$t}#fwzk#1#cs"] = $ownership['fwzk'][1]['cs'];
                    $data["{$t}#fwzk#1#jzjg"] = $ownership['fwzk'][1]['jzjg'];
                    $data["{$t}#fwzk#1#js"] = $ownership['fwzk'][1]['js'];
                    $data["{$t}#fwzk#1#jzmj"] = $ownership['fwzk'][1]['jzmj'];
                    $data["{$t}#fwzk#2#zh"] = $ownership['fwzk'][2]['zh'];
                    $data["{$t}#fwzk#2#fh"] = $ownership['fwzk'][2]['fh'];
                    $data["{$t}#fwzk#2#cs"] = $ownership['fwzk'][2]['cs'];
                    $data["{$t}#fwzk#2#jzjg"] = $ownership['fwzk'][2]['jzjg'];
                    $data["{$t}#fwzk#2#js"] = $ownership['fwzk'][2]['js'];
                    $data["{$t}#fwzk#2#jzmj"] = $ownership['fwzk'][2]['jzmj'];
                    $data["{$t}#fwzk#3#zh"] = $ownership['fwzk'][3]['zh'];
                    $data["{$t}#fwzk#3#fh"] = $ownership['fwzk'][3]['fh'];
                    $data["{$t}#fwzk#3#cs"] = $ownership['fwzk'][3]['cs'];
                    $data["{$t}#fwzk#3#jzjg"] = $ownership['fwzk'][3]['jzjg'];
                    $data["{$t}#fwzk#3#js"] = $ownership['fwzk'][3]['js'];
                    $data["{$t}#fwzk#3#jzmj"] = $ownership['fwzk'][3]['jzmj'];
                    $data["{$t}#fwzk#4#zh"] = $ownership['fwzk'][4]['zh'];
                    $data["{$t}#fwzk#4#fh"] = $ownership['fwzk'][4]['fh'];
                    $data["{$t}#fwzk#4#cs"] = $ownership['fwzk'][4]['cs'];
                    $data["{$t}#fwzk#4#jzjg"] = $ownership['fwzk'][4]['jzjg'];
                    $data["{$t}#fwzk#4#js"] = $ownership['fwzk'][4]['js'];
                    $data["{$t}#fwzk#4#jzmj"] = $ownership['fwzk'][4]['jzmj'];
                    $data["{$t}#fwzk#5#zh"] = $ownership['fwzk'][5]['zh'];
                    $data["{$t}#fwzk#5#fh"] = $ownership['fwzk'][5]['fh'];
                    $data["{$t}#fwzk#5#cs"] = $ownership['fwzk'][5]['cs'];
                    $data["{$t}#fwzk#5#jzjg"] = $ownership['fwzk'][5]['jzjg'];
                    $data["{$t}#fwzk#5#js"] = $ownership['fwzk'][5]['js'];
                    $data["{$t}#fwzk#5#jzmj"] = $ownership['fwzk'][5]['jzmj'];
                    $data["{$t}#fwzkjscount"] = $ownership['fwzkjscount'];
                    $data["{$t}#fwzkjzmjcount"] = $ownership['fwzkjzmjcount'];
                    $data["{$t}#jzzdmj"] = $ownership['jzzdmj'];
                    $data["{$t}#ylmj"] = $ownership['ylmj'];
                    $data["{$t}#fwszdong"] = $ownership['fwszdong'];
                    $data["{$t}#fwszxi"] = $ownership['fwszxi'];
                    $data["{$t}#fwsznan"] = $ownership['fwsznan'];
                    $data["{$t}#fwszbei"] = $ownership['fwszbei'];
                    $data["{$t}#fwyt"] = $ownership['fwyt'];
                    $data["{$t}#lqzy"] = $ownership['lqzy'];
                    $data["{$t}#fwjg"] = $ownership['fwjg'];
                    $data["{$t}#fwqyzl"] = $ownership['fwqyzl'];
                    $data["{$t}#fwsl"] = $ownership['fwsl'];
                    $data["{$t}#fwnsje"] = $ownership['fwnsje'];
                    break;
                case \Gini\ORM\Building::OWNERSHIP_GYTDSYQ:
                    $data["{$t}#owner"] = $ownership['owner'];
                    $data["{$t}#fwzl"] = $ownership['fwzl'];
                    $data["{$t}#fwdh"] = $ownership['fwdh'];
                    $data["{$t}#fwth"] = $ownership['fwth'];
                    $data["{$t}#fwdlyt"] = $ownership['fwdlyt'];
                    $data["{$t}#fwqdjg"] = $ownership['fwqdjg'];
                    $data["{$t}#syqlx"] = $ownership['syqlx'];
                    $data["{$t}#fwzzrq"] = $ownership['fwzzrq'];
                    $data["{$t}#syqmj"] = $ownership['syqmj'];
                    $data["{$t}#syqdymj"] = $ownership['syqdymj'];
                    $data["{$t}#syqftmj"] = $ownership['syqftmj'];
                    $data["{$t}#jsdes"] = $ownership['jsdes'];
                    break;
                default:
                    break;
            }
        }
        return $data;
    }
}
