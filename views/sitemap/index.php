<?php

/** @var yii\web\View $this */
/** @var string $content */
/** @var array $items */
/** @var string $host */
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL; ?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9 https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
        <loc>https://xn--80abggjugofbdwfe3b6c2dta2a.xn--p1ai</loc>
        <lastmod><?= date(DATE_W3C, time() - (30 * 60)); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.2</priority>
    </url>
    <url>
        <loc>https://xn--80abggjugofbdwfe3b6c2dta2a.xn--p1ai/archive</loc>
        <lastmod><?= date(DATE_W3C, time() - (30 * 60)); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.2</priority>
    </url>
    <url>
        <loc>https://xn--80abggjugofbdwfe3b6c2dta2a.xn--p1ai/news-video</loc>
        <lastmod><?= date(DATE_W3C, time() - (30 * 60)); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.2</priority>
    </url>
    <url>
        <loc>https://xn--80abggjugofbdwfe3b6c2dta2a.xn--p1ai/documents</loc>
        <lastmod><?= date(DATE_W3C, time() - (30 * 60)); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.2</priority>
    </url>
    <url>
        <loc>https://xn--80abggjugofbdwfe3b6c2dta2a.xn--p1ai/our-achievements</loc>
        <lastmod><?= date(DATE_W3C, time() - (30 * 60)); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.2</priority>
    </url>
    <url>
        <loc>https://xn--80abggjugofbdwfe3b6c2dta2a.xn--p1ai/contact</loc>
        <lastmod><?= date(DATE_W3C, time() - (30 * 60)); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.2</priority>
    </url>

    <?php foreach ($items as $item): ?>
        <?php foreach ($item['models'] as $model): ?>
            <url>
                <loc><?= $host; ?><?= $model->getUrl(); ?></loc>
                <lastmod><?= date(DATE_W3C, $model->updated_at); ?></lastmod>
                <changefreq><?= $item['changefreq']; ?></changefreq>
                <priority><?= $item['priority']; ?></priority>
            </url>
        <?php endforeach; ?>
    <?php endforeach; ?>
</urlset>