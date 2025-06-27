@extends(backpack_view('blank'))

@php
	if (backpack_theme_config('show_getting_started')) {
	    $widgets['before_content'][] = [
	        'type' => 'view',
	        'view' => backpack_view('inc.getting_started'),
	    ];
	} else {
	    $widgets['before_content'][] = [
	        'type' => 'jumbotron',
	        'heading' => '<h1 class="fw-bold mb-4">SISTEM CLUSTERING PENYAKIT </h1>',
	        'heading_class' =>
	            'display-4 ' . (backpack_theme_config('layout') === 'horizontal_overlap' ? ' text-white' : ''),
	        'content' =>
	            '
                 <div class="row align-items-center">
                    <div class="col-12 text-center mb-6">
                        <img src="' .
	            asset('img/puskesmas.png') .
	            '" width="10%" alt="PT Haleyora Powerindo Logo" class="img-fluid" />
                    <h1>PUSKESMAS BUKIT SANGKAL</h1>
                    </div>
                    <div class="col-12">
                        <div class="row justify-content-center">
                            <div class="col-md-6 mb-4 mb-md-0 text-center">
                                <h2 class="fw-bold">VISI</h2>
                                <p class="mb-0">"Puskesmas yang berorientasi pada kebutuhan masyarakat menuju Kelurahan Bukit Sangkal sehat dan mandiri tahun 2026"</p>
                            </div>
                            <div class="col-md-6 text-center">
                                <h2 class="fw-bold">MISI</h2>
                                <ol class="mb-0 text-start d-inline-block" style="max-width: 400px;">
                                    <li>Memberi pelayanan secara optimal</li>
                                    <li>Meningkatkan kualitas sumber daya manusia</li>
                                    <li>Mendorong kemandirian masyarakat untuk hidup sehat</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>',
	        'content_class' => backpack_theme_config('layout') === 'horizontal_overlap' ? 'text-white' : '',
	    ];
	}
@endphp
