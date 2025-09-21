@extends('layouts.app')

@section('title', 'YouTube Videos')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>YouTube Videos</h5>
                <div class="ibox-tools">
                    <a href="{{ route('admin.youtube-videos.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Tambah Video
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        {{ session('success') }}
                    </div>
                @endif

                @if($videos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>YouTube URL</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($videos as $video)
                                <tr>
                                    <td>{{ $video->id }}</td>
                                    <td>{{ $video->title ?? '-' }}</td>
                                    <td>
                                        <a href="{{ $video->youtube_url }}" target="_blank" class="text-primary">
                                            {{ Str::limit($video->youtube_url, 50) }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($video->is_active)
                                            <span class="label label-primary">Active</span>
                                        @else
                                            <span class="label label-default">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $video->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.youtube-videos.show', $video) }}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.youtube-videos.edit', $video) }}" class="btn btn-warning btn-xs">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.youtube-videos.destroy', $video) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus video ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center">
                        <p>Belum ada video YouTube yang ditambahkan.</p>
                        <a href="{{ route('admin.youtube-videos.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Video Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
