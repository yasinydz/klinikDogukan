<?php
/**
 * app/helpers/paginator.php
 *
 * Sayfalama hesaplama ve HTML üretimi.
 */

class Paginator
{
    public int $currentPage;
    public int $totalItems;
    public int $perPage;
    public int $totalPages;
    public int $offset;

    public function __construct(int $totalItems, int $perPage = 6, int $currentPage = 1)
    {
        $this->totalItems  = max(0, $totalItems);
        $this->perPage     = max(1, $perPage);
        $this->currentPage = max(1, $currentPage);
        $this->totalPages  = (int) ceil($this->totalItems / $this->perPage);
        $this->offset      = ($this->currentPage - 1) * $this->perPage;
    }

    public function hasPages(): bool
    {
        return $this->totalPages > 1;
    }

    public function hasPrevious(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function previousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function nextPage(): int
    {
        return min($this->totalPages, $this->currentPage + 1);
    }

    /**
     * Sayfa numaralarını döndürür (pencere: ±2).
     *
     * @return int[]
     */
    public function pageRange(): array
    {
        $start = max(1, $this->currentPage - 2);
        $end   = min($this->totalPages, $this->currentPage + 2);

        return range($start, $end);
    }

    /**
     * Sayfalama HTML'i üretir.
     *
     * @param string $baseUrl Temel URL (ör: '/blog')
     * @param string $param   Sayfa parametre adı (varsayılan: 'sayfa')
     */
    public function render(string $baseUrl, string $param = 'sayfa'): string
    {
        if (!$this->hasPages()) {
            return '';
        }

        $html  = '<nav class="pagination container" aria-label="Sayfa navigasyonu">';

        if ($this->hasPrevious()) {
            $url   = $baseUrl . '?' . $param . '=' . $this->previousPage();
            $html .= '<a href="' . e($url) . '" class="btn sm" aria-label="Önceki sayfa">‹ Önceki</a>';
        }

        foreach ($this->pageRange() as $page) {
            $url      = $baseUrl . '?' . $param . '=' . $page;
            $active   = $page === $this->currentPage ? ' active' : '';
            $current  = $page === $this->currentPage ? ' aria-current="page"' : '';
            $html    .= '<a href="' . e($url) . '" class="btn sm' . $active . '"' . $current . '>' . $page . '</a>';
        }

        if ($this->hasNext()) {
            $url   = $baseUrl . '?' . $param . '=' . $this->nextPage();
            $html .= '<a href="' . e($url) . '" class="btn sm" aria-label="Sonraki sayfa">Sonraki ›</a>';
        }

        $html .= '</nav>';

        return $html;
    }
}
