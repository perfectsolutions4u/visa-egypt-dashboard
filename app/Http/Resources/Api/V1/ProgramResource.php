<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Tour;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProgramResource extends JsonResource
{
    public function toArray($request): array
    {
        if ($this->resource instanceof Tour) {
            return $this->fromTour($request);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'duration' => $this->duration,
            'cities' => $this->cities,
            'highlights' => $this->highlights,
            'itinerary' => $this->itinerary,
            'inclusions' => $this->inclusions,
            'exclusions' => $this->exclusions,
            'starting_price' => $this->starting_price,
            'hero_image' => $this->hero_image,
            'gallery' => is_array($this->gallery ?? null) ? array_values($this->gallery) : [],
            'options' => is_array($this->options ?? null) ? array_values($this->options) : [],
            'is_best_seller' => $this->is_best_seller,
            'is_favorited' => (bool) $this->is_favorited,
        ];
    }

    protected function fromTour($request): array
    {
        /** @var Tour $tour */
        $tour = $this->resource;

        $duration = $tour->duration;
        $dayCount = $tour->relationLoaded('days') ? $tour->days->count() : 0;
        if ($dayCount > 0) {
            $duration = $dayCount === 1 ? '1 Day' : $dayCount.' Days';
        } elseif (! $duration && $tour->duration_in_days) {
            $duration = $tour->duration_in_days === 1
                ? '1 Day'
                : $tour->duration_in_days.' Days';
        }

        return [
            'id' => $tour->id,
            'name' => $tour->title,
            'slug' => $tour->slug ?: Str::slug($tour->title).'-'.$tour->id,
            'duration' => $duration,
            'cities' => $tour->relationLoaded('destinations')
                ? $tour->destinations->pluck('title')->filter()->values()->all()
                : [],
            'highlights' => self::htmlToList($tour->highlights),
            'itinerary' => $this->mapItinerary($tour),
            'inclusions' => self::htmlToList($tour->included),
            'exclusions' => self::htmlToList($tour->excluded),
            'starting_price' => $tour->start_from,
            'hero_image' => ClientResource::publicImageUrl($tour->featured_image, $request),
            'gallery' => $this->mapGallery($tour->gallery, $request),
            'options' => $this->mapOptions($tour),
            'is_best_seller' => (bool) $tour->featured,
            'is_favorited' => (bool) $tour->getAttribute('is_favorited'),
        ];
    }

    /**
     * @param  mixed  $gallery
     * @return list<string>
     */
    protected function mapGallery($gallery, $request): array
    {
        if (! is_array($gallery) || $gallery === []) {
            return [];
        }

        return Collection::make($gallery)
            ->map(function ($item) use ($request) {
                if (is_array($item)) {
                    $item = $item['url'] ?? $item['path'] ?? $item['src'] ?? null;
                }

                return ClientResource::publicImageUrl(
                    is_string($item) ? $item : null,
                    $request
                );
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function mapOptions(Tour $tour): array
    {
        if (! $tour->relationLoaded('options')) {
            return [];
        }

        return $tour->options
            ->filter(fn ($option) => filled($option->name))
            ->values()
            ->map(function ($option) {
                $description = trim(html_entity_decode(strip_tags((string) $option->description)));
                if (in_array($description, ['', '.', '-'], true)) {
                    $description = null;
                }

                $groups = $option->pricing_groups;
                if ($groups instanceof Collection) {
                    $groups = $groups->values()->all();
                } elseif (! is_array($groups)) {
                    $groups = [];
                }

                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'description' => $description,
                    'adult_price' => (float) $option->adult_price,
                    'child_price' => (float) $option->child_price,
                    'pricing_groups' => array_values($groups),
                ];
            })
            ->all();
    }

    protected function mapItinerary(Tour $tour): array
    {
        if (! $tour->relationLoaded('days')) {
            return [];
        }

        return $tour->days->values()->map(function ($day, int $index) {
            return [
                'day' => $index + 1,
                'title' => $day->title ?: ('Day '.($index + 1)),
                'description' => $day->description
                    ? trim(html_entity_decode(strip_tags($day->description)))
                    : null,
            ];
        })->all();
    }

    public static function htmlToList(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $value, $matches)) {
            return Collection::make($matches[1])
                ->map(fn ($item) => trim(html_entity_decode(strip_tags($item))))
                ->filter()
                ->values()
                ->all();
        }

        $normalized = Str::of($value)
            ->replace(['<br>', '<br/>', '<br />', '</p>', '</div>'], "\n")
            ->stripTags()
            ->toString();

        $normalized = html_entity_decode($normalized);

        return Collection::make(preg_split('/\r\n|\r|\n|•|●/', $normalized) ?: [])
            ->map(fn ($item) => trim((string) $item, " \t\n\r\0\x0B-–—"))
            ->filter()
            ->values()
            ->all();
    }
}
