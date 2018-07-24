<?php

namespace Gini\ORM;

class Building extends Object
{
    // 业主
    public $owner       = 'string:20';
    // 坐落
    public $address     = 'string:100';
    // 建造年代
    public $year        = 'string:10';
    // 物业管理
    public $property    = 'int:1';
    // 结构
    public $structure   = 'int:1';
    // 总层数
    public $total_floor = 'string:20';
    // 所在层数
    public $floor       = 'string:20';
    // 朝向
    public $front       = 'int:1';
    // 用途
    public $use         = 'int:1';
    // 建筑面积
    public $area        = 'string:20';
    // 户型
    public $type        = 'int:1';
    // 层高
    public $height      = 'string:20';
    // 是否有抵押
    public $mortgage    = 'int:1';
    // 附属物
    public $appurtenance= 'array';
    // 外墙
    public $out_wall    = 'array';
    public $parlour_door_outside    = 'int:1';
    public $parlour_door_inside     = 'int:1';
    public $parlour_window_outside  = 'int:1';
    public $parlour_window_inside   = 'int:1';
    public $parlour_wall            = 'int:1';
    public $parlour_platfond        = 'int:1';
    public $parlour_floor           = 'int:1';
    
    public $room_wall            = 'int:1';
    public $room_platfond        = 'int:1';
    public $room_floor           = 'int:1'; 

    public $toilet_wall            = 'int:1';
    public $toilet_platfond        = 'int:1';
    public $toilet_floor           = 'int:1';
    public $toilet_appurtenance    = 'array';

    public $cook_wall            = 'int:1';
    public $cook_platfond        = 'int:1';
    public $cook_floor           = 'int:1';
    public $cook_appurtenance    = 'array';

    public $veranda_wall         = 'int:1';
    public $veranda_floor        = 'int:1';

    // 照明
    public $lighting            = 'int:1';
    // 一户一表
    public $ammeter             = 'int:1';
    // 烟感报警
    public $smoke_detector      = 'int:1';
    // 上下水
    public $up_down_water       = 'int:1';
    // 有线电视
    public $line_tv             = 'int:1';
    // 喷淋系统
    public $spray_system        = 'int:1';
    // 暖气
    public $heating             = 'int:1';
    // 电话预留
    public $telephone_reservation = 'int:1';
    // 中水
    public $center_water        = 'int:1';
    // 煤气
    public $gas                 = 'int:1';
    // 电梯
    public $lift                = 'int:1';
    // 热水
    public $hot_water           = 'int:1';
    // 对讲
    public $talk_back           = 'int:1';

    // 利用状况
    public $use_status          = 'int:1';
    // 几成新
    public $percent_new         = 'string:50';

    public $east_front          = 'string:100';
    public $south_front         = 'string:100';
    public $west_front          = 'string:100';
    public $north_front         = 'string:100';

    public $amount              = 'string:50';
    public $unit                = 'string:50';
    public $upper_amount        = 'string:50';

    public $ownership_cert      = 'int:1';


    protected static $db_index = [
        'address', 'owner'
    ];

    const GHJG = 1;
    const ZHJG = 2;
    const ZMJG = 3;
    const GJG = 4;
    const JYJG = 5;
    const QTJG = 6;

    static $structure_s = [
        self::GHJG => '钢混结构',
        self::ZHJG => '砖混结构',
        self::ZMJG => '砖木结构',
        self::GJG => '钢结构',
        self::JYJG => '简易结构',
        self::QTJG => '其他结构'
    ];

    const NB = 1;
    const DN = 2;
    const DB = 3;
    const XN = 4;
    const XB = 5;
    const DD = 6;
    const XX = 7;
    const NN = 8;
    const BB = 9;

    static $front_s = [
        self::NB => '南北',
        self::DN => '东南',
        self::DB => '东北',
        self::XN => '西南',
        self::XB => '西北',
        self::DD => '东',
        self::XX => '西',
        self::NN => '南',
        self::BB => '北'
    ];

    const USE_ZZ = 1;
    const USE_BG = 2;
    const USE_SY = 3;
    const USE_GY = 4;
    const USE_QT = 5;

    static $use_s = [
        self::USE_ZZ => '住宅',
        self::USE_BG => '办公',
        self::USE_SY => '商业',
        self::USE_GY => '工业',
        self::USE_QT => '其他'
    ];

    const TYPE_ONE = 1;
    const TYPE_TWO = 2;
    const TYPE_THREE = 3;
    const TYPE_FOUR = 4;
    const TYPE_FIVE = 5;
    const TYPE_SIX = 6;

    static $type_s = [
        self::TYPE_ONE => '一室一厅',
        self::TYPE_TWO => '二室一厅',
        self::TYPE_THREE => '三室一厅',
        self::TYPE_FOUR => '三室二厅',
        self::TYPE_FIVE => '一梯户',
        self::TYPE_SIX => '其他'
    ];

