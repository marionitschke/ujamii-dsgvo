<?php
declare( strict_types=1 );

namespace Ujamii\UjamiiDsgvo\Command;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Ujamii\UjamiiDsgvo\Service\DbOperationsService;

/**
 * Class CleanupCommandController
 * @package Ujamii\UjamiiDsgvo\Command
 */
class CleanupCommandController extends CommandController {

	/**
	 * @var \Ujamii\UjamiiDsgvo\Service\DbOperationsService
	 */
	protected $service;

	/**
	 * Cleans up old and deleted records in the database to comply with the DSGVO rules in Germany, which are
	 * based on the privacy shield regulations valid in the whole EU.
	 *
	 * @param int $pageUid
	 *
	 * @throws \Exception
	 */
	public function cleanDatabaseCommand($pageUid = 1) {
		$tsConfig = BackendUtility::getPagesTSconfig( $pageUid );
		if ( isset( $tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.'] ) && is_array( $tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.'] ) ) {
			$tsConfig = GeneralUtility::removeDotsFromTS( $tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.'] );

			$this->service = $this->objectManager->get( 'Ujamii\UjamiiDsgvo\Service\DbOperationsService' );
			$this->service->setTsConfiguration( $tsConfig );

			$result = $this->service->getDbCheckResult( DbOperationsService::MODE_DELETE );
			$this->outputLine((new \DateTime())->format('Y-m-d H:i:s'));
			$this->outputLine(DebuggerUtility::var_dump($result, 'FALSE means Extension not installed, integer is amount of deleted records.', 8, true, true, true));
		} else {
			throw new \Exception('TS could not be loaded!');
		}
	}

}
