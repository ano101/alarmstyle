<?php

namespace App\Services;

class JsonLdService
{
    /** @var array<int,array<string,mixed>> */
    protected array $blocks = [];

    /**
     * Добавить один JSON-LD блок.
     */
    public function add(array $schema): static
    {
        $this->blocks[] = $schema;

        return $this;
    }

    /**
     * Полностью заменить набор блоков.
     *
     * @param  array<int,array<string,mixed>>  $schemas
     */
    public function set(array $schemas): static
    {
        $this->blocks = $schemas;

        return $this;
    }

    /**
     * Очистить все блоки.
     */
    public function clear(): static
    {
        $this->blocks = [];

        return $this;
    }

    /**
     * Массив схем как есть.
     */
    public function getArray(): array
    {
        return $this->blocks;
    }

    /**
     * Одна JSON-LD строка для `<script>`.
     *
     * Если блоков несколько — упакуем в @graph.
     */
    public function get(): ?string
    {
        if (empty($this->blocks)) {
            return null;
        }

        $data = $this->blocks;

        if (count($data) > 1) {
            $data = [
                '@context' => 'https://schema.org',
                '@graph' => $data,
            ];
        } else {
            $data = $data[0];
        }

        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
    }

    /**
     * Добавить JSON-LD разметку Organization (информация об организации).
     * Использует данные из settings (контакты, название компании и т.д.)
     */
    public function addOrganization(): static
    {
        $companyName = setting('company.name', config('app.name', 'AlarmStyle'));
        $companyTagline = setting('company.tagline', 'Противоугонные системы и автоэлектроника');
        $phone = setting('contacts.phone');
        $email = setting('contacts.email');
        $address = setting('contacts.address');

        $organization = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $companyName,
            'description' => $companyTagline,
            'url' => url('/'),
        ];

        // Добавляем телефон если есть
        if ($phone) {
            $organization['telephone'] = $phone;
        }

        // Добавляем email если есть
        if ($email) {
            $organization['email'] = $email;
        }

        // Добавляем адрес если есть
        if ($address) {
            $organization['address'] = [
                '@type' => 'PostalAddress',
                'addressLocality' => $address,
            ];
        }

        // Добавляем социальные сети если есть
        $sameAs = [];
        if ($whatsapp = setting('contacts.whatsapp')) {
            $sameAs[] = $whatsapp;
        }
        if ($telegram = setting('contacts.telegram')) {
            $sameAs[] = $telegram;
        }
        if (!empty($sameAs)) {
            $organization['sameAs'] = $sameAs;
        }

        $this->add($organization);

        return $this;
    }
}

