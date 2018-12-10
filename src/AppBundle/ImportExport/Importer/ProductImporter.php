<?php

/*
 * This file is part of the Digi Doo s.r.o. sshop project.
 *
 * (c) Digi Doo s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\ImportExport\Importer;

use AppBundle\ImportExport\Processor\ProductProcessor;
use Doctrine\Common\Persistence\ObjectManager;
use FriendsOfSylius\SyliusImportExportPlugin\Exception\ImporterException;
use FriendsOfSylius\SyliusImportExportPlugin\Exception\ItemIncompleteException;
use FriendsOfSylius\SyliusImportExportPlugin\Importer\ImporterInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Importer\ImporterResultInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Processor\ResourceProcessorInterface;
use Port\Excel\ExcelReaderFactory;
use Port\Reader\ReaderFactory;

/**
 * Main product importer
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class ProductImporter implements ImporterInterface
{
    /** @var ReaderFactory */
    private $readerFactory;

    /** @var ObjectManager */
    private $objectManager;

    /** @var ResourceProcessorInterface */
    private $resourceProcessor;

    /** @var ImporterResultInterface */
    private $result;

    /** @var int */
    private $batchSize;

    /** @var bool */
    private $failOnIncomplete;

    /** @var bool */
    private $stopOnFailure;

    public function __construct(
        ExcelReaderFactory $readerFactory,
        ObjectManager $objectManager,
        ProductProcessor $resourceProcessor,
        ImporterResultInterface $importerResult,
        int $batchSize,
        bool $failOnIncomplete,
        bool $stopOnFailure
    ) {
        $this->readerFactory = $readerFactory;
        $this->objectManager = $objectManager;
        $this->resourceProcessor = $resourceProcessor;
        $this->result = $importerResult;
        $this->batchSize = $batchSize;
        $this->failOnIncomplete = $failOnIncomplete;
        $this->stopOnFailure = $stopOnFailure;
    }

    /**
     * @param string $fileName
     *
     * @return ImporterResultInterface
     */
    public function import(string $fileName): ImporterResultInterface
    {
        $reader = $this->readerFactory->getReader(new \SplFileObject($fileName));
        $reader->setHeaderRowNumber(0);

        $this->result->start();

        $this->objectManager->getConnection()->beginTransaction();

        $batchCount = 0;
        foreach ($reader as $i => $row) {
            try {
                $this->resourceProcessor->process($row, ($i + 1));
                $this->result->success($i);

                ++$batchCount;
                if ($this->batchSize && $batchCount === $this->batchSize) {
                    $this->objectManager->flush();
                    $batchCount = 0;
                }
            } catch (ItemIncompleteException $e) {
                // Incomplete
                if ($this->failOnIncomplete) {
                    $this->result->failed($i);
                    if ($this->stopOnFailure) {
                        $this->objectManager->getConnection()->rollBack();

                        break;
                    }
                } else {
                    $this->result->skipped($i);
                }
            } catch (ImporterException $e) {
                // Failed
                $this->result->failed($i);
                if ($this->stopOnFailure) {
                    $this->objectManager->getConnection()->rollBack();

                    break;
                }
            }
        }

        if ($batchCount) {
            $this->objectManager->flush();
        }

        $this->result->stop();

        $this->objectManager->getConnection()->commit();

        return $this->result;
    }
}
