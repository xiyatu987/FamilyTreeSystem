@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">添加新字辈</h1>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">字辈信息</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('ziwei.store') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="character" class="form-label">字辈</label>
                    <input type="text" class="form-control @error('character') is-invalid @enderror" id="character" name="character" value="{{ old('character') }}" required>
                    @error('character')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="order" class="form-label">顺序</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order') }}" required>
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="generation" class="form-label">世代</label>
                    <input type="number" class="form-control @error('generation') is-invalid @enderror" id="generation" name="generation" value="{{ old('generation') }}">
                    @error('generation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">描述</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('ziwei.index') }}" class="btn btn-secondary">返回</a>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
