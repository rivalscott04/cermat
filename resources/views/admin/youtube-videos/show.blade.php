@extends('layouts.app')

@section('title', 'Detail YouTube Video')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Detail YouTube Video</h5>
                <div class="ibox-tools">
                    <a href="{{ route('admin.youtube-videos.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="150">ID</th>
                                <td>{{ $youtubeVideo->id }}</td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td>{{ $youtubeVideo->title ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>YouTube URL</th>
                                <td>
                                    <a href="{{ $youtubeVideo->youtube_url }}" target="_blank" class="text-primary">
                                        {{ $youtubeVideo->youtube_url }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>YouTube ID</th>
                                <td><code>{{ $youtubeVideo->youtube_id }}</code></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($youtubeVideo->is_active)
                                        <span class="label label-primary">Active</span>
                                    @else
                                        <span class="label label-default">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $youtubeVideo->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $youtubeVideo->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $youtubeVideo->updated_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        @if($youtubeVideo->youtube_id)
                        <h4>Preview Video</h4>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" 
                                    src="{{ $youtubeVideo->embed_url }}" 
                                    frameborder="0" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <a href="{{ route('admin.youtube-videos.edit', $youtubeVideo) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.youtube-videos.destroy', $youtubeVideo) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus video ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
