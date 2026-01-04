<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

ini_set('display_errors', 0);

if (!isset($_SESSION['username'])) {
	header("location:login.php");
}

require_once 'db-conn.php';

$perPage = 5;
$hlm = isset($_POST['hlm']) ? (int)$_POST['hlm'] : 1;
if ($hlm < 1) $hlm = 1;

$offset = ($hlm - 1) * $perPage;

$countStmt = $pdo->query('SELECT COUNT(*) FROM article');
$total_records = (int)$countStmt->fetchColumn();
$jumlah_page = (int)ceil($total_records / $perPage);

$sql = "SELECT * FROM article ORDER BY tanggal DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
?>

<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
<table class="w-full">
	<thead>
		<tr class="bg-gray-100 dark:bg-gray-700">
			<th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">No</th>
			<th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Judul</th>
			<th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Isi</th>
			<th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Gambar</th>
			<th class="px-4 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-200">Aksi</th>
		</tr>
	</thead>
	<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
		<?php
		$no = $offset + 1;
		if ($stmt->rowCount() > 0) {
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			?>
			<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
				<td class="px-4 py-4 text-gray-600 dark:text-gray-300"><?= $no++ ?></td>
				<td class="px-4 py-4 min-w-[200px]">
					<div class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($row['judul']) ?></div>
					<div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
						<span class="inline-flex items-center gap-1"><i class="bi bi-calendar3"></i> <?= $row['tanggal'] ?></span>
					</div>
					<div class="text-sm text-gray-500 dark:text-gray-400">
						<span class="inline-flex items-center gap-1"><i class="bi bi-person"></i> <?= htmlspecialchars($row['username']) ?></span>
					</div>
				</td>
				<td class="px-4 py-4 max-w-[300px]">
					<p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3"><?= htmlspecialchars(substr($row['isi'], 0, 150)) ?><?= strlen($row['isi']) > 150 ? '...' : '' ?></p>
				</td>
				<td class="px-4 py-4">
					<?php if (!empty($row['gambar'])): ?>
						<img src="img/<?= htmlspecialchars($row['gambar']) ?>" class="w-20 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm" alt="<?= htmlspecialchars($row['judul']) ?>">
					<?php else: ?>
						<div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
							<i class="bi bi-image text-2xl text-gray-400"></i>
						</div>
					<?php endif; ?>
				</td>
				<td class="px-4 py-4">
					<div class="flex items-center justify-center gap-2">
						<button onclick="openEditModal(<?= (int)$row['id'] ?>, '<?= htmlspecialchars(addslashes($row['judul']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($row['isi']), ENT_QUOTES) ?>', '<?= htmlspecialchars($row['gambar'] ?? '') ?>')"
							class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-lg transition-colors cursor-pointer" title="Edit">
							<i class="bi bi-pencil-square"></i>
						</button>
						<button onclick="openDeleteModal(<?= (int)$row['id'] ?>, '<?= htmlspecialchars(addslashes($row['judul']), ENT_QUOTES) ?>')"
							class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition-colors cursor-pointer" title="Hapus">
							<i class="bi bi-trash"></i>
						</button>
					</div>
				</td>
			</tr>
			<?php
			}
		} else {
			?>
			<tr>
				<td colspan="5" class="px-4 py-12 text-center">
					<div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
						<i class="bi bi-inbox text-5xl mb-3"></i>
						<p class="text-lg font-medium">Belum ada artikel</p>
						<p class="text-sm">Klik tombol "Tambah Artikel" untuk membuat artikel baru</p>
					</div>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
</div>

<?php if ($jumlah_page > 1): ?>
<div class="mt-4 flex items-center justify-end gap-1 sm:gap-2 flex-wrap">
	<?php
	$jumlah_number = 2;
	$start_number = ($hlm > $jumlah_number) ? $hlm - $jumlah_number : 1;
	$end_number = ($hlm < ($jumlah_page - $jumlah_number)) ? $hlm + $jumlah_number : $jumlah_page;

	if ($hlm == 1) {
		echo '<span class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed text-sm"><i class="bi bi-chevron-double-left"></i></span>';
		echo '<span class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed text-sm"><i class="bi bi-chevron-left"></i></span>';
	} else {
		$link_prev = $hlm - 1;
		echo '<span class="halaman px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors text-sm cursor-pointer" id="1"><i class="bi bi-chevron-double-left"></i></span>';
		echo '<span class="halaman px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors text-sm cursor-pointer" id="' . $link_prev . '"><i class="bi bi-chevron-left"></i></span>';
	}

	if ($start_number > 1) {
		echo '<span class="halaman px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors text-sm cursor-pointer" id="1">1</span>';
		if ($start_number > 2) echo '<span class="px-2 text-gray-400">...</span>';
	}

	for ($i = $start_number; $i <= $end_number; $i++) {
		if ($hlm == $i) {
			echo '<span class="px-3 py-2 rounded-lg bg-blue-500 text-white font-semibold text-sm">' . $i . '</span>';
		} else {
			echo '<span class="halaman px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors text-sm cursor-pointer" id="' . $i . '">' . $i . '</span>';
		}
	}

	if ($end_number < $jumlah_page) {
		if ($end_number < $jumlah_page - 1) echo '<span class="px-2 text-gray-400">...</span>';
		echo '<span class="halaman px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors text-sm cursor-pointer" id="' . $jumlah_page . '">' . $jumlah_page . '</span>';
	}

	switch ($hlm) {
		case $jumlah_page:
			echo '<span class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed text-sm"><i class="bi bi-chevron-right"></i></span>';
			echo '<span class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed text-sm"><i class="bi bi-chevron-double-right"></i></span>';
			break;
		default:
			$link_next = $hlm + 1;
			echo '<span class="halaman px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors text-sm cursor-pointer" id="' . $link_next . '"><i class="bi bi-chevron-right"></i></span>';
			echo '<span class="halaman px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors text-sm cursor-pointer" id="' . $jumlah_page . '"><i class="bi bi-chevron-double-right"></i></span>';
			break;
	}
	?>
</div>
<?php endif; ?>

<div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
	<i class="bi bi-info-circle mr-1"></i>
	Total: <span class="font-semibold"><?= $total_records; ?></span> artikel
</div>
