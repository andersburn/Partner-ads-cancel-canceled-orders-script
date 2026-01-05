<?php
// --- KONFIGURATION ---
$shop_url = "handle.myshopify.com"; //fx: butikhandle.myshopify.com 
$access_token = "shpat_x7s7x7s7d7s7s7"; //Shopify API kode fx: shpat_c2fhfds389cf1233dd2f4302f1
$pa_key = "xxxxxx"; //Parner ads api id fx: 12345678901234567890
$pa_prg = "12345"; //Parner ads program id fx: 12345

$results = [];
$message = "";

// Funktion til at kalde Partner-ads
function callPartnerAds($orderNum, $key, $prg) {
    $url = "https://www.partner-ads.com/dk/autannsalg.php?" . http_build_query([
        "key"     => $key,
        "prg"     => $prg,
        "ordrenr" => $orderNum,
        "tekst"   => "Ordre annulleret via automatik"
    ]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return trim(strip_tags($response)); // Returnerer svar fra Partner-ads
}

// Håndtering af synkronisering
if (isset($_POST['sync']) || isset($_GET['cron'])) {
    // 1. Hent annullerede ordrer fra Shopify (seneste 50)
    $shopify_url = "https://$shop_url/admin/api/2024-10/orders.json?status=cancelled&limit=50&fields=order_number,cancelled_at";
    
    $ch = curl_init($shopify_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Shopify-Access-Token: $access_token"]);
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    $orders = $data['orders'] ?? [];
    
    // 2. Kør ALLE ordrer igennem Partner-ads kaldet
    foreach ($orders as $order) {
        $status = callPartnerAds($order['order_number'], $pa_key, $pa_prg);
        $results[] = [
            'number' => $order['order_number'],
            'date'   => $order['cancelled_at'],
            'status' => $status
        ];
    }
    
    $message = "Synkronisering fuldført. " . count($results) . " ordrer er processeret.";
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Ads Sync Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">

    <div class="max-w-5xl mx-auto py-12 px-6">
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Partner Ads <span class="text-indigo-600">Sync</span></h1>
                <p class="text-slate-500 mt-1">Automatisk annullering af ordrer fra Shopify</p>
            </div>
            <form method="POST">
                <button name="sync" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-indigo-200 active:scale-95">
                    Kør fuld synkronisering nu
                </button>
            </form>
        </div>

        <?php if ($message): ?>
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center text-emerald-800">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600 uppercase tracking-wider">Ordre</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600 uppercase tracking-wider">Dato (Shopify)</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600 uppercase tracking-wider text-right">Partner Ads Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center text-slate-400 italic">
                                Ingen ordrer behandlet i denne session. Tryk på knappen for at starte.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results as $res): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-slate-700">#<?php echo $res['number']; ?></td>
                                <td class="px-6 py-4 text-slate-500 text-sm">
                                    <?php echo date("d/m-Y H:i", strtotime($res['date'])); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        <?php echo $res['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <p class="mt-6 text-center text-slate-400 text-xs uppercase tracking-widest font-semibold">
            Program ID: <?php echo $pa_prg; ?> &bull; Systemet tjekker de 50 seneste annulleringer
        </p>
    </div>

</body>
</html>
