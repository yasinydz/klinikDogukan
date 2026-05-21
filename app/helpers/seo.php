<?php
/**
 * app/helpers/seo.php
 *
 * JSON-LD schema üretimi ve SEO meta yardımcıları.
 * config/seo.php'nin yerini alır.
 */

if (!defined('SITE_URL')) {
    require_once dirname(__DIR__, 2) . '/config/app.php';
}

// ── Yardımcı Fonksiyonlar ─────────────────────────────────────

/**
 * Schema dizisini JSON-LD script tag'i olarak basar.
 */
function renderSchema(array $schema): void
{
    echo '<script type="application/ld+json">';
    echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo '</script>' . PHP_EOL;
}

/**
 * Görsel URL'si oluşturur.
 */
function buildImageUrl(?string $path, string $default = ''): string
{
    $path = trim((string) $path);

    if ($path === '') {
        if ($default !== '') return $default;
        // Public profile avatar veya site default
        if (function_exists('publicAvatarUrl')) {
            return publicAvatarUrl();
        }
        return SITE_URL . '/images/dogukan.png';
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    // Upload edilmiş görseller (timestamp prefix) uploads/ altında
    if (preg_match('/^\d{10,}_/', $path)) {
        return SITE_URL . '/images/uploads/' . ltrim($path, '/');
    }

    return SITE_URL . '/images/' . ltrim($path, '/');
}

/**
 * Metin temizler (schema için).
 */
function schemaText(?string $value): string
{
    return trim(strip_tags((string) $value));
}

/**
 * Tarihi ISO 8601 formatına çevirir.
 */
function schemaDate(?string $value): ?string
{
    if (!$value || trim($value) === '') {
        return null;
    }

    try {
        return (new DateTime($value))->format(DateTime::ATOM);
    } catch (Exception) {
        return null;
    }
}

// ── Schema Üreticiler ─────────────────────────────────────────

/**
 * WebSite schema — her sayfada.
 */
function schemaWebSite(): array
{
    return [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => SITE_NAME,
        'url'      => SITE_URL,
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => SITE_URL . '/arama?search={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];
}

/**
 * Person schema — hakkımda ve global.
 */
function schemaPerson(): array
{
    $sameAs = array_filter([
        SOCIAL_INSTAGRAM,
        SOCIAL_FACEBOOK,
        SOCIAL_LINKEDIN,
        GBP_PROFILE_URL,
    ]);

    return [
        '@context' => 'https://schema.org',
        '@type'    => 'Person',
        'name'     => PSYCHOLOGIST_NAME,
        'jobTitle' => PSYCHOLOGIST_TITLE,
        'url'      => SITE_URL . '/hakkimda',
        'image'    => function_exists('publicAvatarUrl') ? publicAvatarUrl() : SITE_URL . '/images/dogukan.png',
        'telephone' => CONTACT_PHONE,
        'email'     => CONTACT_EMAIL,
        'address'  => [
            '@type'           => 'PostalAddress',
            'addressLocality' => ADDRESS_DISTRICT,
            'addressRegion'   => ADDRESS_CITY,
            'addressCountry'  => ADDRESS_COUNTRY,
            'postalCode'      => ADDRESS_POSTAL,
        ],
        'sameAs'   => array_values($sameAs),
    ];
}

/**
 * LocalBusiness schema — şehir sayfaları ve ana sayfa.
 * MedicalBusiness + ProfessionalService multi-type.
 */
function schemaLocalBusiness(string $cityName = ''): array
{
    $city = $cityName ?: ADDRESS_DISTRICT;

    $sameAs = array_filter([
        SOCIAL_INSTAGRAM,
        SOCIAL_FACEBOOK,
        GBP_PROFILE_URL,
    ]);

    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => ['LocalBusiness', 'MedicalBusiness', 'ProfessionalService'],
        'name'       => SITE_NAME,
        'image'      => function_exists('publicAvatarUrl') ? publicAvatarUrl() : SITE_URL . '/images/dogukan.png',
        'url'        => SITE_URL,
        'telephone'  => CONTACT_PHONE,
        'email'      => CONTACT_EMAIL,
        'priceRange' => '₺₺',
        'description' => PSYCHOLOGIST_BIO,
        'address'    => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => ADDRESS_STREET,
            'addressLocality' => $city,
            'addressRegion'   => ADDRESS_CITY,
            'postalCode'      => ADDRESS_POSTAL,
            'addressCountry'  => ADDRESS_COUNTRY,
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => GEO_LATITUDE,
            'longitude' => GEO_LONGITUDE,
        ],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'İzmit'],
            ['@type' => 'City', 'name' => 'Kocaeli'],
            ['@type' => 'City', 'name' => 'Gebze'],
        ],
        'openingHoursSpecification' => [
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday'],
                'opens'     => '09:00',
                'closes'    => '18:00',
            ],
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => 'Saturday',
                'opens'     => '10:00',
                'closes'    => '14:00',
            ],
        ],
        'sameAs' => array_values($sameAs),
    ];

    // GBP Place ID varsa hasMap ekle
    if (GBP_PLACE_ID !== '') {
        $schema['hasMap'] = 'https://www.google.com/maps/place/?q=place_id:' . GBP_PLACE_ID;
    }

    return $schema;
}