    const APP_XY = 1;
    const APP_GL = 2;
    const APP_LT = 3;
    const APP_DXS = 4;
    const APP_CK = 5;

    static $appurtenance_s = [
        self::APP_XY => '小院',
        self::APP_GL => '阁楼',
        self::APP_LT => '露台',
        self::APP_DXS => '地下室',
        self::APP_CK => '车库'
    ];

    const OUT_ALL_DBQ = 1;
    const OUT_ALL_QSQ = 2;
    const OUT_ALL_SSS = 3;
    const OUT_ALL_SNZM = 4;
    const OUT_ALL_STL = 5;
    const OUT_ALL_CZ = 6;
    const OUT_ALL_MSK = 7;
    const OUT_ALL_MHQ = 8;
    const OUT_ALL_TMQ = 9;
    const OUT_ALL_QT = 10;

    static $out_wall_s = [
        self::OUT_ALL_DBQ => '大白墙',
        self::OUT_ALL_QSQ => '清水墙',
        self::OUT_ALL_SSS => '水刷石',
        self::OUT_ALL_SNZM => '水泥罩面',
        self::OUT_ALL_STL => '刷涂料',
        self::OUT_ALL_CZ => '瓷砖',
        self::OUT_ALL_MSK => '马赛克',
        self::OUT_ALL_MHQ => '抹灰墙',
        self::OUT_ALL_TMQ => '贴面墙',
        self::OUT_ALL_QT => '其他'
    ];

    const DOOR_OUTSIZE_MZ = 1;
    const DOOR_OUTSIZE_LHJ = 2;
    const DOOR_OUTSIZE_SG = 3;
    const DOOR_OUTSIZE_FDM = 4;
    const DOOR_OUTSIZE_QT = 5;

    static $door_outside_s = [
        self::DOOR_OUTSIZE_MZ => '木制',
        self::DOOR_OUTSIZE_LHJ => '铝合金',
        self::DOOR_OUTSIZE_SG => '塑钢',
        self::DOOR_OUTSIZE_FDM => '防盗门',
        self::DOOR_OUTSIZE_QT => '其他'
    ];

    const DOOR_INSIZE_MM = 1;
    const DOOR_INSIZE_LHJ = 2;
    const DOOR_INSIZE_GMM = 3;
    const DOOR_INSIZE_SG = 4;
    const DOOR_INSIZE_TLM = 5;
    const DOOR_INSIZE_QT = 6;

    static $door_inside_s = [
        self::DOOR_INSIZE_MM => '木门',
        self::DOOR_INSIZE_LHJ => '铝合金',
        self::DOOR_INSIZE_GMM => '钢木门',
        self::DOOR_INSIZE_SG => '塑钢',
        self::DOOR_INSIZE_TLM => '推拉门',
        self::DOOR_INSIZE_QT => '其他'
    ];

    const WINDOW_OUTSIZE_MC = 1;
    const WINDOW_OUTSIZE_GC = 2;
    const WINDOW_OUTSIZE_LHJ = 3;
    const WINDOW_OUTSIZE_BLG = 4;
    const WINDOW_OUTSIZE_SG = 5;

    static $window_outside_s = [
        self::WINDOW_OUTSIZE_MC => '木制',
        self::WINDOW_OUTSIZE_GC => '钢窗',
        self::WINDOW_OUTSIZE_LHJ => '铝合金',
        self::WINDOW_OUTSIZE_BLG => '玻璃钢',
        self::WINDOW_OUTSIZE_SG => '塑钢'
    ];

    const WINDOW_INSIZE_MC = 1;
    const WINDOW_INSIZE_GC = 2;
    const WINDOW_INSIZE_LHJ = 3;
    const WINDOW_INSIZE_BLG = 4;
    const WINDOW_INSIZE_QT = 5;

    static $window_inside_s = [
        self::WINDOW_INSIZE_MC => '木制',
        self::WINDOW_INSIZE_GC => '钢窗',
        self::WINDOW_INSIZE_LHJ => '铝合金',
        self::WINDOW_INSIZE_BLG => '玻璃钢',
        self::WINDOW_INSIZE_QT => '其他'
    ];

    // 抹灰墙；石灰浆；乳胶漆；壁纸；壁毡；喷涂；木墙裙；暖气罩；其他
    const PARLOUR_WALL_MHQ = 1;
    const PARLOUR_WALL_SHJ = 2;
    const PARLOUR_WALL_RJQ = 3;
    const PARLOUR_WALL_BZ = 4;
    const PARLOUR_WALL_BZHAN = 5;
    const PARLOUR_WALL_PT = 6;
    const PARLOUR_WALL_MQQ = 7;
    const PARLOUR_WALL_NQZ = 8;
    const PARLOUR_WALL_QT = 9;

