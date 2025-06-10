<div class="container">
	<h2 class="mb-4">Hasil Clustering K-Means</h2>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Pasien</th>
				<th>Jenis Penyakit</th>
				<th>Kelompok Usia</th>
				<th>Jenis Kelamin</th>
				<th>Cluster</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($datasets as $data)
				<tr>
					<td>{{ $data->pasien }}</td>
					<td>{{ $data->jenis_penyakit }}</td>
					<td>{{ $data->kelompok_usia }}</td>
					<td>{{ $data->jenis_kelamin }}</td>
					<td><span class="badge bg-primary">Cluster {{ $data->cluster }}</span></td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
