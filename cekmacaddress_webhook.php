<?php
/**
 * Dentanet WAHA Webhook - Cek MAC Address Barang Gudang
 * Format pesan WA:
 *   carimacaddress-<MAC>
 * Contoh:
 *   carimacaddress-C0-51-5C-D2-D5-51
 *
 * Sumber data (publik, tanpa login):
 *   /GUDANGV1/gudang/barang_masuk_gudang.php?id_barang=&nama_barang=&mac_address=<MAC>&bulan=&tahun=&ekspedisi=
 */

date_default_timezone_set('Asia/Jakarta');

/* ===================== CONFIG ===================== */
$WAHA_BASE_URL = 'https://waha-anantasatr-9be.zetpod.id';
$WAHA_API_KEY  = 'hf9UwoFQLqhMfrK57k0fmnSx1NC2zvdy';
$SESSION       = 'default';

$URL_GUDANG   = 'http://mrtg.dentanet.id:1480/GUDANGV1/gudang/barang_masuk_gudang.php';

$LOG_FILE = __DIR__ . '/webhook_cekmacaddress.log';
$REPLY_NOT_FOUND = true;
/* ================================================== */

function logx($msg) {
  global $LOG_FILE;
  file_put_contents($LOG_FILE, date('Y-m-d H:i:s').' '.$msg.PHP_EOL, FILE_APPEND);
}

function http_get($url) {
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Webhook CekMacAddress)',
  ]);
  $out = curl_exec($ch);
  curl_close($ch);
  return $out ?: '';
}

function send_text($to, $text) {
  global $WAHA_BASE_URL, $WAHA_API_KEY, $SESSION;
  $payload = [
    'session' => $SESSION,
    'chatId'  => $to,
    'phone'   => preg_replace('/\D+/', '', $to),
    'text'    => $text,
  ];

  $ch = curl_init(rtrim($WAHA_BASE_URL,'/').'/api/sendText');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
      'Accept: application/json',
      'Content-Type: application/json',
      "X-Api-Key: {$WAHA_API_KEY}",
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT => 15,
  ]);
  $res  = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  logx("sendText [{$http}] to={$to} len=".strlen($text));
}

/* ---------------- Parsing Helpers ----------------- */
function norm($s) {
  $s = html_entity_decode($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  $s = preg_replace('/\s+/u', ' ', $s);
  return trim(mb_strtolower($s));
}

/** cari tabel yang header-nya memuat "id barang" */
function pick_table_with_headers($xp) {
  foreach ($xp->query('//table') as $tb) {
    $heads = [];
    foreach ($xp->query('.//thead//th', $tb) as $i => $th) {
      $heads[$i] = norm($th->textContent);
    }
    $joined = implode('|', $heads);
    if (strpos($joined, 'id barang') !== false) {
      return [$tb, $heads];
    }
  }
  // fallback: tabel pertama
  $tb = $xp->query('//table')->item(0);
  if ($tb) {
    $heads = [];
    foreach ($xp->query('.//thead//th', $tb) as $i => $th) {
      $heads[$i] = norm($th->textContent);
    }
    return [$tb, $heads];
  }
  return [null, []];
}

function parse_table_first_row($html) {
  if (!$html) return [[], null];
  libxml_use_internal_errors(true);
  $dom = new DOMDocument();
  @$dom->loadHTML($html);
  $xp = new DOMXPath($dom);

  list($table, $headers) = pick_table_with_headers($xp);
  if (!$table) return [[], null];

  // Ambil baris data pertama
  $row = null;
  $rows = $xp->query('.//tbody/tr', $table);
  if ($rows->length === 0) $rows = $xp->query('.//tr[not(th)]', $table);
  if ($rows->length > 0) {
    $first = $rows->item(0);
    $tds   = $xp->query('./td', $first);
    $cols  = [];
    foreach ($tds as $td) {
      $cols[] = trim(preg_replace('/\s+/u', ' ', $td->textContent));
    }
    if (implode('', $cols) !== '') $row = $cols;
  }
  return [$headers, $row];
}

function find_indexes(array $headers) {
  $idIdx = $namaIdx = $tipeIdx = null;
  foreach ($headers as $i => $h) {
    if ($idIdx===null   && $h==='id barang')   $idIdx = $i;
    if ($namaIdx===null && $h==='nama barang') $namaIdx = $i;
    if ($tipeIdx===null && ($h==='tipe barang' || $h==='type barang')) $tipeIdx = $i;
  }
  // fallback contains
  if ($idIdx===null)   foreach ($headers as $i=>$h) if (strpos($h,'id barang')!==false) { $idIdx=$i; break; }
  if ($namaIdx===null) foreach ($headers as $i=>$h) if (strpos($h,'nama barang')!==false){ $namaIdx=$i; break; }
  if ($tipeIdx===null) foreach ($headers as $i=>$h) if (strpos($h,'tipe')!==false){ $tipeIdx=$i; break; }
  return [$idIdx,$namaIdx,$tipeIdx];
}

/** Standarkan MAC menjadi XX-XX-... (supaya cocok dengan tampilan situs) */
function standardize_mac_hyphen($mac) {
  $hex = strtoupper(preg_replace('/[^A-F0-9]/i', '', $mac));
  if ($hex === '') return strtoupper($mac);
  return implode('-', str_split($hex, 2));
}

/* ===================== MAIN ===================== */
$raw = file_get_contents('php://input');
logx("--- WEBHOOK START ---");
logx("RAW: ".$raw);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(200);
  echo "ok";
  exit;
}

$payload = json_decode($raw, true);
if (!is_array($payload)) {
  logx("Invalid JSON");
  http_response_code(400);
  exit;
}

$event = $payload['event'] ?? '';
$data  = $payload['payload'] ?? [];
$from  = $data['from'] ?? '';
$body  = trim($data['body'] ?? '');

if ($event !== 'message' || !$from || $body === '') {
  http_response_code(200);
  echo "ignored";
  exit;
}

logx("Incoming: from={$from} body={$body}");

// ===== Filter format pesan =====
if (!preg_match('/^carimacaddress[-\s]+(.+)$/i', $body, $m)) {
  http_response_code(200);
  echo "ignored";
  exit;
}
$macRaw = trim($m[1]);
$macStd = standardize_mac_hyphen($macRaw);

// Bangun URL target
$queryUrl = $URL_GUDANG . '?id_barang=&nama_barang=&mac_address=' . urlencode($macStd) . '&bulan=&tahun=&ekspedisi=';
logx("Fetch: ".$queryUrl);

// Ambil & parse
$html = http_get($queryUrl);
list($headers, $row) = parse_table_first_row($html);

$resp = '';
if ($row) {
  list($idIdx, $namaIdx, $tipeIdx) = find_indexes($headers);
  $id   = $idIdx   !== null ? ($row[$idIdx]   ?? '-') : '-';
  $nama = $namaIdx !== null ? ($row[$namaIdx] ?? '-') : '-';
  $tipe = $tipeIdx !== null ? ($row[$tipeIdx] ?? '-') : '-';

  $resp = "Ditemukan untuk MAC *{$macStd}*:\n"
        . "ID Barang   : {$id}\n"
        . "Nama Barang : {$nama}\n"
        . "Tipe Barang : {$tipe}";
} else {
  if ($REPLY_NOT_FOUND) {
    $resp = "⚠️ Tidak ditemukan untuk MAC: *{$macStd}*";
  } else {
    $resp = "ok";
  }
}

// Kirim balasan via WAHA
send_text($from, $resp);

http_response_code(200);
echo "ok";
