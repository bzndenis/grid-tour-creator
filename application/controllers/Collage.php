<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Input $input
 */
class Collage extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		header('X-Frame-Options: SAMEORIGIN');
	}

	public function index()
	{
		$this->load->view('collage_editor');
	}

	// Server-side render using GD
	public function generate()
	{
		// Expecting multipart/form-data with files img1..img6 and texts
		$this->load->helper('file');

		$gridWidth = (int)($this->input->post('width') ?: 1440);
		$gridHeight = (int)($this->input->post('height') ?: 2160);
		$bgColor = $this->input->post('bg') ?: '#1f2937'; // gray-800
		$leftName = $this->input->post('leftName') ?: '';
		$rightName = $this->input->post('rightName') ?: '';
		$r1 = $this->input->post('r1') ?: 'R1';
		$r2 = $this->input->post('r2') ?: 'R2';
		$r3 = $this->input->post('r3') ?: 'R3';

		$canvas = imagecreatetruecolor($gridWidth, $gridHeight);
		list($r,$g,$b) = $this->hexToRgb($bgColor);
		$bg = imagecolorallocate($canvas, $r, $g, $b);
		imagefill($canvas, 0, 0, $bg);
		imageantialias($canvas, true);

		$cols = 2; $rows = 3; $gap = 28; // spacing
		$tileW = (int)(($gridWidth - ($gap * ($cols + 1))) / $cols);
		$tileH = (int)(($gridHeight - ($gap * ($rows + 1))) / $rows);

		// Load fonts (prioritas: bundled Inter -> Windows Arial -> DejaVu)
		list($fontRegular, $fontBold) = $this->resolveFonts();

		// Draw grid images
		for ($i = 1; $i <= 6; $i++) {
			if (!isset($_FILES['img'.$i]) || $_FILES['img'.$i]['error'] !== UPLOAD_ERR_OK) continue;
			$src = $this->createImageFromUpload($_FILES['img'.$i]['tmp_name']);
			if (!$src) continue;
			$col = ($i % 2 === 1) ? 0 : 1; // 1,3,5 left ; 2,4,6 right
			$row = (int)floor(($i-1) / 2);
			$dstX = $gap + $col * ($tileW + $gap);
			$dstY = $gap + $row * ($tileH + $gap);
			$this->smartCopyResize($canvas, $src, $dstX, $dstY, $tileW, $tileH, 18);
			imagedestroy($src);
		}

		// Overlay round labels R1, R2, R3 on left and right tiles midline
		$white = imagecolorallocate($canvas, 255,255,255);
		$shadow = imagecolorallocatealpha($canvas, 0,0,0,80);
		$labelFont = $fontBold;
		$labelSize = (int)($tileH * 0.35);
		$centerX = (int)($gridWidth/2);
		$centerYs = [
			(int)($gap + $tileH/2),
			(int)($gap*2 + $tileH*1.5),
			(int)($gap*3 + $tileH*2.5)
		];
		$labels = [$r1,$r2,$r3];
		for ($j=0;$j<3;$j++){
			$text = $labels[$j];
			$bbox = imagettfbbox($labelSize, 0, $labelFont, $text);
			$textW = $bbox[2]-$bbox[0];
			$textH = $bbox[1]-$bbox[7];
			$x = $centerX - (int)($textW/2);
			$y = $centerYs[$j] + (int)($textH/2);
			// shadow then text
			imagettftext($canvas, $labelSize, 0, $x+4, $y+4, $shadow, $labelFont, $text);
			imagettftext($canvas, $labelSize, 0, $x, $y, $white, $labelFont, $text);
		}

		// Corner names (bold + stroke/outline for readability)
		$nameFont = $fontBold;
		$nameSize = (int)($gridWidth * 0.065);
		$leftPad = $gap; $topPad = (int)($gap*0.8);
		$stroke = imagecolorallocatealpha($canvas, 0,0,0,70);
		$this->ttfStroke($canvas, $nameSize, 0, $leftPad, $topPad + $nameSize, $white, $stroke, $nameFont, $leftName, 3);
		$bboxR = imagettfbbox($nameSize, 0, $nameFont, $rightName);
		$rightTextW = $bboxR[2]-$bboxR[0];
		$this->ttfStroke($canvas, $nameSize, 0, $gridWidth - $rightTextW - $leftPad, $topPad + $nameSize, $white, $stroke, $nameFont, $rightName, 3);

		// Output
		header('Content-Type: image/png');
		header('Content-Disposition: inline; filename="collage.png"');
		imagepng($canvas, null, 9);
		imagedestroy($canvas);
	}

	private function hexToRgb($hex)
	{
		$hex = ltrim($hex, '#');
		if (strlen($hex) === 3) {
			$hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
		}
		return [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
	}

	private function createImageFromUpload($path)
	{
		$info = getimagesize($path);
		if (!$info) return null;
		switch ($info[2]) {
			case IMAGETYPE_JPEG: return imagecreatefromjpeg($path);
			case IMAGETYPE_PNG: return imagecreatefrompng($path);
			case IMAGETYPE_GIF: return imagecreatefromgif($path);
			default: return null;
		}
	}

	private function smartCopyResize($dstCanvas, $srcImg, $dstX, $dstY, $dstW, $dstH, $radius = 0)
	{
		$srcW = imagesx($srcImg); $srcH = imagesy($srcImg);
		$srcRatio = $srcW / $srcH; $dstRatio = $dstW / $dstH;
		if ($srcRatio > $dstRatio) {
			$cropH = $srcH; $cropW = (int)($srcH * $dstRatio);
			// Fokus di kiri: mulai dari x=0
			$cropX = 0; $cropY = 0;
		} else {
			$cropW = $srcW; $cropH = (int)($srcW / $dstRatio);
			$cropX = 0; $cropY = (int)(($srcH - $cropH)/2);
		}
		$temp = imagecreatetruecolor($dstW, $dstH);
		imagecopyresampled($temp, $srcImg, 0, 0, $cropX, $cropY, $dstW, $dstH, $cropW, $cropH);
		if ($radius > 0) {
			$this->applyRoundedCorners($temp, $radius);
		}
		imagecopy($dstCanvas, $temp, $dstX, $dstY, 0, 0, $dstW, $dstH);
		imagedestroy($temp);
	}

	private function applyRoundedCorners(&$img, $radius)
	{
		$w = imagesx($img); $h = imagesy($img);
		$mask = imagecreatetruecolor($w, $h);
		$transparent = imagecolorallocate($mask, 0, 0, 0);
		$opaque = imagecolorallocate($mask, 255, 255, 255);
		imagefill($mask, 0, 0, $transparent);
		imagefilledrectangle($mask, $radius, 0, $w-$radius, $h, $opaque);
		imagefilledrectangle($mask, 0, $radius, $w, $h-$radius, $opaque);
		imagefilledellipse($mask, $radius, $radius, $radius*2, $radius*2, $opaque);
		imagefilledellipse($mask, $w-$radius, $radius, $radius*2, $radius*2, $opaque);
		imagefilledellipse($mask, $radius, $h-$radius, $radius*2, $radius*2, $opaque);
		imagefilledellipse($mask, $w-$radius, $h-$radius, $radius*2, $radius*2, $opaque);
		imagecolortransparent($mask, $transparent);
		imagecopymerge($img, $mask, 0,0,0,0,$w,$h,100);
		imagedestroy($mask);
	}

	private function resolveFonts()
	{
		$fontDir = APPPATH.'third_party/fonts/';
		$interRegular = $fontDir.'Inter-Regular.ttf';
		$interBold = $fontDir.'Inter-Bold.ttf';
		if (file_exists($interRegular) && file_exists($interBold)) {
			return [$interRegular, $interBold];
		}
		// Windows common fonts
		$winRegular = 'C:\\Windows\\Fonts\\arial.ttf';
		$winBold = 'C:\\Windows\\Fonts\\arialbd.ttf';
		if (file_exists($winRegular) && file_exists($winBold)) {
			return [$winRegular, $winBold];
		}
		// Linux common fonts
		$dejavuRegular = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
		$dejavuBold = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
		if (file_exists($dejavuRegular) && file_exists($dejavuBold)) {
			return [$dejavuRegular, $dejavuBold];
		}
		// final fallback: use same existing if only one exists
		return [file_exists($interRegular)?$interRegular:$winRegular, file_exists($interBold)?$interBold:$winBold];
	}

	private function ttfStroke($image, $size, $angle, $x, $y, $textColor, $strokeColor, $fontFile, $text, $px)
	{
		for ($c1 = ($x-$px); $c1 <= ($x+$px); $c1++) {
			for ($c2 = ($y-$px); $c2 <= ($y+$px); $c2++) {
				imagettftext($image, $size, $angle, $c1, $c2, $strokeColor, $fontFile, $text);
			}
		}
		imagettftext($image, $size, $angle, $x, $y, $textColor, $fontFile, $text);
	}
}


