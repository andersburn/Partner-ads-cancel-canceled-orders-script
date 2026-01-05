<h1>Shopify to Partner Ads: Automatic Order Cancellation</h1>
Dette script automatiserer synkroniseringen mellem Shopify og Partner Ads, så ordrer, der annulleres i din webshop, automatisk bliver annulleret i Partner Ads-systemet. Dette sikrer korrekt afregning af provision uden manuelt arbejde.

<h2>🚀 Funktioner</h2>
Automatisk Synkronisering: Henter de seneste annullerede ordrer fra Shopify via REST API.

Partner Ads Integration: Sender annulleringer direkte til Partner Ads via deres autannsalg.php API.

Visuelt Dashboard: En moderne brugerflade bygget med Tailwind CSS til manuel overvågning og kørsel.

Cronjob Ready: Kan køres automatisk med faste intervaller (f.eks. hvert 15. minut).

Fejlhåndtering: Viser statusmeddelelser for hver enkelt ordrebehandling.

<h2>🛠️ Installation & Opsætning</h2>
Shopify API: Opret en Custom App i din Shopify Admin og giv den read_orders tilladelser.

Konfiguration: Indsæt dine oplysninger i toppen af PHP-filen:

$shop_url: Din butiks URL.

$access_token: Din Shopify Access Token.

$pa_key: Din unikke Partner Ads API nøgle.

$pa_prg: Dit Partner Ads Program ID.

Upload: Upload filen til din server (PHP 7.4+ anbefales).

Automatisering: Opsæt et cronjob for at køre scriptet automatisk:

Bash
*/15 * * * * /usr/bin/php /sti/til/dit/script.php
🖥️ Brugerflade
Scriptet indeholder et indbygget dashboard, der viser:

<h2>Forbindelsesstatus til Shopify.</h2>

En liste over de seneste 50 annullerede ordrer.

Direkte feedback fra Partner Ads API for hver behandlet ordre.
