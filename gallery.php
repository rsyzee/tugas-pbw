<?php

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';
$messageType = '';
$redirect = '';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

ini_set('display_errors', 0);

if (!isset($_SESSION['username'])) {
	header("location:login.php");
}

include 'upload-handler.php';

if (!isset($uploadFsDir) || !is_string($uploadFsDir) || $uploadFsDir === '') {
	$uploadFsDir = __DIR__ . '/img';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_gallery'])) {
	$judul = trim($_POST['judul']);
	$deskripsi = trim($_POST['deskripsi']);
	$username = $_SESSION['username'];
	$gambar = '';

	$nama_gambar = $_FILES['gambar']['name'] ?? '';
	if ($nama_gambar !== '') {
		$cek_upload = upload_foto($_FILES['gambar']);
		if ($cek_upload['status']) {
			$gambar = $cek_upload['message'];
		} else {
			$message = $cek_upload['message'];
			$messageType = 'error';
			$action = 'add';
		}
	} else {
		$message = 'Gambar wajib diupload!';
		$messageType = 'error';
		$action = 'add';
	}

	if (!empty($gambar) && $messageType !== 'error') {
		$sql = "INSERT INTO gallery (judul, deskripsi, gambar, tanggal, username) VALUES (:judul, :deskripsi, :gambar, NOW(), :username)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([
			':judul' => $judul,
			':deskripsi' => $deskripsi,
			':gambar' => $gambar,
			':username' => $username
		]);
		$redirect = 'admin.php?page=gallery&msg=added';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_gallery'])) {
	$id = $_POST['id'];
	$judul = trim($_POST['judul']);
	$deskripsi = trim($_POST['deskripsi']);
	$gambar_lama = $_POST['gambar_lama'] ?? '';

	$gambar = $gambar_lama;
	if ($gambar === '') {
		$stmt = $pdo->prepare("SELECT gambar FROM gallery WHERE id = :id");
		$stmt->execute([':id' => $id]);
		$current = $stmt->fetch();
		$gambar = $current['gambar'] ?? '';
	}

	$nama_gambar = $_FILES['gambar']['name'] ?? '';
	if ($nama_gambar !== '') {
		$cek_upload = upload_foto($_FILES['gambar']);
		if ($cek_upload['status']) {
			if (!empty($gambar)) {
				$oldPath = $uploadFsDir . '/' . $gambar;
				if (is_file($oldPath)) {
					@unlink($oldPath);
				}
			}
			$gambar = $cek_upload['message'];
		} else {
			$message = $cek_upload['message'];
			$messageType = 'error';
			$action = 'edit';
		}
	}

	if (!empty($gambar) && $messageType !== 'error') {
		$sql = "UPDATE gallery SET judul = :judul, deskripsi = :deskripsi, gambar = :gambar WHERE id = :id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([
			':judul' => $judul,
			':deskripsi' => $deskripsi,
			':gambar' => $gambar,
			':id' => $id
		]);
		$redirect = 'admin.php?page=gallery&msg=updated';
	} else {
		if ($messageType !== 'error') {
			$message = 'Gambar tidak boleh kosong!';
			$messageType = 'error';
		}
		$action = 'edit';
	}
}

if ($action === 'delete' && isset($_GET['id'])) {
	$id = $_GET['id'];

	$stmt = $pdo->prepare("SELECT gambar FROM gallery WHERE id = :id");
	$stmt->execute([':id' => $id]);
	$gallery = $stmt->fetch();

	if ($gallery) {
		if (!empty($gallery['gambar'])) {
			$imagePath = $uploadFsDir . '/' . $gallery['gambar'];
			if (is_file($imagePath)) {
				@unlink($imagePath);
			}
		}

		$stmt = $pdo->prepare("DELETE FROM gallery WHERE id = :id");
		$stmt->execute([':id' => $id]);
	}

	$redirect = 'admin.php?page=gallery&msg=deleted';
}

if (isset($_GET['msg'])) {
	switch ($_GET['msg']) {
		case 'added':
			$message = 'Gallery berhasil ditambahkan!';
			$messageType = 'success';
			break;
		case 'updated':
			$message = 'Gallery berhasil diperbarui!';
			$messageType = 'success';
			break;
		case 'deleted':
			$message = 'Gallery berhasil dihapus!';
			$messageType = 'success';
			break;
	}
}

if (!empty($redirect)) {
	echo '<script>window.location.href = "' . $redirect . '";</script>';
	exit();
}
?>

<?php
$editGallery = null;
if ($action === 'edit' && isset($_GET['id'])) {
	$id = $_GET['id'];
	$stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = :id");
	$stmt->execute([':id' => $id]);
	$editGallery = $stmt->fetch();
}
?>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 md:p-8">
	<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
		<div>
			<h1 class="text-xl sm:text-2xl md:text-3xl font-bold mb-2">
				<i class="bi bi-images mr-2"></i>Manage Gallery
			</h1>
			<p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">Kelola semua gambar gallery yang ditampilkan di homepage</p>
		</div>
		<div class="mt-2 md:mt-0">
			<button onclick="openAddModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 sm:py-3 sm:px-6 rounded-lg transition-colors inline-flex items-center gap-2 text-sm sm:text-base cursor-pointer">
				<i class="bi bi-plus-circle"></i> Tambah Gallery
			</button>
		</div>
	</div>

	<?php if ($message && $messageType === 'success'): ?>
	<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
		<p class="text-green-600 dark:text-green-400">
			<i class="bi bi-check-circle mr-2"></i><?= $message ?>
		</p>
	</div>
	<?php endif; ?>
	<div class="table-responsive" id="gallery_data"></div>
