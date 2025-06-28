@extends(backpack_view('blank'))

@section('content')
	<div class="card">
		<div class="container mt-4">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h1 class="mb-0">Detail Import</h1>
				<a href="{{ route('dataset.index') }}" class="btn btn-outline-danger">Tutup</a>
			</div>

			@if (session('success'))
				<div class="alert alert-success">
					{{ session('success') }}
				</div>
			@endif

			@if ($countPenyakitTanpaKasus > 0)
				<div class="alert alert-info">
					Jumlah penyakit tanpa kasus: {{ $countPenyakitTanpaKasus }}
				</div>
			@endif

			{{-- Tabel Penyakit Tanpa Kasus --}}
			<div class="mb-4">
				<h3>Penyakit Tanpa Kasus</h3>
				<table id="penyakit_tanpakasus" class="display">
					<thead>
						<tr>
							<th>No</th>
							<th>Nama Penyakit Tanpa Kasus</th>
						</tr>
					</thead>
					<tbody>
						@foreach (session('penyakit_tanpa_kasus', []) as $index => $penyakit)
							<tr>
								<td>{{ $index + 1 }}</td>
								<td>{{ $penyakit }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			{{-- Tabel Hasil Import --}}
			<div class="mb-4">
				<h3>Hasil Import Data</h3>
				<table id="hasil_import" class="display">
					<thead>
						<tr>
							<th>No</th>
							<th>Nama Pasien</th>
							<th>Jenis Penyakit</th>
							<th>Kelompok Usia</th>
							<th>Jenis Kelamin</th>
						</tr>
					</thead>
					<tbody>
						@foreach (session('import_results', []) as $index => $result)
							<tr>
								<td>{{ $index + 1 }}</td>
								<td>{{ $result->pasien }}</td>
								<td>{{ $result->jenis_penyakit }}</td>
								<td>{{ $result->kelompok_usia }}</td>
								<td>{{ $result->jenis_kelamin }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection

@push('after_styles')
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
	<style>
		/* CSS Kustom untuk DataTable */
		body {
			font-family: 'Arial', sans-serif;
			background-color: #f8f9fa;
		}

		.container {
			margin-top: 20px;
		}

		h1 {
			color: #333;
			margin-bottom: 20px;
			font-size: 24px;
		}

		table.display {
			width: 100%;
			border-collapse: collapse;
			margin-top: 10px;
			background-color: white;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}

		table.display thead {
			background-color: #007bff;
			color: white;
		}

		table.display th,
		table.display td {
			padding: 12px 15px;
			border: 1px solid #ddd;
			text-align: left;
		}

		table.display tr:nth-child(even) {
			background-color: #f2f2f2;
		}

		table.display tr:hover {
			background-color: #f1f1f1;
		}

		.alert-success,
		.alert-info {
			margin-top: 20px;
			padding: 15px;
			border-radius: 4px;
		}
	</style>
@endpush

@push('after_scripts')
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#penyakit_tanpakasus').DataTable({
				searching: true,
				ordering: true,
				lengthChange: false,
				pageLength: 10,
				language: {
					info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
					infoEmpty: "Menampilkan 0 hingga 0 dari 0 entri",
					emptyTable: "Tidak ada data tersedia di tabel",
					lengthMenu: "Tampilkan _MENU_ entri",
					search: "Pencarian:",
					paginate: {
						first: "Pertama",
						last: "Terakhir",
						next: "Berikutnya",
						previous: "Sebelumnya"
					}
				}
			});

			$('#hasil_import').DataTable({
				searching: true,
				ordering: true,
				lengthChange: false,
				pageLength: 10,
				language: {
					info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
					infoEmpty: "Menampilkan 0 hingga 0 dari 0 entri",
					emptyTable: "Tidak ada data tersedia di tabel",
					lengthMenu: "Tampilkan _MENU_ entri",
					search: "Pencarian:",
					paginate: {
						first: "Pertama",
						last: "Terakhir",
						next: "Berikutnya",
						previous: "Sebelumnya"
					}
				}
			});
		});
	</script>
@endpush
