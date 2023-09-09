<?php

namespace DC\AdvancedDownload\Repository;

use XF\Mvc\Entity\Repository;

class Download extends Repository
{
	public function getAttachmentContainerUrl(\XF\Entity\Attachment $attachment)
	{
		/** Build post link */
		switch ($attachment->content_type)
		{
			case 'post':
				return $this->app()->router('public')->buildLink('posts/' . $attachment->content_id);
			case 'resource_version':
				return $this->app()->router('public')->buildLink('resources/' . $attachment->content_id);
			default:
				return '';
		}
	}
	
	public function getFileSizeData($fileSize)
	{
		switch ($fileSize)
		{
			case $fileSize >= 1000000:
				return [
					'file_size' => round($fileSize * 0.000001, 2),
					'file_size_ex' => 'MB'
				];
			case $fileSize >=1000 && $fileSize < 1000000:
				return [
					'file_size' => round($fileSize * 0.001, 2),
					'file_size_ex' => 'KB'
				];
			case $fileSize < 1000:
				return [
					'file_size' => $fileSize,
					'file_size_ex' => 'Bytes'
				];
		}
	}
}