<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Ρυθμίσεις — Μαχητές Admin';
$active = 'settings';
$pdo = db();
$flash = null;

$groups = [
    'branding'    => 'Επωνυμία &amp; top bar',
    'hero'        => 'Αρχική — Hero',
    'features'    => 'Αρχική — 4 features',
    'aboutblock'  => 'Αρχική — Split “Ο σύλλογος”',
    'programs_h'  => 'Αρχική — Τμήματα (headings)',
    'athletes_h'  => 'Αρχική — Αθλητές (headings)',
    'trophies_h'  => 'Αρχική — Τρόπαια (headings + bg)',
    'gallery_h'   => 'Αρχική — Gallery (headings)',
    'reviews_h'   => 'Αρχική — Reviews (headings + bg)',
    'news_h'      => 'Αρχική — Νέα (headings)',
    'ctaband'     => 'Αρχική — CTA band',

    'about_pg'    => 'Σελίδα Σύλλογος (about.php)',
    'about_cards' => 'Σελίδα Σύλλογος — 3 link cards',

    'history_pg'  => 'Σελίδα Ιστορία (hero + timeline)',
    'history_tl'  => 'Ιστορία — 5 timeline items',

    'master_pg'   => 'Σελίδα Δάσκαλος (hero + bio)',
    'master_facts'=> 'Δάσκαλος — 4 fact-list items',

    'schedule_pg' => 'Σελίδα Πρόγραμμα (hero + τμήματα header)',
    'contact_pg'  => 'Σελίδα Επικοινωνία (hero)',
    'athletes_pg' => 'Σελίδα Αθλητές (hero)',
    'trophies_pg' => 'Σελίδα Τρόπαια (hero)',
    'gallery_pg'  => 'Σελίδα Gallery (hero)',
    'blog_pg'     => 'Σελίδα Νέα (hero)',

    'contact'     => 'Στοιχεία επικοινωνίας',
    'social'      => 'Κοινωνικά δίκτυα &amp; σύνδεσμοι',
    'footer'      => 'Footer',
];
$fields = [
    // --- Branding & top bar ---
    'topbar_hours'   => ['Ώρες λειτουργίας (top bar, προαιρετικό)', 'text', 'Δευ–Παρ 17:00–20:30', 'branding'],

    // --- Home hero ---
    'hero_eyebrow'   => ['Eyebrow (πάνω από τον τίτλο)', 'text', 'Taekwondo · Μεσολόγγι · Από το 1990', 'hero'],
    'hero_title'     => ['Τίτλος', 'text', 'Δύναμη. Πειθαρχία. Σεβασμός.', 'hero'],
    'hero_subtitle'  => ['Υπότιτλος', 'textarea', '', 'hero'],
    'hero_bg'        => ['Εικόνα φόντου (URL)', 'text', '', 'hero'],
    'hero_cta1'      => ['Κείμενο κουμπιού #1 (εγγραφή)', 'text', 'Έλα για εγγραφή', 'hero'],
    'hero_cta2'      => ['Κείμενο κουμπιού #2 (πρόγραμμα)', 'text', 'Δες το πρόγραμμα', 'hero'],
    'hero_rating'    => ['Κείμενο rating (π.χ. 4,9/5)', 'text', '4,9/5', 'hero'],
    'hero_rating_sub'=> ['Κείμενο μετά το rating', 'text', 'στο Google · Δες αξιολογήσεις', 'hero'],

    // --- Home 4 features ---
    'feat1_icon'  => ['#1 Εικονίδιο (FontAwesome)', 'text', 'fa-solid fa-hand-fist', 'features'],
    'feat1_title' => ['#1 Τίτλος', 'text', '7ου dan δάσκαλος', 'features'],
    'feat1_body'  => ['#1 Κείμενο', 'textarea', '', 'features'],
    'feat2_icon'  => ['#2 Εικονίδιο', 'text', 'fa-solid fa-medal', 'features'],
    'feat2_title' => ['#2 Τίτλος', 'text', 'Πανελλήνιοι τίτλοι', 'features'],
    'feat2_body'  => ['#2 Κείμενο', 'textarea', '', 'features'],
    'feat3_icon'  => ['#3 Εικονίδιο', 'text', 'fa-solid fa-children', 'features'],
    'feat3_title' => ['#3 Τίτλος', 'text', 'Όλες οι ηλικίες', 'features'],
    'feat3_body'  => ['#3 Κείμενο', 'textarea', '', 'features'],
    'feat4_icon'  => ['#4 Εικονίδιο', 'text', 'fa-solid fa-shield-halved', 'features'],
    'feat4_title' => ['#4 Τίτλος', 'text', 'Αυτοάμυνα', 'features'],
    'feat4_body'  => ['#4 Κείμενο', 'textarea', '', 'features'],

    // --- Home about split ---
    'about_eyebrow'  => ['Eyebrow', 'text', 'Ο Σύλλογος', 'aboutblock'],
    'about_title'    => ['Τίτλος', 'text', 'Μια οικογένεια. Μια πολεμική τέχνη.', 'aboutblock'],
    'about_body'     => ['Κείμενο (HTML)', 'textarea', '', 'aboutblock'],
    'about_bg'       => ['Εικόνα δεξιά (URL)', 'text', '', 'aboutblock'],
    'about_stat1_val'=> ['Stat #1 τιμή', 'text', '7ος Dan', 'aboutblock'],
    'about_stat1_lbl'=> ['Stat #1 label', 'text', 'δάσκαλος', 'aboutblock'],
    'about_stat2_val'=> ['Stat #2 τιμή', 'text', '5.100+', 'aboutblock'],
    'about_stat2_lbl'=> ['Stat #2 label', 'text', 'Φίλοι στα social', 'aboutblock'],
    'about_stat3_val'=> ['Stat #3 τιμή', 'text', '4,9/5', 'aboutblock'],
    'about_stat3_lbl'=> ['Stat #3 label', 'text', 'Google reviews', 'aboutblock'],

    // --- Home programs headings ---
    'programs_eyebrow' => ['Eyebrow', 'text', 'Τμήματα', 'programs_h'],
    'programs_title'   => ['Τίτλος', 'text', 'Προπόνηση για κάθε ηλικία', 'programs_h'],
    'programs_sub'     => ['Υπότιτλος', 'text', '', 'programs_h'],

    // --- Home athletes headings ---
    'athletes_home_eyebrow' => ['Eyebrow', 'text', 'Οι αθλητές μας', 'athletes_h'],
    'athletes_home_title'   => ['Τίτλος', 'text', 'Οι μαχητές μας στο αγωνιστικό ταπί', 'athletes_h'],

    // --- Home trophies headings & bg ---
    'trophies_home_eyebrow' => ['Eyebrow', 'text', 'Τρόπαια & διακρίσεις', 'trophies_h'],
    'trophies_home_title'   => ['Τίτλος', 'text', 'Πρόσφατες νίκες', 'trophies_h'],
    'trophies_home_bg'      => ['Εικόνα φόντου (URL)', 'text', '', 'trophies_h'],
    'trophies_home_cta'     => ['Κείμενο CTA', 'text', 'Δες όλα τα τρόπαια', 'trophies_h'],

    // --- Home gallery headings ---
    'gallery_home_eyebrow' => ['Eyebrow', 'text', 'Στιγμές', 'gallery_h'],
    'gallery_home_title'   => ['Τίτλος', 'text', 'Ο σύλλογος σε δράση', 'gallery_h'],

    // --- Home reviews headings & bg ---
    'reviews_home_eyebrow' => ['Eyebrow', 'text', 'Αξιολογήσεις Google', 'reviews_h'],
    'reviews_home_title'   => ['Τίτλος', 'text', '4,9 / 5 από 15 αξιολογήσεις', 'reviews_h'],
    'reviews_home_sub'     => ['Υπότιτλος', 'text', 'Πραγματικά σχόλια από γονείς και αθλητές μας.', 'reviews_h'],
    'reviews_home_bg'      => ['Εικόνα φόντου (URL)', 'text', '', 'reviews_h'],
    'reviews_home_cta'     => ['Κείμενο CTA', 'text', 'Δες όλες τις αξιολογήσεις', 'reviews_h'],

    // --- Home news headings ---
    'news_home_eyebrow' => ['Eyebrow', 'text', 'Νέα', 'news_h'],
    'news_home_title'   => ['Τίτλος', 'text', 'Πρόσφατα άρθρα', 'news_h'],

    // --- Home CTA band ---
    'cta_home_title' => ['Τίτλος', 'text', 'Έτοιμος να μπεις στο ταπί;', 'ctaband'],
    'cta_home_body'  => ['Κείμενο', 'text', 'Έλα για μια δωρεάν δοκιμαστική προπόνηση. Ο δάσκαλος περιμένει.', 'ctaband'],
    'cta_home_bg'    => ['Εικόνα φόντου (URL)', 'text', '', 'ctaband'],
    'cta_home_phone' => ['Κείμενο κουμπιού τηλεφώνου', 'text', 'Πάρε τηλέφωνο', 'ctaband'],
    'cta_home_msg'   => ['Κείμενο κουμπιού μηνύματος', 'text', 'Στείλε μήνυμα', 'ctaband'],

    // --- About page ---
    'about_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'about_pg'],
    'about_page_eyebrow' => ['Eyebrow', 'text', 'Ο Σύλλογος', 'about_pg'],
    'about_page_title'   => ['Τίτλος', 'text', 'Ποιοι είμαστε', 'about_pg'],
    'about_page_lead'    => ['Υπότιτλος', 'text', 'Ο Γ.Α.Σ. Μαχητές Μεσολογγίου — δύναμη, πειθαρχία, σεβασμός.', 'about_pg'],
    'about_cards_eyebrow'=> ['Section eyebrow', 'text', 'Περισσότερα', 'about_pg'],
    'about_cards_title'  => ['Section τίτλος', 'text', 'Γνώρισε τον σύλλογο', 'about_pg'],

    // --- About page — 3 link cards ---
    'about_c1_icon'  => ['#1 Εικονίδιο', 'text', 'fa-solid fa-clock-rotate-left', 'about_cards'],
    'about_c1_title' => ['#1 Τίτλος', 'text', 'Η ιστορία μας', 'about_cards'],
    'about_c1_body'  => ['#1 Κείμενο', 'text', 'Πώς ξεκίνησε ο σύλλογος και τι έχουμε πετύχει.', 'about_cards'],
    'about_c2_icon'  => ['#2 Εικονίδιο', 'text', 'fa-solid fa-user-tie', 'about_cards'],
    'about_c2_title' => ['#2 Τίτλος', 'text', 'Ο δάσκαλος', 'about_cards'],
    'about_c2_body'  => ['#2 Κείμενο', 'text', 'Σωτήρης Λιαμέτης, 7ου Dan, ETU TKD WTF.', 'about_cards'],
    'about_c3_icon'  => ['#3 Εικονίδιο', 'text', 'fa-solid fa-calendar-days', 'about_cards'],
    'about_c3_title' => ['#3 Τίτλος', 'text', 'Πρόγραμμα', 'about_cards'],
    'about_c3_body'  => ['#3 Κείμενο', 'text', 'Ημέρες και ώρες προπονήσεων ανά τμήμα.', 'about_cards'],

    // --- History page ---
    'history_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'history_pg'],
    'history_page_eyebrow' => ['Eyebrow', 'text', 'Ιστορία', 'history_pg'],
    'history_page_title'   => ['Τίτλος', 'text', 'Η ιστορία των Μαχητών', 'history_pg'],
    'history_page_lead'    => ['Υπότιτλος', 'text', 'Δεκαετίες προπόνησης, εμπειρίας και νικών στο Μεσολόγγι.', 'history_pg'],
    'history_body'         => ['Εισαγωγικό κείμενο (HTML)', 'textarea', '', 'history_pg'],

    // --- History timeline (5 items) ---
    'hist_t1_year'  => ['#1 Έτος/Εποχή', 'text', '1990s', 'history_tl'],
    'hist_t1_title' => ['#1 Τίτλος', 'text', 'Οι πρώτες προπονήσεις', 'history_tl'],
    'hist_t1_body'  => ['#1 Κείμενο', 'textarea', '', 'history_tl'],
    'hist_t2_year'  => ['#2 Έτος/Εποχή', 'text', '2000s', 'history_tl'],
    'hist_t2_title' => ['#2 Τίτλος', 'text', 'Πανελλήνιες συμμετοχές', 'history_tl'],
    'hist_t2_body'  => ['#2 Κείμενο', 'textarea', '', 'history_tl'],
    'hist_t3_year'  => ['#3 Έτος/Εποχή', 'text', '2010s', 'history_tl'],
    'hist_t3_title' => ['#3 Τίτλος', 'text', 'Ο σύλλογος μεγαλώνει', 'history_tl'],
    'hist_t3_body'  => ['#3 Κείμενο', 'textarea', '', 'history_tl'],
    'hist_t4_year'  => ['#4 Έτος/Εποχή', 'text', '2024', 'history_tl'],
    'hist_t4_title' => ['#4 Τίτλος', 'text', 'Μαθήματα αυτοάμυνας στο ΚΑΠΗ', 'history_tl'],
    'hist_t4_body'  => ['#4 Κείμενο', 'textarea', '', 'history_tl'],
    'hist_t5_year'  => ['#5 Έτος/Εποχή', 'text', 'Σήμερα', 'history_tl'],
    'hist_t5_title' => ['#5 Τίτλος', 'text', 'Δύναμη, Πειθαρχία, Σεβασμός', 'history_tl'],
    'hist_t5_body'  => ['#5 Κείμενο', 'textarea', '', 'history_tl'],

    // --- Master page ---
    'master_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'master_pg'],
    'master_page_eyebrow' => ['Eyebrow', 'text', 'Ο Δάσκαλος', 'master_pg'],
    'master_name'         => ['Όνομα δασκάλου', 'text', 'Σωτήρης Λιαμέτης', 'master_pg'],
    'master_title'        => ['Τίτλος δασκάλου', 'text', 'Δάσκαλος 7ου Dan · ETU TKD WTF', 'master_pg'],
    'master_photo'        => ['Φωτογραφία (filename από uploads/athletes)', 'text', '', 'master_pg'],
    'master_photo_fallback' => ['Εικόνα εναλλακτική (URL)', 'text', '', 'master_pg'],
    'master_bio'          => ['Βιογραφικό (HTML)', 'textarea', '', 'master_pg'],
    'master_cta1'         => ['CTA #1 (κύρια)', 'text', 'Κλείσε δοκιμαστική', 'master_pg'],
    'master_cta2'         => ['CTA #2 (δευτερεύουσα)', 'text', 'Δες τους αθλητές του', 'master_pg'],

    // --- Master facts ---
    'mf1_icon'  => ['#1 Εικονίδιο', 'text', 'fa-solid fa-medal', 'master_facts'],
    'mf1_title' => ['#1 Τίτλος', 'text', '7ου Dan', 'master_facts'],
    'mf1_body'  => ['#1 Υπότιτλος', 'text', 'μαύρη ζώνη Tae Kwon Do', 'master_facts'],
    'mf2_icon'  => ['#2 Εικονίδιο', 'text', 'fa-solid fa-globe', 'master_facts'],
    'mf2_title' => ['#2 Τίτλος', 'text', 'ETU · WTF', 'master_facts'],
    'mf2_body'  => ['#2 Υπότιτλος', 'text', 'Ευρωπαϊκή & Παγκόσμια Ομοσπονδία', 'master_facts'],
    'mf3_icon'  => ['#3 Εικονίδιο', 'text', 'fa-solid fa-users', 'master_facts'],
    'mf3_title' => ['#3 Τίτλος', 'text', 'Δεκαετίες', 'master_facts'],
    'mf3_body'  => ['#3 Υπότιτλος', 'text', 'στην προπόνηση αθλητών', 'master_facts'],
    'mf4_icon'  => ['#4 Εικονίδιο', 'text', 'fa-solid fa-trophy', 'master_facts'],
    'mf4_title' => ['#4 Τίτλος', 'text', 'Πολλές διακρίσεις', 'master_facts'],
    'mf4_body'  => ['#4 Υπότιτλος', 'text', 'μαθητές του σε πανελλήνιους αγώνες', 'master_facts'],

    // --- Schedule page ---
    'schedule_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'schedule_pg'],
    'schedule_page_eyebrow' => ['Eyebrow', 'text', 'Πρόγραμμα', 'schedule_pg'],
    'schedule_page_title'   => ['Τίτλος', 'text', 'Προπονήσεις & τμήματα', 'schedule_pg'],
    'schedule_page_lead'    => ['Υπότιτλος', 'text', 'Το εβδομαδιαίο πρόγραμμά μας και τα τμήματα ανά ηλικία και επίπεδο.', 'schedule_pg'],
    'schedule_prog_eyebrow' => ['Section “Τμήματα” eyebrow', 'text', 'Τμήματα', 'schedule_pg'],
    'schedule_prog_title'   => ['Section “Τμήματα” τίτλος', 'text', 'Επιλέξτε την ομάδα που ταιριάζει', 'schedule_pg'],
    'schedule_week_eyebrow' => ['Section “Εβδομαδιαίο” eyebrow', 'text', 'Εβδομαδιαίο πρόγραμμα', 'schedule_pg'],
    'schedule_week_title'   => ['Section “Εβδομαδιαίο” τίτλος', 'text', 'Ημέρες & ώρες προπονήσεων', 'schedule_pg'],
    'schedule_week_note'    => ['Section “Εβδομαδιαίο” υπότιτλος', 'text', 'Η στήλη της σημερινής ημέρας είναι επισημασμένη. Σύρετε πλάγια σε κινητό.', 'schedule_pg'],

    // --- Contact page ---
    'contact_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'contact_pg'],
    'contact_page_eyebrow' => ['Eyebrow', 'text', 'Επικοινωνία', 'contact_pg'],
    'contact_page_title'   => ['Τίτλος', 'text', 'Έλα στους Μαχητές', 'contact_pg'],
    'contact_page_lead'    => ['Υπότιτλος', 'text', 'Στείλε μας μήνυμα ή τηλεφώνησε — κλείνουμε δοκιμαστική προπόνηση.', 'contact_pg'],

    // --- Athletes page ---
    'athletes_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'athletes_pg'],
    'athletes_page_eyebrow' => ['Eyebrow', 'text', 'Οι Αθλητές μας', 'athletes_pg'],
    'athletes_page_title'   => ['Τίτλος', 'text', 'Οι Μαχητές', 'athletes_pg'],
    'athletes_page_lead'    => ['Υπότιτλος', 'text', 'Οι αθλητές που τιμούν τον σύλλογο στο ταπί.', 'athletes_pg'],

    // --- Trophies page ---
    'trophies_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'trophies_pg'],
    'trophies_page_eyebrow' => ['Eyebrow', 'text', 'Τρόπαια', 'trophies_pg'],
    'trophies_page_title'   => ['Τίτλος', 'text', 'Τρόπαια & Διακρίσεις', 'trophies_pg'],
    'trophies_page_lead'    => ['Υπότιτλος', 'text', 'Οι νίκες μας σε πανελλήνια πρωταθλήματα και κύπελλα.', 'trophies_pg'],

    // --- Gallery page ---
    'gallery_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'gallery_pg'],
    'gallery_page_eyebrow' => ['Eyebrow', 'text', 'Στιγμές', 'gallery_pg'],
    'gallery_page_title'   => ['Τίτλος', 'text', 'Gallery', 'gallery_pg'],
    'gallery_page_lead'    => ['Υπότιτλος', 'text', 'Ο σύλλογος σε δράση — προπονήσεις, αγώνες, νίκες.', 'gallery_pg'],

    // --- Blog page ---
    'blog_page_bg'      => ['Εικόνα hero (URL)', 'text', '', 'blog_pg'],
    'blog_page_eyebrow' => ['Eyebrow', 'text', 'Blog', 'blog_pg'],
    'blog_page_title'   => ['Τίτλος', 'text', 'Νέα & άρθρα', 'blog_pg'],
    'blog_page_lead'    => ['Υπότιτλος', 'text', 'Ενημερώσεις για αγώνες, ανακοινώσεις και ιστορίες από τους Μαχητές.', 'blog_pg'],

    // --- Contact info ---
    'address'        => ['Διεύθυνση', 'text', 'Γεωργίου Λιακατά 17, Μεσολόγγι 30200', 'contact'],
    'phone1'         => ['Σταθερό τηλέφωνο', 'text', '2631055890', 'contact'],
    'phone2'         => ['Κινητό τηλέφωνο', 'text', '6937125755', 'contact'],
    'email'          => ['Email', 'text', '', 'contact'],
    'whatsapp_number'=> ['WhatsApp (με κωδικό χώρας)', 'text', '306937125755', 'contact'],
    'viber_number'   => ['Viber (με κωδικό χώρας)', 'text', '306937125755', 'contact'],

    // --- Social ---
    'facebook_url'   => ['Facebook URL', 'text', 'https://facebook.com/liametisswtirismaxites1', 'social'],
    'instagram_url'  => ['Instagram URL', 'text', 'https://instagram.com/liametisswtirismaxites1', 'social'],
    'tiktok_url'     => ['TikTok URL', 'text', '', 'social'],
    'youtube_url'    => ['YouTube URL', 'text', '', 'social'],
    'twitter_url'    => ['X (Twitter) URL', 'text', '', 'social'],
    'google_reviews_url' => ['Σύνδεσμος αξιολογήσεων Google', 'text', '', 'social'],
    'google_maps_url'    => ['Σύνδεσμος Google Maps', 'text', '', 'social'],

    // --- Footer ---
    'footer_tagline' => ['Σύντομο κείμενο footer', 'textarea', 'Δύναμη, Πειθαρχία, Σεβασμός.', 'footer'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_POST['op'] ?? '') === 'save') {
        $up = $pdo->prepare('INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
        foreach ($fields as $k => $_) $up->execute([$k, (string)($_POST[$k] ?? '')]);
        $flash = 'Οι ρυθμίσεις αποθηκεύτηκαν.';
    }
    if (($_POST['op'] ?? '') === 'password') {
        $cur = (string)($_POST['current'] ?? '');
        $new = (string)($_POST['new'] ?? '');
        if (mb_strlen($new) < 8) $flash = 'Ο νέος κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες.';
        else {
            $me = current_user();
            $s = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
            $s->execute([$me['id']]); $row = $s->fetch();
            if (!$row || !password_verify($cur, $row['password_hash'])) $flash = 'Ο τρέχων κωδικός δεν είναι σωστός.';
            else {
                $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $me['id']]);
                $flash = 'Ο κωδικός αλλάχθηκε.';
            }
        }
    }
}

