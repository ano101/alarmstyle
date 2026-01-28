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
}
