<?php
/**
 * @file exportExcel.php
 * @doc 导出数据到excel中
 * @author 方刚
 * @time 2015/10/21 13:50
 */
//$result = mysql_query("select * from student");
$str = "姓名\t性别\t年龄\t\n";
$str = iconv('utf-8','gb2312',$str);
/*while($row=mysql_fetch_array($result)){
    $name = iconv('utf-8','gb2312',$row['name']);
    $sex = iconv('utf-8','gb2312',$row['sex']);
    $str .= $name."\t".$sex."\t".$row['age']."\t\n";
}*/
$arr=[
    ['name'=>'方刚','sex'=>'男','age'=>'25'],
    ['name'=>'方刚2','sex'=>'男','age'=>'23'],
    ['name'=>'方刚3','sex'=>'男','age'=>'24'],
];
foreach ($arr as $key=>$value) {
    $name = iconv('utf-8','gb2312',$value['name']);
    $sex = iconv('utf-8','gb2312',$value['sex']);
    $str .= $name."\t".$sex."\t".$value['age']."\t\n";
}


$filename = date('Ymd').'.xls';
exportExcel($filename,$str);
// exportExcel函数用于设置header信息。

function exportExcel($filename,$content){
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/vnd.ms-execl");
    header("Content-Type: application/force-download");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment; filename=".$filename);
    header("Content-Transfer-Encoding: binary");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $content;
}