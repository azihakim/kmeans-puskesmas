@extends(backpack_view('blank'))

@section('content')
	<div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
		<div class="row mb-3 w-100" style="max-width: 500px;">
			<div class="col-12">
				<div class="card">
					<div class="card-header">Import Data dari Excel</div>
					<div class="card-body">
						<form id="importForm" action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
							@csrf
							<div class="mb-3">
								<label for="excel_file" class="form-label">Pilih File Excel (.xlsx, .xls)</label>
								<input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
							</div>
							<div class="d-flex justify-content-end">
								<button type="submit" class="btn btn-outline-primary">Import</button>
							</div>
						</form>
						<!-- Modal Loading -->
						<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered">
								<div class="modal-content text-center p-4">
									<div class="spinner-border text-primary mb-3" role="status"></div>
									<div>Memproses data, mohon tunggu...</div>
								</div>
							</div>
						</div>
						<!-- End Modal -->
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('after_scripts')
	<script>
		document.getElementById('importForm').addEventListener('submit', function() {
			// Tampilkan modal loading
			var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
			loadingModal.show();
		});
	</script>
@endpush
