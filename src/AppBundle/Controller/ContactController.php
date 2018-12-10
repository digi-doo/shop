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

use AppBundle\Form\Type\FooterContactType;
use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class ContactController extends FOSRestController
{
    /**
     * @var ContactEmailManagerInterface
     */
    private $contactEmailManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param ContactEmailManagerInterface $contactEmailManager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EngineInterface $templatingEngine,
        ChannelContextInterface $channelContext,
        ContactEmailManagerInterface $contactEmailManager
    ) {
        $this->contactEmailManager = $contactEmailManager;
        $this->formFactory = $formFactory;
        $this->templatingEngine = $templatingEngine;
        $this->channelContext = $channelContext;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function requestAction(Request $request): Response
    {
        $formType = $this->getSyliusAttribute($request, 'form', FooterContactType::class);
        $form = $this->formFactory->create($formType);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $form->getData();
            $validator = $this->get('app.validator.contact_form');
            $errors = $validator->validate($data);

            if (count($errors) > 0) {
                $request->getSession()->getFlashBag()->add('error', 'procamping.footer_contact.invalid_formats');

                return new RedirectResponse($request->headers->get('referer'));
            }

            $channel = $this->channelContext->getChannel();
            $contactEmails = $channel->getNotificationAdminOrderEmails();

            if (!$contactEmails) {
                $errorMessage = $this->getSyliusAttribute(
                    $request,
                    'error_flash',
                    'sylius.contact.request_error'
                );
                $request->getSession()->getFlashBag()->add('error', $errorMessage);

                return new RedirectResponse($request->headers->get('referer'));
            }

            // Send email to order admins
            $data['requestUrl'] = $request->headers->get('referer');
            $this->contactEmailManager->sendContactRequest($data, $contactEmails);

            $successMessage = $this->getSyliusAttribute(
                $request,
                'success_flash',
                'sylius.contact.request_success'
            );
            $request->getSession()->getFlashBag()->add('success', $successMessage);

            $redirectRoute = $this->getSyliusAttribute($request, 'redirect', 'referer');

            return new RedirectResponse($request->headers->get('referer'));
        }

        $template = $this->getSyliusAttribute($request, 'template', '@SyliusShop/Contact/footerForm.html.twig');

        return $this->templatingEngine->renderResponse($template, ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param string $attributeName
     * @param string|null $default
     *
     * @return string|null
     */
    private function getSyliusAttribute(Request $request, string $attributeName, ?string $default): ?string
    {
        $attributes = $request->attributes->get('_sylius');

        return $attributes[$attributeName] ?? $default;
    }

    /**
     * @return array
     */
    private function getFormOptions(): array
    {
        return [];
    }
}
