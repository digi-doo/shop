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

namespace AppBundle\Context;

use AppBundle\Exception\TaxonNotFoundException;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class TaxonContext
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param RequestStack $requestStack
     * @param TaxonRepositoryInterface $taxonRepository
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        RequestStack $requestStack,
        TaxonRepositoryInterface $taxonRepository,
        LocaleContextInterface $localeContext
    ) {
        $this->requestStack = $requestStack;
        $this->taxonRepository = $taxonRepository;
        $this->localeContext = $localeContext;
    }

    public function getTaxon(): ?TaxonInterface
    {
        $slug = $this->requestStack->getCurrentRequest()->get('slug');
        $localeCode = $this->localeContext->getLocaleCode();
        $taxon = $this->taxonRepository->findOneBySlug($slug, $localeCode);

        if (null === $slug || null === $taxon) {
            throw new TaxonNotFoundException();
        }

        return $taxon->isEnabled() && $taxon->isFilterEnabled() ? $taxon : null;
    }
}
