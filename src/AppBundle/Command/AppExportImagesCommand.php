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

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class AppExportImagesCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    public const SOURCE_PATH = '/web/media/image/';

    /**
     * @var string
     */
    public const EXPORT_PATH = '/etc/export/';

    protected function configure()
    {
        $this
            ->setName('app:export-images')
            ->setDescription('Export images to ./etc folder. Each folder = product based on product code.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $prodRepo = $this->getContainer()->get('sylius.repository.product');
        $prods = $prodRepo->findAll();
        $prodsCount = count($prods);
        $projectDir = $this->getContainer()->getParameter('kernel.project_dir');

        $io->note('Found ' . $prodsCount . ' products in database.');
        $io->newLine();

        $prodsWithImages = [];
        $prodsWithoutImages = [];
        foreach ($prods as $prod) {
            if ($prod->getImages()->isEmpty()) {
                $prodsWithoutImages[] = $prod;
            } else {
                $prodsWithImages[] = $prod;
            }
        }

        $io->note('Found ' . count($prodsWithImages) . ' products with at least one image.');
        $io->newLine();
        $io->note('Starting export...');

        $io->progressStart(count($prodsWithImages));
        foreach ($prodsWithImages as $prod) {
            $this->createProductDir($prod->getCode());

            $images = $prod->getImages();
            foreach ($images as $image) {
                $this->copyAndRenameImage(
                    $projectDir . self::SOURCE_PATH . $image->getPath(),
                    $projectDir . self::EXPORT_PATH . $prod->getCode() . '/',
                    $prod->getCode(),
                    $image->getType());
            }

            $io->progressAdvance();
        }
        $io->progressFinish();
        $io->newLine();

        $io->success('All images were successfully exported. Check etc/export folder.');
    }

    /**
     * @param  string $productCode
     */
    private function createProductDir(string $productCode): void
    {
        $projectDir = $this->getContainer()->getParameter('kernel.project_dir');
        $fileSystem = $this->getContainer()->get('filesystem');
        $fileSystem->mkdir($projectDir . self::EXPORT_PATH, 0700);
        $fileSystem->mkdir($projectDir . self::EXPORT_PATH . $productCode . '/', 0700);
    }

    /**
     * @param  string $sourcePath
     * @param  string $targetPath
     * @param  string $prodCode
     * @param  string $type
     */
    private function copyAndRenameImage(string $sourcePath, string $targetPath, string $prodCode, string $type): void
    {
        $fileSystem = $this->getContainer()->get('filesystem');

        if ($type === 'main') {
            $mainImageTarget = $targetPath . $prodCode . '.' . pathinfo($sourcePath)['extension'];
            $fileSystem->copy($sourcePath, $mainImageTarget, true);
        } else {
            $fileSystem->copy($sourcePath, $targetPath . ('thumbnail-' . basename($sourcePath)), true);
        }
    }
}