</div>

<!-- Add Gallery Modal -->
<div id="addModal" class="fixed inset-0 z-50 <?= $action === 'add' ? '' : 'hidden' ?>" role="dialog" aria-modal="true">
	<!-- Backdrop -->
	<div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAddModal()"></div>

	<!-- Modal Content -->
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<div class="flex min-h-full items-center justify-center p-4">
			<div class="relative w-full max-w-2xl transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-200 dark:border-gray-700">
				<!-- Modal Header -->
				<div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
							<i class="bi bi-plus-circle mr-2 text-blue-500"></i>Tambah Gallery
						</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload gambar baru ke gallery</p>
					</div>
					<button type="button" onclick="closeAddModal()" class="rounded-lg p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
						<i class="bi bi-x-lg text-xl"></i>
					</button>
				</div>

				<!-- Modal Body -->
				<form method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
					<?php if ($message && $messageType === 'error' && $action === 'add'): ?>
					<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
						<p class="text-red-600 dark:text-red-400">
							<i class="bi bi-exclamation-circle mr-2"></i><?= htmlspecialchars($message) ?>
						</p>
					</div>
					<?php endif; ?>

					<div class="space-y-5">
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judul Gallery</label>
							<input type="text" name="judul"
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base"
								   placeholder="Masukkan judul gallery (opsional)...">
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
							<textarea name="deskripsi" rows="3"
									  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base"
									  placeholder="Tulis deskripsi gambar (opsional)..."></textarea>
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gambar <span class="text-red-500">*</span></label>
							<input type="file" name="gambar" accept="image/*" required
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
							<p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Format: JPG, JPEG, PNG, GIF, WEBP (Max: 800KB)</p>
						</div>
					</div>

					<!-- Modal Footer -->
					<div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
						<button type="button" onclick="closeAddModal()"
								class="w-full sm:w-auto px-6 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors inline-flex items-center justify-center gap-2">
							<i class="bi bi-x-circle"></i> Batal
						</button>
						<button type="submit" name="add_gallery"
								class="w-full sm:w-auto px-6 py-3 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold transition-colors inline-flex items-center justify-center gap-2">
							<i class="bi bi-check-circle"></i> Simpan Gallery
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Edit Gallery Modal -->
<div id="editModal" class="fixed inset-0 z-50 <?= ($action === 'edit' && $editGallery) ? '' : 'hidden' ?>" role="dialog" aria-modal="true">
	<!-- Backdrop -->
	<div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>

	<!-- Modal Content -->
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<div class="flex min-h-full items-center justify-center p-4">
			<div class="relative w-full max-w-2xl transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-200 dark:border-gray-700">
				<!-- Modal Header -->
				<div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
							<i class="bi bi-pencil-square mr-2 text-yellow-500"></i>Edit Gallery
						</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perbarui data gallery</p>
					</div>
					<button type="button" onclick="closeEditModal()" class="rounded-lg p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
						<i class="bi bi-x-lg text-xl"></i>
					</button>
				</div>

				<!-- Modal Body -->
				<form method="POST" enctype="multipart/form-data" class="p-4 sm:p-6" id="editForm">
					<?php if ($message && $messageType === 'error' && $action === 'edit'): ?>
					<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
						<p class="text-red-600 dark:text-red-400">
							<i class="bi bi-exclamation-circle mr-2"></i><?= htmlspecialchars($message) ?>
						</p>
					</div>
					<?php endif; ?>

					<input type="hidden" name="id" id="edit_id" value="<?= $editGallery ? $editGallery['id'] : '' ?>">
					<input type="hidden" name="gambar_lama" id="edit_gambar_lama" value="<?= $editGallery ? htmlspecialchars($editGallery['gambar'] ?? '') : '' ?>">

					<div class="space-y-5">
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judul Gallery</label>
							<input type="text" name="judul" id="edit_judul"
								   value="<?= $editGallery ? htmlspecialchars($editGallery['judul']) : '' ?>"
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base"
								   placeholder="Masukkan judul gallery (opsional)...">
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
							<textarea name="deskripsi" id="edit_deskripsi" rows="3"
									  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base"
									  placeholder="Tulis deskripsi gambar (opsional)..."><?= $editGallery ? htmlspecialchars($editGallery['deskripsi']) : '' ?></textarea>
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gambar</label>
							<div id="currentImageContainer" class="mb-4 <?= ($editGallery && !empty($editGallery['gambar'])) ? '' : 'hidden' ?>">
								<p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Gambar saat ini:</p>
								<img id="currentImage" src="<?= $editGallery && !empty($editGallery['gambar']) ? 'img/' . htmlspecialchars($editGallery['gambar']) : '' ?>"
									 class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
							</div>
							<input type="file" name="gambar" accept="image/*"
								   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
							<p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak ingin mengubah gambar. Format: JPG, JPEG, PNG, GIF, WEBP (Max: 800KB)</p>
						</div>
					</div>

					<!-- Modal Footer -->
					<div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
						<button type="button" onclick="closeEditModal()"
								class="w-full sm:w-auto px-6 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors inline-flex items-center justify-center gap-2">
							<i class="bi bi-x-circle"></i> Batal
						</button>
						<button type="submit" name="edit_gallery"
								class="w-full sm:w-auto px-6 py-3 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white font-semibold transition-colors inline-flex items-center justify-center gap-2">
							<i class="bi bi-check-circle"></i> Update Gallery
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
	<!-- Backdrop -->
	<div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>

	<!-- Modal Content -->
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<div class="flex min-h-full items-center justify-center p-4">
			<div class="relative w-full max-w-md transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-200 dark:border-gray-700">
				<!-- Modal Header -->
				<div class="p-6 text-center">
					<div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
						<i class="bi bi-exclamation-triangle text-3xl text-red-600 dark:text-red-400"></i>
					</div>
					<h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hapus Gallery</h3>
					<p class="text-gray-500 dark:text-gray-400 text-sm">
						Apakah Anda yakin ingin menghapus gallery:<br>
						<span id="delete_gallery_title" class="font-semibold text-gray-700 dark:text-gray-300"></span>
					</p>
					<p class="text-red-500 dark:text-red-400 text-xs mt-2">
						<i class="bi bi-info-circle mr-1"></i>Tindakan ini tidak dapat dibatalkan!
					</p>
				</div>

				<!-- Modal Footer -->
				<div class="flex gap-3 p-6 pt-0">
					<button type="button" onclick="closeDeleteModal()"
							class="flex-1 px-6 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors inline-flex items-center justify-center gap-2">
						<i class="bi bi-x-circle"></i> Batal
					</button>
					<a id="delete_confirm_link" href="#"
					   class="flex-1 px-6 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold transition-colors inline-flex items-center justify-center gap-2">
						<i class="bi bi-trash"></i> Hapus
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function openAddModal() {
	document.getElementById('addModal').classList.remove('hidden');
	document.body.style.overflow = 'hidden';
}

