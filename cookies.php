<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Πολιτική Cookies — ' . SITE_NAME;
$active = 'cookies';
$legal_title = 'Πολιτική Cookies';
$legal_icon = 'cookie-bite';
$legal_updated = date('d/m/Y');
ob_start();
?>
<p>Ο ιστότοπός μας χρησιμοποιεί περιορισμένο αριθμό cookies, αποκλειστικά για βασική λειτουργικότητα και στατιστική επισκεψιμότητας.</p>

<h2>1. Τι είναι τα cookies</h2>
<p>Μικρά αρχεία κειμένου που αποθηκεύονται στη συσκευή σας από τον περιηγητή, για να «θυμάται» ο ιστότοπος πληροφορίες σχετικά με την επίσκεψή σας.</p>

<h2>2. Ποια cookies χρησιμοποιούμε</h2>
<table class="legal-table">
  <thead><tr><th>Όνομα</th><th>Είδος</th><th>Σκοπός</th><th>Διάρκεια</th></tr></thead>
  <tbody>
    <tr>
      <td><code>PHPSESSID</code></td>
      <td>Αναγκαίο</td>
      <td>Συνεδρία συνδεδεμένου διαχειριστή.</td>
      <td>Έως κλείσιμο περιηγητή</td>
    </tr>
    <tr>
      <td><code>maxites_cookie_ok</code></td>
      <td>Αναγκαίο</td>
      <td>Θυμάται ότι δεχτήκατε το banner cookies.</td>
      <td>12 μήνες</td>
    </tr>
  </tbody>
</table>

<h2>3. Στατιστικά επισκεψιμότητας</h2>
<p>Χρησιμοποιούμε <strong>δικό μας</strong> εργαλείο μέτρησης. Δεν χρησιμοποιούμε Google Analytics ή cookies τρίτων παρακολούθησης. Οι IP δεν αποθηκεύονται (χρησιμοποιείται μη αναστρέψιμο hash).</p>

<h2>4. Ενσωματωμένο περιεχόμενο</h2>
<p>Οι σελίδες μας ενσωματώνουν <strong>Google Maps</strong> (χάρτης) και <strong>YouTube</strong> (βίντεο αγώνων). Αυτές οι υπηρεσίες ενδέχεται να τοποθετούν δικά τους cookies. Δείτε τις πολιτικές των αντίστοιχων υπηρεσιών.</p>

<h2>5. Απενεργοποίηση cookies</h2>
<p>Μπορείτε να διαχειριστείτε τα cookies μέσα από τις ρυθμίσεις του περιηγητή σας. Τα cookies session είναι αναγκαία για τη λειτουργία του ιστότοπου.</p>
<?php
$legal_body = ob_get_clean();
include __DIR__ . '/includes/legal_page.php';
