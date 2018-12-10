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

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SearchController extends Controller
{
    public function searchBar()
    {
        $form = $this->createFormBuilder(null)
                ->setAction($this->generateUrl('sylius_shop_product_search'))
                ->add('query', TextType::class, [
                    'label' => false,
                ])
                ->add('submit', SubmitType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'search-ico',
                    ],
                ])
            ->getForm()
        ;

        return $this->render('SyliusShopBundle:Header:_search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function handleSearch(Request $request, $_query)
    {
        $productRepo = $this->container->get('sylius.repository.product');
        $taxonRepo = $this->container->get('sylius.repository.product');
        $tagRepo = $this->container->get('sylius.repository.product');
        $manufacturerRepo = $this->container->get('sylius.repository.product');

        if ($_query) {
            $data = $repo->findByFulltext($_query);
        } else {
            $data = $repo->findAll();
        }

        // iterate over all the resuls and 'inject' the image inside
        for ($index = 0; $index < count($data); ++$index) {
            $object = $data[$index];
            $object->setImage('http://via.placeholder.com/35/0000FF/ffffff');
        }

        // setting up the serializer
        $normalizers = [
            new ObjectNormalizer(),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);
        $data = $serializer->serialize($data, 'json');

        return new JsonResponse($data, 200, [], true);
    }
}