function closeAddModal() {
	document.getElementById('addModal').classList.add('hidden');
	document.body.style.overflow = '';
	window.history.replaceState({}, '', 'admin.php?page=gallery');
}

function openEditModal(id, judul, deskripsi, gambar) {
	document.getElementById('edit_id').value = id;
	document.getElementById('edit_judul').value = judul;
	document.getElementById('edit_deskripsi').value = deskripsi;
	document.getElementById('edit_gambar_lama').value = gambar || '';

	const imageContainer = document.getElementById('currentImageContainer');
	const imageElement = document.getElementById('currentImage');

	if (gambar) {
		imageElement.src = <?= json_encode('img/') ?> + gambar;
		imageContainer.classList.remove('hidden');
	} else {
		imageContainer.classList.add('hidden');
	}

	document.getElementById('editModal').classList.remove('hidden');
	document.body.style.overflow = 'hidden';
}

function closeEditModal() {
	document.getElementById('editModal').classList.add('hidden');
	document.body.style.overflow = '';
	window.history.replaceState({}, '', 'admin.php?page=gallery');
}

document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape') {
		closeAddModal();
		closeEditModal();
		closeDeleteModal();
	}
});

function openDeleteModal(id, title) {
	document.getElementById('delete_gallery_title').textContent = title ? '"' + title + '"' : '(Tanpa Judul)';
	document.getElementById('delete_confirm_link').href = 'admin.php?page=gallery&action=delete&id=' + id;
	document.getElementById('deleteModal').classList.remove('hidden');
	document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
	document.getElementById('deleteModal').classList.add('hidden');
	document.body.style.overflow = '';
}
</script>

<script>
$(document).ready(function () {
	window.load_data = function (hlm) {
		if (typeof hlm === 'undefined' || hlm === null || hlm === '') hlm = 1;
		hlm = parseInt(hlm, 10);
		if (isNaN(hlm) || hlm < 1) hlm = 1;

		$.ajax({
			url: "gallery_data.php",
			method: "POST",
			data: { hlm: hlm },
			success: function (data) {
				$('#gallery_data').html(data);
			},
			error: function (xhr) {
				$('#gallery_data').html(
					'<div class="alert alert-danger">Gagal memuat data (HTTP ' +
					(xhr && xhr.status ? xhr.status : 'error') + '). Silakan refresh atau login ulang.</div>'
				);
			}
		});
	};

	load_data(1);

	$(document).on('click', '.halaman', function (e) {
		e.preventDefault();
		var page = parseInt($(this).attr('id'), 10);
		if (!isNaN(page) && page > 0) {
			load_data(page);
		}
	});
});
</script>
