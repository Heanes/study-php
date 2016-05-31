<?php
function createUnixTime($date_start, $date_end = '') {
    $datetimeBegin = new DateTime($date_start);
    $date_end = $date_end == '' ? date('Y-m-d') : $date_end;
    $datetimeEnd = new DateTime($date_end);
    $daysCount = $datetimeEnd->diff($datetimeBegin)->days + 1;
    $dateArr = [];
    for ($i = 0; $i < $daysCount; $i++) {
        $time = strtotime($date_start) + $i * 24 * 3600;
        $dateArr[] = [
            'unix_time'   => $time,
            'format_date' => date('Y-m-d', $time),
        ];
    }
    $dateArr['total_days'] = $daysCount;
    return $dateArr;
}
$date_start = $date_end = '';
if(!empty($_GET['s'])){
	$date_start = $_GET['s'];
}else{
	$date_end = date("Y-m-d");
}
if(!empty($_GET['e'])){
	$date_end = $_GET['e'];
}
$dateArr = createUnixTime($date_start, $date_end);
?>
<html>
<head>
    <title>test</title>
</head>
<body>
<div>
    <table>
        <tbody>
        <tr>
            <td style="padding:0 6px;">日期</td>
            <td style="padding:0 6px;">UnixTime</td>
        </tr>
        <?php foreach ($dateArr as $index => $item) { ?>
            <tr>
                <td style="padding:0 6px;"><?php echo $item['format_date']; ?></td>
                <td style="padding:0 6px;"><?php echo $item['unix_time']; ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="2">共<?php echo $dateArr['total_days']; ?>天</td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