    static $parlour_wall_s = [
        self::PARLOUR_WALL_MHQ => '抹灰墙',
        self::PARLOUR_WALL_SHJ => '石灰浆',
        self::PARLOUR_WALL_RJQ => '乳胶漆',
        self::PARLOUR_WALL_BZ => '壁纸',
        self::PARLOUR_WALL_BZHAN => '壁毡',
        self::PARLOUR_WALL_PT => '喷涂',
        self::PARLOUR_WALL_MQQ => '木墙裙',
        self::PARLOUR_WALL_NQZ => '暖气罩',
        self::PARLOUR_WALL_QT => '其他'
    ];

    const PARLOUR_PLATFOND_MHDP = 1;
    const PARLOUR_PLATFOND_BCDP = 2;
    const PARLOUR_PLATFOND_RJQ = 3;
    const PARLOUR_PLATFOND_SGX = 4;
    const PARLOUR_PLATFOND_DC = 5;
    const PARLOUR_PLATFOND_MQGPLDD = 6;
    const PARLOUR_PLATFOND_QT = 7;

    // 抹灰顶棚；板材顶棚；乳胶漆；石膏线；灯池；木棋格玻璃吊顶；其他
    static $parlour_platfond_s = [
        self::PARLOUR_PLATFOND_MHDP => '抹灰顶棚',
        self::PARLOUR_PLATFOND_BCDP => '板材顶棚',
        self::PARLOUR_PLATFOND_RJQ => '乳胶漆',
        self::PARLOUR_PLATFOND_SGX => '石膏线',
        self::PARLOUR_PLATFOND_DC => '灯池',
        self::PARLOUR_PLATFOND_MQGPLDD => '木棋格玻璃吊顶',
        self::PARLOUR_PLATFOND_QT => '其他'
    ];

    const PARLOUR_FLOOR_SNSJ = 1;
    const PARLOUR_FLOOR_SMS = 2;
    const PARLOUR_FLOOR_SS = 3;
    const PARLOUR_FLOOR_LQHNT = 4;
    const PARLOUR_FLOOR_ST = 5;
    const PARLOUR_FLOOR_CZ = 6;
    const PARLOUR_FLOOR_DLS = 7;
    const PARLOUR_FLOOR_HGY = 8;
    const PARLOUR_FLOOR_MDB = 9;
    const PARLOUR_FLOOR_FHDB = 10;
    const PARLOUR_FLOOR_SLDBZ = 11;
    const PARLOUR_FLOOR_SY = 12;
    const PARLOUR_FLOOR_QT = 13;

    // 水泥砂浆；水磨石；碎石；沥青混凝土；素土；瓷砖；大理石；花岗岩；木地板；复合地板；塑料地板砖；石英；其他
    static $parlour_floor_s = [
        self::PARLOUR_FLOOR_SNSJ => '水泥砂浆',
        self::PARLOUR_FLOOR_SMS => '水磨石',
        self::PARLOUR_FLOOR_SS => '碎石',
        self::PARLOUR_FLOOR_LQHNT => '沥青混凝土',
        self::PARLOUR_FLOOR_ST => '素土',
        self::PARLOUR_FLOOR_CZ => '瓷砖',
        self::PARLOUR_FLOOR_DLS => '大理石',
        self::PARLOUR_FLOOR_HGY => '花岗岩',
        self::PARLOUR_FLOOR_MDB => '木地板',
        self::PARLOUR_FLOOR_FHDB => '复合地板',
        self::PARLOUR_FLOOR_SLDBZ => '塑料地板砖',
        self::PARLOUR_FLOOR_SY => '石英',
        self::PARLOUR_FLOOR_QT => '其他'
    ];

    const TOILET_WALL_RJQ = 1;
    const TOILET_WALL_MH = 2;
    const TOILET_WALL_CZBQ = 3;
    const TOILET_WALL_CZDD = 4;
    const TOILET_WALL_QT = 5;

    // 乳胶漆；抹灰；瓷砖半墙；瓷砖到顶；其他
    static $toilet_wall_s = [
        self::TOILET_WALL_RJQ => '乳胶漆',
        self::TOILET_WALL_MH => '抹灰',
        self::TOILET_WALL_CZBQ => '瓷砖半墙',
        self::TOILET_WALL_CZDD => '瓷砖到顶',
        self::TOILET_WALL_QT => '其他'
    ];

    const TOILET_PLATFOND_WDD = 1;
    const TOILET_PLATFOND_PVCDD = 2;
    const TOILET_PLATFOND_LZKB = 3;
    const TOILET_PLATFOND_MQGPLDD = 4;
    const TOILET_PLATFOND_QT = 5;

