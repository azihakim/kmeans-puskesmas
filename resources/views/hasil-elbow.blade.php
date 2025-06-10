<div class="container">
	<h2>Evaluasi Elbow Method (Jarak Centroid - WCSS)</h2>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Jumlah Cluster (K)</th>
				<th>Jarak Centroid (WCSS)</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($evaluasi as $row)
				<tr>
					<td>{{ $row['k'] }}</td>
					<td>{{ $row['wcss'] }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
