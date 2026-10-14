<?php
// GCS Performance Dashboard - small demo app with sample data.
// Replace $rows with your real source (CSV/DB/API) when ready.
$rows = [
    ['name' => 'Asha',   'team' => 'Team A', 'handled' => 182, 'aht' => 312, 'csat' => 4.6, 'sla' => 97],
    ['name' => 'Ravi',   'team' => 'Team A', 'handled' => 165, 'aht' => 355, 'csat' => 4.3, 'sla' => 92],
    ['name' => 'Meera',  'team' => 'Team B', 'handled' => 201, 'aht' => 290, 'csat' => 4.8, 'sla' => 98],
    ['name' => 'John',   'team' => 'Team B', 'handled' => 143, 'aht' => 402, 'csat' => 3.9, 'sla' => 85],
    ['name' => 'Priya',  'team' => 'Team C', 'handled' => 176, 'aht' => 330, 'csat' => 4.5, 'sla' => 94],
    ['name' => 'Kiran',  'team' => 'Team C', 'handled' => 158, 'aht' => 370, 'csat' => 4.1, 'sla' => 89],
];

$team = $_GET['team'] ?? '';
$sort = $_GET['sort'] ?? 'handled';
$cols = ['name', 'team', 'handled', 'aht', 'csat', 'sla'];
if (!in_array($sort, $cols, true)) $sort = 'handled';

$teams = array_values(array_unique(array_column($rows, 'team')));
if ($team !== '') $rows = array_filter($rows, fn($r) => $r['team'] === $team);
usort($rows, fn($a, $b) => $sort === 'name' || $sort === 'team' ? strcmp($a[$sort], $b[$sort]) : $b[$sort] <=> $a[$sort]);

$n = max(count($rows), 1);
$total = array_sum(array_column($rows, 'handled'));
$avg = fn($k) => array_sum(array_column($rows, $k)) / $n;
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
$max = max(array_column($rows, 'handled') ?: [1]);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>GCS Performance Dashboard</title>
<style>
  body{font:15px system-ui,sans-serif;margin:0;background:#f4f6f9;color:#1d2433}
  main{max-width:900px;margin:0 auto;padding:16px}
  h1{font-size:20px}
  .tiles{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin:12px 0}
  .tile,.card{background:#fff;border-radius:10px;padding:14px;box-shadow:0 1px 3px #0001}
  .tile b{display:block;font-size:24px;margin-top:4px}
  .tile span{color:#667}
  table{width:100%;border-collapse:collapse}
  th,td{padding:8px;text-align:left;border-bottom:1px solid #eee}
  th a{color:inherit;text-decoration:none}
  .bar{background:#e3e8f0;border-radius:4px;height:10px}
  .bar i{display:block;height:100%;background:#2f6fed;border-radius:4px}
  .low{color:#c0392b;font-weight:600}
  select{padding:6px}
  .scroll{overflow-x:auto}
</style>
</head>
<body>
<main>
  <h1>GCS Performance Dashboard</h1>
  <form method="get">
    <label>Team
      <select name="team" onchange="this.form.submit()">
        <option value="">All</option>
        <?php foreach ($teams as $t): ?>
          <option <?= $t === $team ? 'selected' : '' ?>><?= $h($t) ?></option>
        <?php endforeach ?>
      </select>
    </label>
    <input type="hidden" name="sort" value="<?= $h($sort) ?>">
  </form>

  <div class="tiles">
    <div class="tile"><span>Total handled</span><b><?= $total ?></b></div>
    <div class="tile"><span>Avg handle time</span><b><?= round($avg('aht')) ?>s</b></div>
    <div class="tile"><span>Avg CSAT</span><b><?= number_format($avg('csat'), 1) ?>/5</b></div>
    <div class="tile"><span>SLA met</span><b><?= round($avg('sla')) ?>%</b></div>
  </div>

  <div class="card scroll">
    <table>
      <tr>
        <?php foreach (['name' => 'Agent', 'team' => 'Team', 'handled' => 'Handled', 'aht' => 'AHT (s)', 'csat' => 'CSAT', 'sla' => 'SLA %'] as $k => $label): ?>
          <th><a href="?team=<?= urlencode($team) ?>&sort=<?= $k ?>"><?= $label ?><?= $k === $sort ? ' ▾' : '' ?></a></th>
        <?php endforeach ?>
        <th style="width:25%">Volume</th>
      </tr>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= $h($r['name']) ?></td>
        <td><?= $h($r['team']) ?></td>
        <td><?= $r['handled'] ?></td>
        <td><?= $r['aht'] ?></td>
        <td><?= $r['csat'] ?></td>
        <td class="<?= $r['sla'] < 90 ? 'low' : '' ?>"><?= $r['sla'] ?></td>
        <td><div class="bar"><i style="width:<?= round($r['handled'] / $max * 100) ?>%"></i></div></td>
      </tr>
      <?php endforeach ?>
    </table>
  </div>
  <p style="color:#889">Sample data. Run: <code>php -S localhost:8000</code></p>
</main>
</body>
</html>
