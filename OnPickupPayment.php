<?php

declare(strict_types=1);

namespace OnPickupPayment;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Model\Order;
use Thelia\Model\OrderStatus;
use Thelia\Model\OrderStatusQuery;
use Thelia\Module\AbstractPaymentModule;

class OnPickupPayment extends AbstractPaymentModule
{
    public const string DOMAIN_NAME = 'onpickuppayment';
    public const string MESSAGE_DOMAIN = 'anselmi';
    public const string ORDER_STATUS_ON_PICKUP_PAYMENT_PAID = 'on_pickup_payment_paid';

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR.ucfirst(self::getModuleCode()).'/I18n/*'])
            ->autowire(true)
            ->autoconfigure(true);
    }

    public function pay(Order $order): bool
    {
        return $this->getCurrentOrderTotalAmount() > 0;
    }

    public function isValidPayment(): bool
    {
        return $this->getCurrentOrderTotalAmount() > 0;
    }

    public function postActivation(ConnectionInterface $con = null): void
    {
        $this->createSpecialStatut();
    }

    public function manageStockOnCreation(): bool
    {
        return false;
    }

    private function createSpecialStatut(): void
    {
        if (OrderStatusQuery::create()->filterByCode(self::ORDER_STATUS_ON_PICKUP_PAYMENT_PAID)->findOne()) {
            return;
        }

        $lastPosition = OrderStatusQuery::create()
            ->orderByPosition(Criteria::DESC)
            ->findOne()
            ?->getPosition() ?? 0;

        $orderStatus = new OrderStatus();
        $orderStatus
            ->setLocale('fr_FR')
            ->setCode(self::ORDER_STATUS_ON_PICKUP_PAYMENT_PAID)
            ->setTitle('Paiement sur place lors du retrait')
            ->setColor('#14b8a6')
            ->setPosition($lastPosition + 1)
            ->setProtectedStatus(1)
            ->save();
    }
}