$vals = [];
foreach ($pdo->query('SELECT `key`,`value` FROM settings')->fetchAll() as $r) $vals[$r['key']] = $r['value'];

include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <h1>Ρυθμίσεις</h1>
  <p class="muted">Άφησε ένα πεδίο κενό για να χρησιμοποιηθεί η προεπιλογή (placeholder). Οι εικόνες φόντου δέχονται πλήρη URLs.</p>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>
  <form method="post" class="admin-form settings-form">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="op" value="save">
    <?php foreach ($groups as $gkey => $glabel): ?>
      <section class="panel">
        <h2><?= $glabel ?></h2>
        <div class="settings-grid">
          <?php foreach ($fields as $key => [$label, $type, $ph, $group]):
            if ($group !== $gkey) continue; ?>
            <label class="setting-<?= e($type) ?>">
              <span><?= e($label) ?></span>
              <?php if ($type === 'textarea'): ?>
                <textarea name="<?= e($key) ?>" rows="4" placeholder="<?= e($ph) ?>"><?= e($vals[$key] ?? '') ?></textarea>
              <?php else: ?>
                <input type="text" name="<?= e($key) ?>" value="<?= e($vals[$key] ?? '') ?>" placeholder="<?= e($ph) ?>">
              <?php endif; ?>
            </label>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
    <div class="save-bar">
      <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Αποθήκευση ρυθμίσεων</button>
    </div>
  </form>

  <section class="panel">
    <h2>Αλλαγή κωδικού</h2>
    <form method="post" class="admin-form" style="max-width:420px">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="op" value="password">
      <label>Τρέχων κωδικός<input type="password" name="current" required></label>
      <label>Νέος κωδικός (≥8 χαρ.)<input type="password" name="new" required minlength="8"></label>
      <button class="btn btn-primary" type="submit"><i class="fa-solid fa-key"></i> Αλλαγή</button>
    </form>
  </section>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