    // 无吊顶；PVC吊顶；铝制扣板；木棋格玻璃吊顶；其他
    static $toilet_platfond_s = [
        self::TOILET_PLATFOND_WDD => '无吊顶',
        self::TOILET_PLATFOND_PVCDD => 'PVC吊顶',
        self::TOILET_PLATFOND_LZKB => '铝制扣板',
        self::TOILET_PLATFOND_MQGPLDD => '木棋格玻璃吊顶',
        self::TOILET_PLATFOND_QT => '其他'
    ];

    const TOILET_FLOOR_SN = 1;
    const TOILET_FLOOR_CZ = 2;
    const TOILET_FLOOR_MSK = 3;
    const TOILET_FLOOR_QT = 4;

    // 水泥；瓷砖；马赛克；其他
    static $toilet_floor_s = [
        self::TOILET_FLOOR_SN => '水泥',
        self::TOILET_FLOOR_CZ => '瓷砖',
        self::TOILET_FLOOR_MSK => '马赛克',
        self::TOILET_FLOOR_QT => '其他'
    ];

    const TOILET_APP_DBQ = 1;
    const TOILET_APP_ZBQ = 2;
    const TOILET_APP_YP = 3;
    const TOILET_APP_XSP = 4;
    const TOILET_APP_HZT = 5;
    const TOILET_APP_DG = 6;
    const TOILET_APP_QT = 7;

    // 蹲便器；坐便器；浴盆；洗手盆；化装台；吊柜；其他
    static $toilet_appurtenance_s = [
        self::TOILET_APP_DBQ => '蹲便器',
        self::TOILET_APP_ZBQ => '坐便器',
        self::TOILET_APP_YP => '浴盆',
        self::TOILET_APP_XSP => '洗手盆',
        self::TOILET_APP_HZT => '化妆台',
        self::TOILET_APP_DG => '吊柜',
        self::TOILET_APP_QT => '其他'
    ];

    const COOK_APP_CP = 1;
    const COOK_APP_LLT = 2;
    const COOK_APP_DG = 3;
    const COOK_APP_XDG = 4;
    const COOK_APP_QT = 5;

    //菜盆；料理台；吊柜；消毒柜；其他
    static $cook_appurtenance_s = [
        self::COOK_APP_CP => '菜盆',
        self::COOK_APP_LLT => '料理台',
        self::COOK_APP_DG => '吊柜',
        self::COOK_APP_XDG => '消毒柜',
        self::COOK_APP_QT => '其他'
    ];

    const VERANDA_WALL_MH = 1;
    const VERANDA_WALL_RJQ = 2;
    const VERANDA_WALL_TL = 3;
    const VERANDA_WALL_CZ = 4;
    const VERANDA_WALL_QT = 5;

    //抹灰；乳胶漆；涂料；瓷砖；其他
    static $veranda_wall_s = [
        self::VERANDA_WALL_MH => '抹灰',
        self::VERANDA_WALL_RJQ => '乳胶漆',
        self::VERANDA_WALL_TL => '涂料',
        self::VERANDA_WALL_CZ => '瓷砖',
        self::VERANDA_WALL_QT => '其他'
    ];

    const VERANDA_FLOOR_SN = 1;
    const VERANDA_FLOOR_CZ = 2;
    const VERANDA_FLOOR_QT = 3;

    //水泥、瓷砖；其他
    static $veranda_floor_s = [
        self::VERANDA_FLOOR_SN => '水泥',
        self::VERANDA_FLOOR_CZ => '瓷砖',
        self::VERANDA_FLOOR_QT => '其他'
    ];

    const OWNERSHIP_BDCQZS = 1;
    const OWNERSHIP_BDCQZS_GY = 2;
    const OWNERSHIP_TJSFDCQZ = 3;
    const OWNERSHIP_TJSFDCGYQZ = 4;
    const OWNERSHIP_FDSYQZ = 5;
    const OWNERSHIP_FWGYQZ = 6;
    const OWNERSHIP_SXB_FWSYQZ = 7;
    const OWNERSHIP_GYTDSYQ = 8;

    static $ownership_cert_type = [
        self::OWNERSHIP_BDCQZS => '不动产权证书',
        self::OWNERSHIP_BDCQZS_GY => '不动产权证书(公有)',
        self::OWNERSHIP_TJSFDCQZ => '天津市房地产权证',
        self::OWNERSHIP_TJSFDCGYQZ => '天津市房地产共有权证',
        self::OWNERSHIP_FDSYQZ => '房地所有权证',
        self::OWNERSHIP_FWGYQZ => '房屋共有权证',
        self::OWNERSHIP_SXB_FWSYQZ => '房屋共有权证(手写版)',
        self::OWNERSHIP_GYTDSYQ => '国有土地使用权'
    ];
}