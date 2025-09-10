@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Riwayat Tes User</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table-bordered table-hover dataTables-example table text-center">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama User</th>
                                    <th>Tanggal Tes</th>
                                    <th>Skor Benar</th>
                                    <th>Skor Salah</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hasilTes as $index => $tes)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $tes->user_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tes->tanggal_tes)->isoFormat('dddd, D MMMM Y') }}</td>
                                        <td>{{ $tes->jumlah_benar }}</td>
                                        <td>{{ 9 - $tes->jumlah_benar }}</td>
                                        <td>
                                            <a href="{{ route('kecermatan.detail', $tes->id) }}"
                                                class="btn btn-primary btn-sm">
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data tes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';

        $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: 'tp<"bottom"l>',
                searching: true,
                buttons: [],
                language: {
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        });
    </script>
@endpush
