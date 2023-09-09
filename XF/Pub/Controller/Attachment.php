<?php

namespace DC\AdvancedDownload\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Attachment extends XFCP_Attachment
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
		
		if ($action == 'Index' && !$bypass)
		{
			/** @var \XF\Entity\Attachment $attachment */
			$attachment = $this->em()->find('XF:Attachment', $params->attachment_id);
			if ($attachment && $attachment->canView())
			{
				/** Get option values */
				$options = \XF::options();
				
				$fileExtensions = $options->DC_AdvancedDownload_fileEx;
				$fileExSplited = preg_split('/\s+/', $fileExtensions, -1, PREG_SPLIT_NO_EMPTY);
				
				if (!in_array($attachment->extension, $fileExSplited))
				{
					/** @var \DC\AdvancedDownload\Repository\Download $downloadRepo */
					$downloadRepo = $this->repository('DC\AdvancedDownload:Download');
					$postUrl = $downloadRepo->getAttachmentContainerUrl($attachment);
					
					$session1 = $_GET['sess'] ?? 1;
					$session2 = $_POST['sess'] ?? 2;
					
					$fileSizeData = $downloadRepo->getFileSizeData($attachment->file_size);
					$fileSize = $fileSizeData['file_size'];
					$fileSizeEx = $fileSizeData['file_size_ex'];
					
					if ($session1 != $session2)
					{
						$viewParams = [
							'sess'          => md5('DC_AdvancedDownload_DownloadInternal' . \XF::$time),
							'attachment'    => $attachment,
							'fileSize'      => "$fileSize $fileSizeEx",
							'postUrl'       => $postUrl,
						];
						
						throw $this->exception($this->view('XF:Attachment\Index', 'DC_AdvancedDownload_DownloadInternal', $viewParams));
					}
				}
			}
		}
	}
}