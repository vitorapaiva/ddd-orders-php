<?php

declare(strict_types=1);

namespace Orders\Tests;

use Orders\Domain\Entities\Order;
use Orders\Domain\ValueObjects\Address;
use Orders\Domain\ValueObjects\Item;
use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Ports\Outbound\EventPublisherInterface;
use Orders\Ports\Outbound\ProductsServiceInterface;

final class TestHelpers
{
    public static function validAddress(): Address
    {
        return Address::fromArray([
            'street_type' => 'Street',
            'street_name' => 'X',
            'number' => '1',
            'complement' => null,
            'district' => 'Y',
            'city' => 'Z',
            'state' => 'NY',
            'zip_code' => '01234-567',
        ]);
    }

    public static function validOrderData(): array
    {
        $address = [
            'street_type' => 'Street',
            'street_name' => 'X',
            'number' => '1',
            'complement' => null,
            'district' => 'Y',
            'city' => 'Z',
            'state' => 'NY',
            'zip_code' => '01234-567',
        ];
        return [
            'customer_id' => 'cust-1',
            'shipping_address' => $address,
            'billing_address' => $address,
            'items' => [['product_id' => 'p1', 'quantity' => 2, 'price' => 10.0]],
        ];
    }

    public static function validOrder(): Order
    {
        $addr = self::validAddress();
        return new Order(
            customerId: 'cust-1',
            shippingAddress: $addr,
            billingAddress: $addr,
            items: [new Item('p1', 2, 10.0)]
        );
    }

    public static function createInMemoryRepository(): OrderRepositoryInterface
    {
        return new class implements OrderRepositoryInterface {
            private array $store = [];

            public function save(Order $order): void
            {
                $this->store[$order->toDto()->id] = $order;
            }

            public function findById(string $id): ?Order
            {
                return $this->store[$id] ?? null;
            }

            public function update(Order $order): void
            {
                $this->store[$order->toDto()->id] = $order;
            }

            public function listAll(): array
            {
                return array_values($this->store);
            }
        };
    }

    public static function createMockProductsService(bool $success = true): ProductsServiceInterface
    {
        return new class($success) implements ProductsServiceInterface {
            public function __construct(private readonly bool $success) {}

            public function reserveProducts(string $orderId, array $items): array
            {
                return ['success' => $this->success, 'products' => [], 'error' => null];
            }

            public function releaseReservation(string $orderId): array
            {
                return ['success' => $this->success];
            }
        };
    }

    public static function createMockEventPublisher(): EventPublisherInterface
    {
        return new class implements EventPublisherInterface {
            public array $events = [];

            public function publish(object $event): void
            {
                $this->events[] = $event;
            }
        };
    }
}
