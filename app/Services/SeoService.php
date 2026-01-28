<?php

namespace App\Services;

class SeoService
{
    protected ?string $title = null;

    protected ?string $h1 = null;

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

    // ======== INTERNAL ========

    protected function blank($value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }

    // ======== SETTERS ========

    public function setMetaTitle(string $title): static
    {
        $this->title = $title;

        // По умолчанию прокидываем в OG/Twitter, если не заданы отдельно
        $this->ogTitle ??= $title;
        $this->twitterTitle ??= $title;

        return $this;
    }

    public function setMetaTitleIfEmpty(?string $title): static
    {
        if ($this->blank($this->title) && ! $this->blank($title)) {
            $this->setMetaTitle(trim((string) $title));
        }

        return $this;
    }

    public function setH1(?string $h1): static
    {
        $this->h1 = $h1;

        return $this;
    }

    public function setH1IfEmpty(?string $h1): static
    {
        if ($this->blank($this->h1) && ! $this->blank($h1)) {
            $this->setH1(trim((string) $h1));
        }

        return $this;
    }

    public function setMetaDescription(string $description): static
    {
        $this->description = $description;

        $this->ogDescription ??= $description;
        $this->twitterDescription ??= $description;

        return $this;
    }

    public function setMetaDescriptionIfEmpty(?string $description): static
    {
        if ($this->blank($this->description) && ! $this->blank($description)) {
            $this->setMetaDescription(trim((string) $description));
        }

        return $this;
    }

    public function setMetaKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function setMetaKeywordsIfEmpty(?string $keywords): static
    {
        if ($this->blank($this->keywords) && ! $this->blank($keywords)) {
            $this->setMetaKeywords(trim((string) $keywords));
        }

        return $this;
    }

    public function setCanonical(?string $url): static
    {
        $this->canonical = $url;

        return $this;
    }

    public function setCanonicalIfEmpty(?string $url): static
    {
        if ($this->blank($this->canonical) && ! $this->blank($url)) {
            $this->setCanonical(trim((string) $url));
        }

        return $this;
    }

    public function setNoIndex(bool $value = true): static
    {
        $this->noIndex = $value;

        return $this;
    }

    // ======== OpenGraph ========

    public function setOgTitle(?string $title): static
    {
        $this->ogTitle = $title;

        return $this;
    }

    public function setOgTitleIfEmpty(?string $title): static
    {
        if ($this->blank($this->ogTitle) && ! $this->blank($title)) {
            $this->setOgTitle(trim((string) $title));
        }

        return $this;
    }

    public function setOgDescription(?string $description): static
    {
        $this->ogDescription = $description;

        return $this;
    }

    public function setOgDescriptionIfEmpty(?string $description): static
    {
        if ($this->blank($this->ogDescription) && ! $this->blank($description)) {
            $this->setOgDescription(trim((string) $description));
        }

        return $this;
    }

    public function setOgImage(?string $imageUrl): static
    {
        $this->ogImage = $imageUrl;

        return $this;
    }

    public function setOgImageIfEmpty(?string $imageUrl): static
    {
        if ($this->blank($this->ogImage) && ! $this->blank($imageUrl)) {
            $this->setOgImage(trim((string) $imageUrl));
        }

        return $this;
    }

    public function setOgType(?string $type): static
    {
        $this->ogType = $type;

        return $this;
    }

    public function setOgTypeIfEmpty(?string $type): static
    {
        if ($this->blank($this->ogType) && ! $this->blank($type)) {
            $this->setOgType(trim((string) $type));
        }

        return $this;
    }

    // ======== Twitter ========

    public function setTwitterCard(?string $cardType): static
    {
        $this->twitterCard = $cardType;

        return $this;
    }

    public function setTwitterCardIfEmpty(?string $cardType): static
    {
        if ($this->blank($this->twitterCard) && ! $this->blank($cardType)) {
            $this->setTwitterCard(trim((string) $cardType));
        }

        return $this;
    }

    public function setTwitterTitle(?string $title): static
    {
        $this->twitterTitle = $title;

        return $this;
    }

    public function setTwitterTitleIfEmpty(?string $title): static
    {
        if ($this->blank($this->twitterTitle) && ! $this->blank($title)) {
            $this->setTwitterTitle(trim((string) $title));
        }

        return $this;
    }

    public function setTwitterDescription(?string $description): static
    {
        $this->twitterDescription = $description;

        return $this;
    }

    public function setTwitterDescriptionIfEmpty(?string $description): static
    {
        if ($this->blank($this->twitterDescription) && ! $this->blank($description)) {
            $this->setTwitterDescription(trim((string) $description));
        }

        return $this;
    }

    public function setTwitterImage(?string $imageUrl): static
    {
        $this->twitterImage = $imageUrl;

        return $this;
    }

    public function setTwitterImageIfEmpty(?string $imageUrl): static
    {
        if ($this->blank($this->twitterImage) && ! $this->blank($imageUrl)) {
            $this->setTwitterImage(trim((string) $imageUrl));
        }

        return $this;
    }

    // ======== GETTERS (для Blade / Inertia share) ========

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getH1(): ?string
    {
        return $this->h1;
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
