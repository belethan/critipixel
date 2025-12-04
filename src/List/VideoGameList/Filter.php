<?php

declare(strict_types=1);

namespace App\List\VideoGameList;

final class Filter
{
    /** @var array<int, string> */
    private array $tags = [];

    public function __construct(
        private ?string $search = null,
    ) {
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array<int, string> $tags
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}
