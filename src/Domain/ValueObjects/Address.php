<?php

declare(strict_types=1);

namespace Orders\Domain\ValueObjects;

use Orders\Domain\Validation\ValidationHelper;

final class Address
{
    public function __construct(
        private readonly string $streetType,
        private readonly string $streetName,
        private readonly string $number,
        private readonly ?string $complement,
        private readonly string $district,
        private readonly string $city,
        private readonly string $state,
        private readonly string $zipCode
    ) {}

    public function getStreetType(): string
    {
        return $this->streetType;
    }

    public function getStreetName(): string
    {
        return $this->streetName;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function toArray(): array
    {
        return [
            'street_type' => $this->streetType,
            'street_name' => $this->streetName,
            'number' => $this->number,
            'complement' => $this->complement,
            'district' => $this->district,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
        ];
    }

    public static function fromArray(array $data): self
    {
        ValidationHelper::requireKeys($data, ['street_type', 'street_name', 'number', 'district', 'city', 'state', 'zip_code'], 'Address');
        foreach (['street_type', 'street_name', 'number', 'district', 'city', 'state', 'zip_code'] as $key) {
            ValidationHelper::requireNonEmpty($data, $key, "Address field '{$key}' is required");
        }

        if (strlen($data['state']) !== 2) {
            throw new \InvalidArgumentException("Address state must be 2 characters (e.g. NY)");
        }

        return new self(
            streetType: (string) $data['street_type'],
            streetName: (string) $data['street_name'],
            number: (string) $data['number'],
            complement: isset($data['complement']) ? (string) $data['complement'] : null,
            district: (string) $data['district'],
            city: (string) $data['city'],
            state: (string) $data['state'],
            zipCode: (string) $data['zip_code']
        );
    }
}
