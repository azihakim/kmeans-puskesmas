@extends(backpack_view('blank')) {{-- Atau layout Backpack v6: backpack_view('theme-tabler::layouts.top_left') --}}

@section('content')
	<div class="row mb-3">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">Import Data dari Excel</div>
				<div class="card-body">
					<form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="mb-3">
							<label for="excel_file" class="form-label">Pilih File Excel (.xlsx, .xls)</label>
							<input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
						</div>
						<button type="submit" class="btn btn-primary">Import</button>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('after_styles')
@endpush

@push('after_scripts')
	\
@endpush
