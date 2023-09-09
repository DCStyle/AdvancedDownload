<?php

namespace DC\AdvancedDownload\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class ResourceVersion extends XFCP_ResourceVersion
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$bypass = false;
		if (\XF::isAddOnActive('DC/AdvancedDownloadPro'))
		{
			/** @var \DC\AdvancedDownloadPro\Repository\DownloadPro $downloadProRepo */
			$downloadProRepo = $this->repository('DC\AdvancedDownloadPro:DownloadPro');
			$bypass = $downloadProRepo->isInByPassUserGroup(\XF::visitor());
		}
		
		if ($action == 'Download' && !$bypass)
		{
			$version = $this->assertViewableVersion($params->resource_version_id);
			if ($version->isDownloadable() && $version->canDownload())
			{
				$options = $this->options();
				$resource = $version->Resource;
				$postUrl = $this->app()->router('public')->buildLink('resources', $resource);
				
				$session1 = $_GET['sess'] ?? 1;
				$session2 = $_POST['sess'] ?? 2;
				
				if ($session1 !== $session2)
				{
					if ($version->download_url)
					{
						$viewParams = [
							'sess'          => md5('DC_AdvancedDownload_DownloadExternal' . \XF::$time),
							'postUrl'       => $postUrl,
							'fileName'      => $resource->title,
							'formUrl'       => $version->download_url
						];
						
						throw $this->exception($this->view('XFRM:ResourceVersion\Download', 'DC_AdvancedDownload_DownloadExternal', $viewParams));
					} else {
						/** @var \XF\Entity\Attachment $attachment */
						$attachment = $this->getAttachmentFromChooser($version);
						if ($attachment)
						{
							$fileExtensions = $options->DC_AdvancedDownload_fileEx;
							$fileExSplited = preg_split('/\s+/', $fileExtensions, -1, PREG_SPLIT_NO_EMPTY);
							
							if (!in_array($attachment->extension, $fileExSplited))
							{
								/** @var \DC\AdvancedDownload\Repository\Download $downloadRepo */
								$downloadRepo = $this->repository('DC\AdvancedDownload:Download');
								
								$fileSizeData = $downloadRepo->getFileSizeData($attachment->file_size);
								$fileSize = $fileSizeData['file_size'];
								$fileSizeEx = $fileSizeData['file_size_ex'];
								
								$viewParams = [
									'sess'          => md5('DC_AdvancedDownload_DownloadInternal' . \XF::$time),
									'attachment'    => $attachment,
									'fileSize'      => "$fileSize $fileSizeEx",
									'postUrl'       => $postUrl,
								];
								
								throw $this->exception($this->view('XFRM:ResourceVersion\Download', 'DC_AdvancedDownload_DownloadInternal', $viewParams));
							}
						}
					}
				}
			}
		}
	}
	
	protected function getAttachmentFromChooser(\XFRM\Entity\ResourceVersion $version)
	{
		$attachments = $version->getRelationFinder('Attachments')->fetch();
		
		$file = $this->filter('file', 'uint');
		if ($attachments->count() == 1)
		{
			return $attachments->first();
		}
		else if ($file && isset($attachments[$file]))
		{
			return $attachments[$file];
		}
		
		return null;
	}
}