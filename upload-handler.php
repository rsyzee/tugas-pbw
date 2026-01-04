<?php
function upload_foto(array $file): array
{
	$result = ['status' => false, 'message' => ''];

	if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
		$code = isset($file['error']) ? (int)$file['error'] : -1;
		$result['message'] = "Upload failed (error code: $code).";
		return $result;
	}

	$tmpPath  = $file['tmp_name'] ?? '';
	$fileSize = (int)($file['size'] ?? 0);

	if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
		$result['message'] = 'Invalid file upload.';
		return $result;
	}

	$maxBytes = 800000;
	if ($fileSize > $maxBytes) {
		$result['message'] = 'Sorry, your file is too large, max 800KB.';
		return $result;
	}

	$allowedMimeToExt = [
		'image/jpeg' => 'jpg',
		'image/png'  => 'png',
		'image/gif'  => 'gif',
		'image/webp' => 'webp',
	];

	$mime = '';
	if (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		if ($finfo) {
			$mime = (string)finfo_file($finfo, $tmpPath);
			finfo_close($finfo);
		}
	}

	$mime = strtolower(trim($mime));

	if (!isset($allowedMimeToExt[$mime])) {
		$result['message'] = 'Only JPG, JPEG, PNG, GIF & WEBP format are allowed.';
		return $result;
	}

	if (!@getimagesize($tmpPath)) {
		$result['message'] = 'file is not a valid image format.';
		return $result;
	}

	$ext = $allowedMimeToExt[$mime];
	$newName = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

	$uploadDir = __DIR__ . '/img';
	$destPath  = "$uploadDir/$newName";

	if (!is_writable($uploadDir)) {
		$result['message'] = 'Sorry, there was an error uploading your file.';
		return $result;
	}

	if (!@move_uploaded_file($tmpPath, $destPath)) {
		$result['message'] = 'Sorry, there was an error uploading your file.';
		return $result;
	}

	$result['status'] = true;
	$result['message'] = $newName;
	return $result;
}