/**
 * Service schema — hizmet detay sayfası.
 */
function schemaService(array $service): array
{
    return [
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => schemaText($service['title'] ?? ''),
        'description' => schemaText($service['meta_desc'] ?? $service['summary'] ?? ''),
        'url'         => SITE_URL . '/hizmetler/' . ($service['slug'] ?? ''),
        'provider'    => [
            '@type' => 'Person',
            'name'  => PSYCHOLOGIST_NAME,
            'url'   => SITE_URL,
        ],
        'areaServed'  => [
            '@type' => 'City',
            'name'  => ADDRESS_DISTRICT,
        ],
        'serviceType' => 'Psikolojik Danışmanlık',
    ];
}

/**
 * BlogPosting schema — tekil yazı.
 */
function schemaBlogPosting(array $post): array
{
    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'BlogPosting',
        'headline'         => schemaText($post['title'] ?? ''),
        'description'      => schemaText($post['meta_desc'] ?? ''),
        'image'            => buildImageUrl($post['thumbnail'] ?? ''),
        'author'           => [
            '@type' => 'Person',
            'name'  => schemaText($post['author_name'] ?? PSYCHOLOGIST_NAME),
        ],
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => SITE_NAME,
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => function_exists('publicAvatarUrl') ? publicAvatarUrl() : SITE_URL . '/images/dogukan.png',
            ],
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id'   => SITE_URL . '/blog/' . ($post['slug'] ?? ''),
        ],
    ];

    if ($date = schemaDate($post['published_at'] ?? null)) {
        $schema['datePublished'] = $date;
        $schema['dateModified']  = schemaDate($post['updated_at'] ?? null) ?? $date;
    }

    return $schema;
}

/**
 * FAQPage schema.
 *
 * @param array $items [['question' => '...', 'answer' => '...'], ...]
 */
function schemaFaqPage(array $items): array
{
    $entities = array_map(fn ($item) => [
        '@type' => 'Question',
        'name'  => schemaText($item['question'] ?? ''),
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => schemaText($item['answer'] ?? ''),
        ],
    ], $items);

    return [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $entities,
    ];
}

/**
 * BreadcrumbList schema.
 *
 * @param array $crumbs [['name' => '...', 'url' => '...'], ...]
 */
function schemaBreadcrumb(array $crumbs): array
{
    $items    = [];
    $position = 1;

    foreach ($crumbs as $crumb) {
        $item = [
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => schemaText($crumb['name'] ?? ''),
        ];

        if (!empty($crumb['url'])) {
            $item['item'] = $crumb['url'];
        }

        $items[] = $item;
        $position++;
    }

    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}
