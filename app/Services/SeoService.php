<?php

namespace App\Services;

class SeoService
{
    protected ?string $title = null;
    protected ?string $description = null;
    protected ?string $keywords = null;
    protected ?string $canonical = null;
    protected bool $noIndex = false;

    // OpenGraph
    protected ?string $ogTitle = null;
    protected ?string $ogDescription = null;
    protected ?string $ogImage = null;
    protected ?string $ogType = 'website';

    // Twitter
    protected ?string $twitterCard = 'summary_large_image';
    protected ?string $twitterTitle = null;
    protected ?string $twitterDescription = null;
    protected ?string $twitterImage = null;

    // ======== SETTERS ========

    public function setMetaTitle(string $title): static
    {
        $this->title = $title;

        // По умолчанию прокидываем в OG/Twitter, если не заданы отдельно
        $this->ogTitle ??= $title;
        $this->twitterTitle ??= $title;

        return $this;
    }

    public function setMetaDescription(string $description): static
    {
        $this->description = $description;

        $this->ogDescription ??= $description;
        $this->twitterDescription ??= $description;

        return $this;
    }

    public function setMetaKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function setCanonical(?string $url): static
    {
        $this->canonical = $url;
        return $this;
    }

    public function setNoIndex(bool $value = true): static
    {
        $this->noIndex = $value;
        return $this;
    }

    // OpenGraph

    public function setOgTitle(?string $title): static
    {
        $this->ogTitle = $title;
        return $this;
    }

    public function setOgDescription(?string $description): static
    {
        $this->ogDescription = $description;
        return $this;
    }

    public function setOgImage(?string $imageUrl): static
    {
        $this->ogImage = $imageUrl;
        return $this;
    }

    public function setOgType(?string $type): static
    {
        $this->ogType = $type;
        return $this;
    }

    // Twitter

    public function setTwitterCard(?string $cardType): static
    {
        $this->twitterCard = $cardType;
        return $this;
    }

    public function setTwitterTitle(?string $title): static
    {
        $this->twitterTitle = $title;
        return $this;
    }

    public function setTwitterDescription(?string $description): static
    {
        $this->twitterDescription = $description;
        return $this;
    }

    public function setTwitterImage(?string $imageUrl): static
    {
        $this->twitterImage = $imageUrl;
        return $this;
    }

    // ======== GETTERS (для Blade) ========

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function getCanonical(): ?string
    {
        return $this->canonical;
    }

    public function isNoIndex(): bool
    {
        return $this->noIndex;
    }

    public function getOgTitle(): ?string
    {
        return $this->ogTitle;
    }

    public function getOgDescription(): ?string
    {
        return $this->ogDescription;
    }

    public function getOgImage(): ?string
    {
        return $this->ogImage;
    }

    public function getOgType(): ?string
    {
        return $this->ogType;
    }

    public function getTwitterCard(): ?string
    {
        return $this->twitterCard;
    }

    public function getTwitterTitle(): ?string
    {
        return $this->twitterTitle;
    }

    public function getTwitterDescription(): ?string
    {
        return $this->twitterDescription;
    }

    public function getTwitterImage(): ?string
    {
        return $this->twitterImage;
    }
}
