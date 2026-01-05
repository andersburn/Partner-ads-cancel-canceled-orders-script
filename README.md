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
*/15 * * * * /usr/bin/php /sti/til/dit/parner_ads_cancel_canceled_orders.php
🖥️ Brugerflade
Scriptet indeholder et indbygget dashboard, der viser:

<h2>Forbindelsesstatus til Shopify.</h2>

En liste over de seneste 50 annullerede ordrer.

Direkte feedback fra Partner Ads API for hver behandlet ordre.

<h2>Begræsninger</h2>
1. Manglende hukommelse (State)

Da vi fjernede logikken, der husker, hvor scriptet sidst slap, har scriptet ingen "hukommelse".

Gentagne kald: Hver gang scriptet kører (f.eks. hvert 15. minut), sender det de samme 50 ordrer til Partner Ads igen.

API-belastning: Selvom Partner Ads sandsynligvis bare ignorerer dubletter, skaber det unødvendig trafik hos både Shopify og Partner Ads.

2. Loft på antal ordrer (Pagination)

Scriptet henter kun de 50 nyeste annullerede ordrer.

Hvis du har en dag med ekstremt mange annulleringer (f.eks. under Black Friday), og der bliver annulleret 60 ordrer mellem to kørsler, vil de 10 ældste ordrer i det interval aldrig blive opdaget, fordi de ryger ud af "Top 50"-listen.

3. Hastighed og Timeouts

Scriptet arbejder sekventielt (én efter én).

For hver ordre skal scriptet vente på svar fra Partner Ads, før det går videre til den næste.

Hvis du har 50 ordrer, og hvert kald tager 1 sekund, tager scriptet knap et minut at køre. De fleste PHP-servere har en standard tidsgrænse (max_execution_time) på 30 eller 60 sekunder. Hvis scriptet tager for lang tid, stopper serveren det midt i processen.

4. Ingen automatisk fejlhåndtering (Retries)

Hvis Partner Ads' server er nede i de 5 minutter, hvor scriptet kører, vil kaldet fejle.

Scriptet vil forsøge igen næste gang cronjobbet kører, men hvis ordren i mellemtiden er røget ud af "Top 50"-listen hos Shopify, bliver den aldrig annulleret hos Partner Ads.

5. Sikkerhed

API-nøglerne er hardcoded direkte i filen.

Hvis du ved en fejl uploader filen til et offentligt arkiv (som et offentligt GitHub-repo) eller placerer den i en mappe, der kan tilgås direkte via en browser uden beskyttelse, kan andre se dine adgangskoder til din butik.

6. Ingen verifikation af kilde

Scriptet annullerer alle annullerede ordrer i Partner Ads, uanset om de oprindeligt kom fra en Partner Ads-partner eller ej.

Partner Ads vil blot returnere en fejl på de ordrer, de ikke kender, hvilket er harmløst, men det fylder i dine logs.
