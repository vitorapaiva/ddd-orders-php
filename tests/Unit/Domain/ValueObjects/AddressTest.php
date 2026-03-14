<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Domain\ValueObjects;

use Orders\Domain\ValueObjects\Address;
use Orders\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testFromArrayCreatesAddress(): void
    {
        $data = TestHelpers::validOrderData()['shipping_address'];
        $address = Address::fromArray($data);

        $this->assertSame('Rua', $address->getStreetType());
        $this->assertSame('X', $address->getStreetName());
        $this->assertSame('1', $address->getNumber());
        $this->assertSame('Y', $address->getDistrict());
        $this->assertSame('Z', $address->getCity());
        $this->assertSame('SP', $address->getState());
        $this->assertSame('01234-567', $address->getZipCode());
    }

    public function testFromArrayWithComplement(): void
    {
        $data = TestHelpers::validOrderData()['shipping_address'];
        $data['complement'] = 'Apto 1';
        $address = Address::fromArray($data);

        $this->assertSame('Apto 1', $address->getComplement());
    }

    public function testFromArrayThrowsWhenRequiredFieldMissing(): void
    {
        $data = TestHelpers::validOrderData()['shipping_address'];
        unset($data['city']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Address missing required field: city");

        Address::fromArray($data);
    }

    public function testFromArrayThrowsWhenStateNotTwoChars(): void
    {
        $data = TestHelpers::validOrderData()['shipping_address'];
        $data['state'] = 'SPP';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Address state must be 2 characters');

        Address::fromArray($data);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $address = TestHelpers::validAddress();
        $arr = $address->toArray();

        $this->assertArrayHasKey('street_type', $arr);
        $this->assertArrayHasKey('zip_code', $arr);
        $this->assertSame('SP', $arr['state']);
    }
}
