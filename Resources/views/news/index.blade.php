@extends('app')
@section('title', __('DB::common.news'))

@section('content')
  <div class="row row-cols-2">
    @foreach($allnews as $news)
      <div class="col">
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1 p-0">
              {{ $news->subject }}
              <i class="fas fa-book-open float-end m-1"></i>
            </h5>
          </div>
          <div class="card-body p-1 text-start">
            {!! $news->body !!}
          </div>
          <div class="card-footer p-1 text-end small">
            <span class="float-start">{{ optional($news->user)->name_private }}</span>
            {{ $news->created_at->format('d.M.Y H:i') }}
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{ $allnews->links('pagination.auto') }}
@endsection
