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

namespace AppBundle\Menu;

use AppBundle\Component\Order\AppOrderTransitions;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Order\OrderTransitions;

/**
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
final class AdminOrderMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminOrderMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $order = $event->getOrder();
        $stateMachine = $event->getStateMachine();

        $menu->removeChild('order_history');
        $menu->removeChild('cancel');

        $menu
            ->addChild('order_internal_notes', [
                'route' => 'app_admin_order_internal_notes',
                'routeParameters' => ['id' => $order->getId()],
            ])
            ->setAttribute('type', 'link')
            ->setAttribute('second_line', true)
            ->setExtra('internal_notes', !$order->getInternalNotes()->isEmpty() ? $order->getInternalNotes()->count() : null)
            ->setLabel('sylius.ui.order_notes')
            ->setLabelAttribute('icon', 'edit')
        ;

        if ($stateMachine->can(AppOrderTransitions::TRANSITION_PROCESS)) {
            $menu
                ->addChild('process', [])
                ->setAttribute('type', 'transition_links')
                ->setAttribute('links', [
                    [
                        'route' => 'app_admin_order_process',
                        'routeParameters' => ['id' => $order->getId(), 'withEmail' => true],
                        'icon' => 'bell outline',
                        'label' => 'sylius.ui.send_notification',
                    ],
                    [
                        'route' => 'app_admin_order_process',
                        'routeParameters' => ['id' => $order->getId(), 'withEmail' => false],
                        'icon' => 'bell slash outline',
                        'label' => 'sylius.ui.dont_send_notification',
                    ],
                ])
                ->setAttribute('second_line', false)
                ->setAttribute('confirmation', $order->hasUnapprovedInternalNotes())
                ->setAttribute('confirmationMessage', 'sylius.ui.there_is_unapproved_note')
                ->setLabel('sylius.ui.process')
                ->setLabelAttribute('icon', 'retweet')
                ->setLabelAttribute('color', 'purple')
            ;
        }

        if ($stateMachine->can(AppOrderTransitions::TRANSITION_EXPEDITE)) {
            $menu
                ->addChild('expedite', [
                    'route' => 'app_admin_order_expedite',
                    'routeParameters' => ['id' => $order->getId()],
                ])
                ->setAttribute('type', 'transition')
                ->setAttribute('second_line', false)
                ->setAttribute('confirmation', $order->hasUnapprovedInternalNotes())
                ->setAttribute('confirmationMessage', 'sylius.ui.there_is_unapproved_note')
                ->setLabel('sylius.ui.expedite')
                ->setLabelAttribute('icon', 'exchange')
                ->setLabelAttribute('color', 'teal')
            ;
        }

        if ($stateMachine->can(AppOrderTransitions::TRANSITION_ISSUE)) {
            $menu
                ->addChild('issue', [
                    'route' => 'app_admin_order_issue',
                    'routeParameters' => ['id' => $order->getId()],
                ])
                ->setAttribute('type', 'transition')
                ->setAttribute('second_line', false)
                ->setAttribute('confirmation', $order->hasUnapprovedInternalNotes())
                ->setAttribute('confirmationMessage', 'sylius.ui.there_is_unapproved_note')
                ->setLabel('sylius.ui.issue')
                ->setLabelAttribute('icon', 'external alternate')
                ->setLabelAttribute('color', 'brown')
            ;
        }

        if ($stateMachine->can(OrderTransitions::TRANSITION_CANCEL)) {
            $menu
                ->addChild('cancel', [])
                ->setAttribute('type', 'transition_links')
                ->setAttribute('links', [
                    [
                        'route' => 'app_admin_order_storno',
                        'routeParameters' => ['id' => $order->getId(), 'withEmail' => true],
                        'icon' => 'bell outline',
                        'label' => 'sylius.ui.send_notification',
                    ],
                    [
                        'route' => 'app_admin_order_storno',
                        'routeParameters' => ['id' => $order->getId(), 'withEmail' => false],
                        'icon' => 'bell slash outline',
                        'label' => 'sylius.ui.dont_send_notification',
                    ],
                ])
                ->setAttribute('confirmation', true)
                ->setAttribute('confirmationMessage', $order->hasUnapprovedInternalNotes() ? 'sylius.ui.there_is_unapproved_note' : 'sylius.ui.order_storno_irreversible')
                ->setLabel('sylius.ui.cancel')
                ->setLabelAttribute('icon', 'ban')
                ->setLabelAttribute('color', 'red')
            ;
        }
    }
}
