<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

ini_set('display_errors', 0);

if (!isset($_SESSION['username'])) {
	header("location:login.php");
}

require_once 'db-conn.php';

$currentUserRole = $_SESSION['role'] ?? 'user';
function canManageUser($currentRole, $targetRole) {
	if ($currentRole === 'superadmin') {
		return true;
	}
	if ($currentRole === 'admin' && $targetRole === 'user') {
		return true;
	}
	return false;
}

function getRoleBadge($role, $isCurrentUser = false) {
	$badges = [
		'superadmin' => [
			'bg' => 'bg-purple-100 dark:bg-purple-900/30',
			'text' => 'text-purple-700 dark:text-purple-400',
			'icon' => 'bi-shield-fill-check',
			'label' => 'Superadmin'
		],
		'admin' => [
			'bg' => 'bg-blue-100 dark:bg-blue-900/30',
			'text' => 'text-blue-700 dark:text-blue-400',
			'icon' => 'bi-shield-fill',
			'label' => 'Admin'
		],
		'user' => [
			'bg' => 'bg-gray-100 dark:bg-gray-700',
			'text' => 'text-gray-600 dark:text-gray-400',
			'icon' => 'bi-person-fill',
			'label' => 'User'
		]
	];

	$badge = $badges[$role] ?? $badges['user'];
	$onlineIndicator = $isCurrentUser ? ' <span class="text-green-500 ml-1">(Anda)</span>' : '';

	return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold ' . $badge['bg'] . ' ' . $badge['text'] . '">
		<i class="bi ' . $badge['icon'] . '"></i> ' . $badge['label'] . $onlineIndicator . '
	</span>';
}

$perPage = 5;
$hlm = isset($_POST['hlm']) ? (int)$_POST['hlm'] : 1;
if ($hlm < 1) $hlm = 1;

$offset = ($hlm - 1) * $perPage;

$countStmt = $pdo->query('SELECT COUNT(*) FROM user');
$total_records = (int)$countStmt->fetchColumn();
$jumlah_page = (int)ceil($total_records / $perPage);

$sql = "SELECT * FROM user ORDER BY id ASC LIMIT :limit OFFSET :offset";
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
			<th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Foto</th>
			<th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Username</th>
			<th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Role</th>
			<th class="px-4 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-200">Aksi</th>
		</tr>
	</thead>
	<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
		<?php
		$no = $offset + 1;
		if ($stmt->rowCount() > 0) {
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$isCurrentUser = ($row['username'] === $_SESSION['username']);
				$targetRole = $row['role'] ?? 'user';
				$canManage = canManageUser($currentUserRole, $targetRole);
			?>
			<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
				<td class="px-4 py-4 text-gray-600 dark:text-gray-300"><?= $no++ ?></td>
				<td class="px-4 py-4">
					<?php if (!empty($row['foto'])): ?>
						<img src="img/<?= htmlspecialchars($row['foto']) ?>" class="w-16 h-16 object-cover rounded-full border-2 border-gray-200 dark:border-gray-600 shadow-sm" alt="<?= htmlspecialchars($row['username']) ?>">
					<?php else: ?>
						<div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-sm">
							<span class="text-white text-xl font-bold"><?= strtoupper(substr($row['username'], 0, 1)) ?></span>
						</div>
					<?php endif; ?>
				</td>
				<td class="px-4 py-4">
					<div class="font-semibold text-gray-800 dark:text-gray-100">
						<?= htmlspecialchars($row['username']) ?>
						<?php if ($isCurrentUser): ?>
							<span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
								<i class="bi bi-check-circle-fill"></i> Online
							</span>
						<?php endif; ?>
					</div>
					<div class="text-sm text-gray-500 dark:text-gray-400">ID: <?= $row['id'] ?></div>
				</td>
				<td class="px-4 py-4">
					<?= getRoleBadge($targetRole, false) ?>
				</td>
				<td class="px-4 py-4">
					<div class="flex items-center justify-center gap-2">
						<?php if ($isCurrentUser || $canManage): ?>
						<button onclick="openEditModal(<?= (int)$row['id'] ?>, '<?= htmlspecialchars(addslashes($row['username']), ENT_QUOTES) ?>', '<?= htmlspecialchars($row['foto'] ?? '') ?>', '<?= $targetRole ?>')"
							class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-lg transition-colors cursor-pointer" title="Edit">
							<i class="bi bi-pencil-square"></i>
						</button>
						<?php else: ?>
						<button disabled
							class="bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 p-2 rounded-lg cursor-not-allowed" title="Tidak memiliki izin">
							<i class="bi bi-pencil-square"></i>
						</button>
						<?php endif; ?>

						<?php if ($isCurrentUser): ?>
						<button disabled
							class="bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 p-2 rounded-lg cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
							<i class="bi bi-trash"></i>
						</button>
						<?php elseif ($canManage): ?>
						<button onclick="openDeleteModal(<?= (int)$row['id'] ?>, '<?= htmlspecialchars(addslashes($row['username']), ENT_QUOTES) ?>')"
							class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition-colors cursor-pointer" title="Hapus">
							<i class="bi bi-trash"></i>
						</button>
						<?php else: ?>
						<button disabled
							class="bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 p-2 rounded-lg cursor-not-allowed" title="Tidak memiliki izin">
							<i class="bi bi-trash"></i>
						</button>
						<?php endif; ?>
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
						<i class="bi bi-people text-5xl mb-3"></i>
						<p class="text-lg font-medium">Belum ada user</p>
						<p class="text-sm">Klik tombol "Tambah User" untuk menambah pengguna baru</p>
					</div>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
</div>

<!-- Pagination Info -->
<?php if ($total_records > 0): ?>
<div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
	<div class="text-sm text-gray-500 dark:text-gray-400">
		Menampilkan <?= $offset + 1 ?> - <?= min($offset + $perPage, $total_records) ?> dari <?= $total_records ?> user
	</div>
	<?php if ($jumlah_page > 1): ?>
	<div class="flex items-center justify-end gap-1 sm:gap-2 flex-wrap">
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
</div>
<?php endif; ?>
