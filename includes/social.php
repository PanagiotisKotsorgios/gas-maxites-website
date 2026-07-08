<?php
function social_links(): array {
    $out = [];
    $map = [
        ['Facebook',    setting('facebook_url'),        'fa-brands fa-facebook-f'],
        ['Instagram',   setting('instagram_url'),       'fa-brands fa-instagram'],
        ['TikTok',      setting('tiktok_url'),          'fa-brands fa-tiktok'],
        ['YouTube',     setting('youtube_url'),         'fa-brands fa-youtube'],
        ['X (Twitter)', setting('twitter_url'),         'fa-brands fa-x-twitter'],
        ['Google',      setting('google_reviews_url'),  'fa-brands fa-google'],
    ];
    foreach ($map as [$label, $url, $icon]) {
        $url = trim($url);
        if ($url !== '' && $url !== '#') $out[] = [$label, $url, $icon];
    }
    return $out;
}
function contact_links(): array {
    $out = [];
    if (($v = trim(setting('phone1'))) !== '') $out[] = ['Σταθερό',  'tel:' . $v, 'fa-solid fa-phone', $v];
    if (($v = trim(setting('phone2'))) !== '') $out[] = ['Κινητό',   'tel:' . $v, 'fa-solid fa-mobile-screen', $v];
    if (($v = trim(setting('whatsapp_number'))) !== '') $out[] = ['WhatsApp', 'https://wa.me/' . preg_replace('/\D+/', '', $v), 'fa-brands fa-whatsapp', $v];
    if (($v = trim(setting('viber_number'))) !== '') $out[] = ['Viber', 'viber://chat?number=%2B' . preg_replace('/\D+/', '', $v), 'fa-brands fa-viber', $v];
    if (($v = trim(setting('email'))) !== '') $out[] = ['Email',    'mailto:' . $v, 'fa-solid fa-envelope', $v];
    return $out;
}
