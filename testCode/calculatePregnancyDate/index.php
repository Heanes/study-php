<?php
date_default_timezone_set('PRC');
/**
 * @doc 根据宝宝出生不同星座计算怀孕合适日期
 * @author Heanes fang <heanes@163.com>
 * @time 2016-06-16 17:01:43 周四
 */
// 生肖年份库
$yearAnimalLib = [
    '鼠','牛','虎','兔','龙','蛇','马','羊','猴','鸡','狗','猪',
];
// 星座日期库
$constellationDateLib = [
    '01shuiping' => [
        'name' => '水瓶座',
        'dateStart' => '01-21',
        'dateEnd' => '02-19',
    ],
    '02shuangyu' => [
        'name' => '双鱼座',
        'dateStart' => '02-20',
        'dateEnd' => '03-20',
    ],
    '03baiyang' => [
        'name' => '白羊座',
        'dateStart' => '03-21',
        'dateEnd' => '04-20',
    ],
    '04jinniu' => [
        'name' => '金牛座',
        'dateStart' => '04-21',
        'dateEnd' => '05-21',
    ],
    '05shuangzi' => [
        'name' => '双子座',
        'dateStart' => '05-22',
        'dateEnd' => '06-21',
    ],
    '06juxie' => [
        'name' => '巨蟹座',
        'dateStart' => '06-22',
        'dateEnd' => '07-23',
    ],
    '07shizi' => [
        'name' => '狮子座',
        'dateStart' => '07-24',
        'dateEnd' => '08-23',
    ],
    '08chunv' => [
        'name' => '处女座',
        'dateStart' => '08-24',
        'dateEnd' => '09-23',
    ],
    '09tiancheng' => [
        'name' => '天秤座',
        'dateStart' => '09-24',
        'dateEnd' => '10-23',
    ],
    '10tianxie' => [
        'name' => '天蝎座',
        'dateStart' => '10-24',
        'dateEnd' => '11-22',
    ],
    '11sheshou' => [
        'name' => '射手座',
        'dateStart' => '11-23',
        'dateEnd' => '12-22',
    ],
    '12mojie' => [
        'name' => '摩羯座',
        'dateStart' => '12-23',
        'dateEnd' => '01-22',
    ],
];

$currentTimeInfo = [
    'year' => date('Y'),
];
$yearList = [];
for($i = 0; $i < 48; $i++){
    $yearTemp = $currentTimeInfo['year'] + $i + 1;
    $remainder = ($yearTemp + 8) % 12;
    $yearList[] = [
        'year' => $yearTemp,
        'animal' => $yearAnimalLib[$remainder],
    ];
}

if(isset($_GET['act']) && $_GET['act'] == 'calc'){
    $selectConstellation = $_REQUEST['selectConstellation'];
    $selectYear = $_REQUEST['selectYear'];
    $selectDateArr = $constellationDateLib[$selectConstellation];
    $calculateDateArr = [
        'dateStart' => date('Y-m-d', strtotime('-40 week', strtotime($selectYear. '-' . $selectDateArr['dateStart']))),
        'dateEnd' => date('Y-m-d', strtotime('-37 week', strtotime($selectYear. '-' . $selectDateArr['dateEnd']))),
    ];
    $result = [
        'dateStart' => $calculateDateArr['dateStart'],
        'dateEnd' => $calculateDateArr['dateEnd'],
        'selectConstellation' => $selectConstellation,
        'selectYear' => $selectYear,
    ];
    ob_clean();
    echo json_encode($result);
    exit;
}

?>

<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="renderer" content="webkit" />
    <meta name="author" content="Heanes heanes.com email(heanes@163.com)" />
    <meta name="keywords" content="软件,商务,HTML,tutorials,source codes" />
    <meta name="description" content="描述，不超过150个字符" />
    <link rel="shortcut icon" href="image/favicon/favicon.ico" />
    <link rel="bookmark" href="image/favicon/favicon.ico" />
    <title>根据宝宝出生不同星座计算怀孕合适日期</title>
    <link rel="stylesheet" type="text/css" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" href="css/base.css" />
    <link rel="stylesheet" type="text/css" href="css/style/style-2015.css" />
    <link rel="stylesheet" type="text/css" href="css/common.css" />
    <link rel="stylesheet" type="text/css" href="css/css.css" />
</head>
<body>
<div class="center wrap">
    <!-- S 主要内容 S -->
    <div class="main">
        <!-- 主体内容 -->
        <div class="main-content main-wrap clearfix">
            <!-- 中心区域 -->
            <div class="center-block center-wrap">
                <div class="container">
                    <div class="title">
                        <h1>根据宝宝出生不同星座计算怀孕合适日期</h1>
                    </div>
                    <div class="content">
                        <div class="calculate-pregnancy-date">
                            <div class="select-constellation">
                                <form class="select-constellation-form">
                                    <div class="form-group inline-group">
                                        <div class="form-label">
                                            <label for="selectConstellationYear">请选择年份</label>
                                        </div>
                                        <div class="form-input">
                                            <select class="form-normal-input select" id="selectConstellationYear">
                                                <?php foreach ($yearList as $year){?>
                                                    <option value="<?php echo $year['year'];?>"><?php echo $year['year'].' '.$year['animal'];?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group inline-group">
                                        <div class="form-label">
                                            <label for="selectConstellationDate">请选择星座</label>
                                        </div>
                                        <div class="form-input relative">
                                            <select class="form-normal-input select select-constellation" id="selectConstellationDate">
                                                <?php foreach ($constellationDateLib as $index => $item) {?>
                                                    <option class="<?php echo $index;?>" value="<?php echo $index;?>" <?php if($index == '08chunv'){?>selected<?php }?>><?php echo $item['name'].' '.$item['dateStart'].'/'.$item['dateEnd'];?></option>
                                                <?php }?>
                                            </select>
                                            <span id="selectConstellationIcon"></span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="calculate-result-block">
                                <div class="result-tittle">
                                    <h2>计算结果</h2>
                                </div>
                                <div class="result-content">
                                    <div class="result-row resultDateStart">
                                        <span class="row-title">最早日期</span>
                                        <span class="row-value" id="earliestDate"></span>
                                    </div>
                                    <div class="result-row resultDateEnd">
                                        <span class="row-title">最晚日期</span>
                                        <span class="row-value" id="latestDate"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
<script type="application/javascript">
    $(function () {
        $('#selectConstellationDate, #selectConstellationYear').on('change', function () {
            var selectConstellation = $('#selectConstellationDate').val();
            var selectYear = $('#selectConstellationYear').val();
            $.ajax({
                url:'index.php?act=calc',
                type: "POST",
                data: {'selectConstellation':selectConstellation,'selectYear':selectYear},
                dataType: "json",
                success: function (result) {
                    $('#earliestDate').empty().html(result.dateStart);
                    $('#latestDate').empty().html(result.dateEnd);
                },
                fail: function (result) {
                    alert('出现问题：' + result.message);
                }
            });
            var selectConstellationIconClass = selectConstellation.substring(2, selectConstellation.length);
            $('#selectConstellationIcon').attr('class', '').addClass(selectConstellationIconClass);
        });
        $('#selectConstellationDate').trigger('change');
    });
</script>
</body>
</html>
