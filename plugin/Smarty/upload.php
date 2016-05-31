<?php
/*
 * Created on 2008-11-20
 * 文件上传类
 */
/******************************************************************************

参数说明:
$max_file_size  : 上传文件大小限制, 单位BYTE
$destination_folder : 上传文件路径
$watermark   : 是否附加水印(1为加水印,其他为不加水印);

使用说明:
1. 将PHP.INI文件里面的"extension=php_gd2.dll"一行前面的;号去掉,因为我们要用到GD库;
2. 将extension_dir =改为你的php_gd2.dll所在目录;

使用例子
$up = new upload;
$up->fileName = $_FILES["upfile"];根据自己的表单来定
$up->imgpreview=1;是否生成缩略图
$up->sw=100;缩略图宽度
$up->sh=150;缩略图高度
$up->up();
echo $up->bImg."<br/>"; 返回图片路径
echo $up->sImg;返回图片路径
******************************************************************************/
?>


<?php
if ($_REQUEST['act']=='upload') {
	foreach ($_FILES['upfile'] as $key => $value) {
		echo $key.'=>'.$value.'<br>';
	}
	var_dump(pathinfo($_FILES['upfile']['name']));
	$up = new Upload;
	$up->fileName = $_FILES["upfile"];
	$up->up();
	echo $up->bImg."<br/>";
}

class Upload {
	//上传文件类型列表
	private $uptypes = array(
	'image/jpg',
	'image/jpeg',
	'image/png',
	'image/pjpeg',
	'image/gif',
	'image/bmp',
	'image/x-png'
	);

	private $max_file_size = 2000000; //上传文件大小限制, 单位BYTE
	private $destination_folder = "uploadimg/"; //上传文件路径
	private $watermark = 1; //是否附加水印(1为加水印,其他为不加水印);
	private $waterstring = "www.heane.com"; //水印字符串
	private $waterimg = "images/logo.gif"; //水印图片gif
	private $watertype = 1; //水印类型(1为文字,2为图片)

	public $fileName; //文件名称
	public $imgpreview; //是否生成缩略图(1为生成,其他为不生成);
	public $sw; //缩略图宽度
	public $sh; //缩略图高度
	public $bImg; //大图的路径
	public $sImg; //小图的路径

	function up() {

		if (!is_uploaded_file($this->fileName[tmp_name]))
		//是否存在文件
		{
			return $this->fileName . "图片不存在!";
			exit;
		}

		if ($this->max_file_size < $this->fileName["size"])
		//检查文件大小
		{
			return "图片太大了超过了$this->max_file_size的限制";
			exit;
		}
		//检查文件类型
		if (!in_array($this->fileName["type"], $this->uptypes)) {
			return "文件类型错误";
			exit;
		}
		//创建文件目录
		if (!file_exists($this->destination_folder)) {
			mkdir($this->destination_folder);
		}
		//上传文件
		$tempName = $this->fileName["tmp_name"];
		$fType = pathinfo($this->fileName["name"]);
		$newName = $this->destination_folder;
		$sImgName = $this->destination_folder;
		$newName .=$fType["filename"].'_'.date('YmdHis',time()).floor(microtime()*1000);
		$sImgName .=$fType["filename"].'_s_'.date('YmdHis',time()).floor(microtime()*1000);

		$newName .= "." . $fType["extension"];
		$sImgName .= "." . $fType["extension"];

		if (!move_uploaded_file($tempName, $newName)) {
			return "移动文件出错";
			exit;
		} else {

			//是否生成缩略图
			if($this->imgpreview == 1){

				$data=GetImageSize($newName);
				switch ($data[2]) {
					case 1 :
						$sImg = imagecreatefromgif($newName);
						break;
					case 2 :
						$sImg = imagecreatefromjpeg($newName);
						break;
					case 3 :
						$sImg = imagecreatefrompng($newName);
						break;
					case 6 :
						$sImg = imagecreatefromwbmp($newName);
						break;
					default :
						die("不支持的文件类型");
						exit;
				}
				$sImgDate=imagecreatetruecolor($this->sw,$this->sh);
				imagecopyresized($sImgDate,$sImg,0,0,0,0,$this->sw,$this->sh,$data[0],$data[1]);
				switch ($data[2]) {
					case 1 :
						imagejpeg($sImgDate, $sImgName);
						break;
					case 2 :
						imagejpeg($sImgDate, $sImgName);
						break;
					case 3 :
						imagepng($sImgDate, $sImgName);
						break;
					case 6 :
						imagewbmp($sImgDate, $sImgName);
						break;
				}
				imagedestroy($sImgDate);
				imagedestroy($sImg);
				$this->sImg=$sImgName;
			}

			//是否增加水印
			if ($this->watermark == 1) {
				$iinfo = getimagesize($newName);
				$nimage = imagecreatetruecolor($iinfo[0], $iinfo[1]);
				$white = imagecolorallocate($nimage, 255, 255, 255);
				$black = imagecolorallocate($nimage, 0, 0, 0);
				$red = imagecolorallocate($nimage, 255, 0, 0);
				imagefill($nimage, 0, 0, $white);
				switch ($iinfo[2]) {
					case 1 :
						$simage = imagecreatefromgif($newName);
						break;
					case 2 :
						$simage = imagecreatefromjpeg($newName);
						break;
					case 3 :
						$simage = imagecreatefrompng($newName);
						break;
					case 6 :
						$simage = imagecreatefromwbmp($newName);
						break;
					default :
						die("不支持的文件类型");
						exit;
				}

				imagecopy($nimage, $simage, 0, 0, 0, 0, $iinfo[0], $iinfo[1]);

				switch ($this->watertype) {
					case 1 : //加水印字符串
						$string_length=strlen($this->waterstring);
						$waterimg_bgwidth=$string_length*6.2;
						imagefilledrectangle($nimage, 1, $iinfo[1] - 12, $waterimg_bgwidth, $iinfo[1], $white);
						imagestring($nimage, 2, 3, $iinfo[1] - 15, $this->waterstring, $black);
						break;
					case 2 : //加水印图片
						$simage1 = imagecreatefromgif($this->waterimg);
						$waterImg = getimagesize($this->waterimg);
						imagecopy($nimage, $simage1, 0, 0, 0, 0, $waterImg[0], $waterImg[1]);
						imagedestroy($simage1);
						break;
				}

				switch ($iinfo[2]) {
					case 1 :
						imagejpeg($nimage, $newName);
						break;
					case 2 :
						imagejpeg($nimage, $newName);
						break;
					case 3 :
						imagepng($nimage, $newName);
						break;
					case 6 :
						imagewbmp($nimage, $newName);
						break;
				}
				//覆盖原上传文件
				imagedestroy($nimage);
				imagedestroy($simage);
			}

			$this->bImg=$newName;
		}
	}
}
?>